<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\WMS_Transfer;
use App\WMS_Transfer_Detail;
use App\WMS_Inventory;

use DateTime;

class WMS_TransferController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $_data;

    public function __construct(Request $request)
    {
        $this->_data['type'] = (isset($request->type) && $request->type !='') ? $request->type : 'default';
        $this->_data['siteconfig'] = config('siteconfig.wms.transfer');
        $this->_data['default_language'] = config('siteconfig.general.language');
        $this->_data['languages'] = config('siteconfig.languages');
        $this->_data['pageTitle'] = $this->_data['siteconfig'][$this->_data['type']]['page-title'];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(){
        $this->_data['items'] = DB::table('wms_transfers as A')
            ->leftjoin('users as B', 'A.user_id','=','B.id')
            ->select('A.*','B.name as username')
            ->where('A.type',$this->_data['type'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->paginate(25);

        $this->_data['total'] = DB::table('wms_transfers')
            ->select(DB::raw('sum(transfer_qty) as qty, sum(transfer_price) as price'))
            ->whereRaw('FIND_IN_SET(\'publish\',status)')
            ->where('type',$this->_data['type'])->first();

        return view('admin.wms.transfers.index',$this->_data);
    }

    public function ajax(Request $request){
        if($request->ajax()){
            $ids = WMS_Inventory::select(DB::raw('max(id) as id'))->groupBy(['store_code','product_id','unit'])->get()->toArray();
            $inventories = WMS_Inventory::whereIn('id',$ids)->where('store_code',$request->store)->whereRaw("(product_code LIKE '%".strtoupper($request->q)."%' OR product_title LIKE '%".$request->q."%')")->orderBy('id','desc')->get();
            $products = [];
            foreach ($inventories as $key => $inventory) {
                $products[$key]['id']              =  $inventory->id;
                $products[$key]['product_id']      =  $inventory->product_id;
                $products[$key]['product_code']    =  $inventory->product_code;
                $products[$key]['product_price']   =  floatval(@$inventory->product_price);
                $products[$key]['product_qty']     =  1;
                $products[$key]['unit']            =  $inventory->unit;
                $products[$key]['inventory']       =  $inventory->inventory;
                $products[$key]['title']           =  $inventory->product_title . ' ( '.($inventory->unit === 1 ? 'Bộ/Cái' : ($inventory->unit === 2 ? 'Dàn nóng' : 'Dàn lạnh')).' )';
            }
            return response()->json(['items'=>$products]);
        }
    }
    
    public function create(){
        $this->_data['warehouses'] = $this->getWarehouses();
        return view('admin.wms.transfers.create',$this->_data);
    }

    public function store(Request $request){
        $valid = Validator::make($request->all(), [
            'products'          => 'required',
            'data.store_from'   => 'required',
            'data.store_to'     => 'required',
            ], [
            'products.required' => 'Vui lòng chọn sản phẩm',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $wms_transfer  = new WMS_Transfer;

            if($request->data){
                foreach($request->data as $field => $value){
                    $wms_transfer->$field = $value;
                }
            }
            $inputProduct = $request->products;
            $products = [];
            $product  = [];
            $sumPrice = 0;
            $sumQty   = 0;
            $dataInsert = [];
            foreach($inputProduct as $key => $value){
                $id    = (int)$value['id'];
                $unit  = (int)$value['unit'];
                if( !isset($products[$id][$unit]) ){
                    $products[$id][$unit]['title']  =  $value['title'];
                    $products[$id][$unit]['code']   =  strtoupper($value['code']);
                    $products[$id][$unit]['price']  =  (int)$value['price'];
                    @$products[$id][$unit]['qty']   += (int)$value['qty'];
                }else{
                    @$products[$id][$unit]['qty']  +=  (int)$value['qty'];
                    unset($inputProduct[$key]);
                }
            }
            array_values($inputProduct);

            foreach($inputProduct as $key => $value){
                $id    = (int)$value['id'];
                $code  = $value['code'];
                $unit  = (int)$value['unit'];
                $product = [];
                if( isset($products[$id][$unit]) ){
                    $product['product_id']    =   $id;
                    $product['product_code']  =   $products[$id][$unit]['code'];
                    $product['product_title'] =   $products[$id][$unit]['title'];
                    $product['product_qty']   =   $products[$id][$unit]['qty'];
                    $product['product_price'] =   $products[$id][$unit]['price'];
                    $product['unit']          =   $unit;
                    
                    $sumPrice       += $products[$id][$unit]['price']*$products[$id][$unit]['qty'];
                    $sumQty         += $products[$id][$unit]['qty'];
                    $dataInsert[]   = new WMS_Transfer_Detail($product);

                    $from = $to = $product;

                    if( $inventory = WMS_Inventory::where('store_code',$wms_transfer->store_from)->where('product_id',$id)->where('unit',$unit)->orderBy('id','desc')->first() ){
                        if( $inventory->inventory > 0 && $inventory->inventory >= $from['product_qty'] ){
                            $from['import']    =   $inventory->import;
                            $from['export']    =   $inventory->export + $from['product_qty'];
                            $from['inventory'] =   $inventory->inventory - $from['product_qty'];
                        } else {
                            return redirect()->route('admin.wms_transfer.index',['type'=>$this->_data['type']])->with('danger','Số lượng tồn kho không tồn tại vui lòng kiểm tra lại');
                        }
                    } else {
                        return redirect()->route('admin.wms_transfer.index',['type'=>$this->_data['type']])->with('danger','Số lượng tồn kho đã thay đổi vui lòng kiểm tra lại');
                    }
                    $from['supplier_id'] = @$wms_transfer->supplier_id;
                    $from['store_code'] = $wms_transfer->store_from;
                    $dataInventory[]   = $from;

                    if( $inventory = WMS_Inventory::where('store_code',$wms_transfer->store_to)->where('product_id',$id)->where('unit',$unit)->orderBy('id','desc')->first() ){
                        $to['import']    =   $inventory->import + $to['product_qty'];
                        $to['export']    =   $inventory->export;
                        $to['inventory'] =   $inventory->inventory + $to['product_qty'];
                    } else {
                        $to['import']    =   $to['product_qty'];
                        $to['export']    =   0;
                        $to['inventory'] =   $to['product_qty'];
                    }
                    $to['supplier_id'] = @$wms_transfer->supplier_id;
                    $to['store_code'] = $wms_transfer->store_to;
                    $dataInventory[]   = $to;
                    
                    unset($products[$id][$unit]);
                }
            }
            $wms_transfer->code          =    time();
            $wms_transfer->transfer_qty    =    (int)$sumQty;
            $wms_transfer->transfer_price  =    floatval($sumPrice);
            $wms_transfer->user_id       =    Auth::id();
            $wms_transfer->priority      =    (int)str_replace('.', '', $request->priority);
            $wms_transfer->status        =    ($request->status) ? implode(',',$request->status) : '';
            $wms_transfer->type          =    $this->_data['type'];
            $wms_transfer->created_at    =    new DateTime();
            $wms_transfer->updated_at    =    new DateTime();
            $wms_transfer->save();
            $wms_transfer->code          =    strtoupper(update_code($wms_transfer->id,'PC'));
            $wms_transfer->save();
            $wms_transfer->details()->saveMany($dataInsert);
            WMS_Inventory::insert($dataInventory);
            return redirect()->route('admin.wms_transfer.index',['type'=>$this->_data['type']])->with('success','Thêm dữ liệu <b>'.$wms_transfer->code.'</b> thành công');
        }
        
    }

    public function edit($id){
        $this->_data['item'] = WMS_Transfer::find($id);
        $this->_data['warehouses'] = $this->getWarehouses();
        if ($this->_data['item'] !== null) {
            $this->_data['products'] = $this->_data['item']->details()->get();
            return view('admin.wms.transfers.edit',$this->_data);
        }
        return redirect()->route('admin.wms_transfer.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){
        return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không được phép thay đổi');
    }

    public function delete(Request $request, $id){
        $wms_transfer = WMS_Transfer::find($id);
        if ($wms_transfer !== null) {
            if($request->data){
                foreach($request->data as $field => $value){
                    $wms_transfer->$field = $value;
                }
            }
            $wms_transfer->type          = $this->_data['type'];
            $wms_transfer->updated_at    = new DateTime();
            $wms_transfer->save();
            WMS_Transfer_Detail::whereIn('id',$wms_transfer->details()->pluck('id')->toArray())->update(['status' => 'cancel']);

            return redirect()->route('admin.wms_transfer.index',['type'=>$this->_data['type']])->with('success', 'Hủy phiếu <b>'.$wms_transfer->code.'</b> thành công');
        }
        return redirect()->route('admin.wms_transfer.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function getWarehouses($type='default'){
        return DB::table('wms_stores')
            ->where('type',$type)
            ->orderBy('priority','asc')
            ->orderBy('id','desc')
            ->get();
    }
    
}