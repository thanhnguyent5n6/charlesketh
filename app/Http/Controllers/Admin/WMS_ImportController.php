<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\WMS_Import;
use App\WMS_Import_Detail;
use App\WMS_Inventory;

use App\User;
use DateTime;
use DNS1D;
use Carbon\Carbon;

class WMS_ImportController extends Controller
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
        $this->_data['siteconfig'] = config('siteconfig.wms.import');
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
        $this->_data['items'] = DB::table('wms_imports as A')
            ->leftjoin('users as B', 'A.user_id','=','B.id')
            ->select('A.*','B.name as username')
            ->where('A.type',$this->_data['type'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->paginate(25);

        $this->_data['total'] = DB::table('wms_imports')
            ->select(DB::raw('sum(import_qty) as qty, sum(import_price) as price'))
            ->whereRaw('FIND_IN_SET(\'publish\',status)')
            ->where('type',$this->_data['type'])->first();

        return view('admin.wms.imports.index',$this->_data);
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
                $products[$key]['title']   		   =  $inventory->product_title . ' ( '.($inventory->unit === 1 ? 'B???/C??i' : ($inventory->unit === 2 ? 'D??n n??ng' : 'D??n l???nh')).' )';
            }
            return response()->json(['items'=>$products]);
        }
    }

    public function print(Request $request){
        if($request->ajax() && $request->id){
            $arrID = explode(',',$request->id);
            $items = WMS_Import::whereIn('id',$arrID)->get();
            if ($items !== null) {
                $data = '';
                foreach($items as $k => $item){
                    $user = User::find($item->user_id);
                    if($k > 0){
                        $data .= '<div style="page-break-after: always;"></div>';
                    }
                    if( $request->loai == 1 ){
                        if( strpos($item->status,'printed') === false ){
                            $item->status .= ',printed';
                            $item->save();
                        }

                        $table = '<table class="table table-bordered table-condensed print-productlist" style="font-size:11px;border:1px solid black">
                        <thead>
                            <tr class="text-uppercase">
                                <th width="7%" align="center" style="border:1px solid black !important"> M?? SP </th>
                                <th width="15%" align="center" style="border:1px solid black !important"> T??n SP </th>
                                <th width="7%" align="center" style="border:1px solid black !important"> ????n v??? t??nh </th>
                                <th width="8%" align="center" style="border:1px solid black !important"> ????n Gi??</th>
                                <th width="6%" align="center" style="border:1px solid black !important"> SL </th>
                                <th width="10%" align="center" style="border:1px solid black !important"> Th??nh ti???n </th>
                            </tr>
                        </thead>
                        <tbody>';
                        if($products = $item->details()->get()){
                            $total = 0;
                            foreach($products as $key => $val){
                                // $total += $val->product_price*$val->product_qty;
                                $table .= '<tr style="font-size:11px">
                                    <td align="center">'.$val->product_code.'</td>
                                    <td>'.$val->product_title.'</td>
                                    <td>
                                        
                                    </td>
                                    <td align="center">'.get_currency_vn($val->product_price,'').'</td>
                                    <td align="center">'.$val->product_qty.'</td>
                                    <td align="center">'.get_currency_vn($val->product_price*$val->product_qty,'').'</td>
                                </tr>';
                            }
                            // $total = ($total + $item->shipping + $item->enhancement)-$item->coupon_amount;
                        }
                        
                        $table .= '<tr>
                                    <td align="right" colspan="30">
                                        <span class="pull-left text-uppercase">
                                            S??? l?????ng: <span class="font-red-mint font-md bold">'.$item->import_qty.'</span>
                                        </span>
                                        <span class="pull-right text-uppercase">
                                            T???ng: <span class="font-red-mint font-md bold">'.get_currency_vn($item->import_price,'').'</span>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>';

                        $data .= '<div class="container">
                            <div class="row">
                                <div class="col-xs-2">
                                    <img src="/public/images/Logo-VTP.png" alt="logo vtp">
                                </div>
                                <div class="col-xs-7">
                                    <p style="font-size: 17px"><b>C??NG TY C??? PH???N ??I???N M??Y V???N TH???NH PH??T</b></p>
                                    <span>M?? s??? thu??: <b>0312629851</b></span><br>
                                    <span>?????a ch???: 272/13A L?? V??n Qu???i, P B??nh H??ng H??a A, Q B??nh T??n, TP. HCM</span><br>
                                    <span>V??n ph??ng giao d???ch: 638 Tr?????ng Chinh, Ph?????ng 15, Qu???n T??n B??nh, TP.HCM</span><br>
                                    <span>??i???n Tho???i: (028) 62 600 888 - Fax: (028) 62 699 888 - T???ng ????i: 0901 100 100</span>
                                </div>
                                <div class="col-xs-3">
                                    <img src="data:image/png;base64,' . DNS1D::getBarcodePNG($item->code, "C128B",3,33) . '" alt="barcode"   />
                                    <p>--------</p> 
                                    <div>Ng??y: '.$item->created_at->format('d/m/Y').'</div>
                                    <div>Ng?????i T???o: Ph Th???o</div>
                                    <div>Phi???u Nh???p: '.$item->code.'</div>
                                    <small style="text-center">Gi??? In: '.Carbon::now().'</small>
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <h2 class="print-title text-center">PHI???U NH???P KHO</h2>
                                <div class="col-xs-12 mb-2"><b>TH??NG TIN NG?????I GIAO H??NG:....................................................................................................................................................................</b></div><br><br>
                                <div class="col-xs-12"><b>Nh???p t???i kho:</b></div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-xs-12"><b>CHI TI???T NH???P H??NG</b></div>
                                <div class="col-xs-12">
                                    '.$table.'
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-xs-12 mb-1">
                                T???ng s??? ti???n ( Vi???t b???ng ch???):..................................................................................................................................................................................
                                </div><br><br>
                                <div class="col-xs-12">
                                S??? ch???ng t??? g???c k??m theo:......................................................................................................................................................................................
                                </div>
                            </div>
                            <br>
                            <br>
                            <div class="row mt-2">
                                <div class="col-xs-3">
                                    <center> <b>Ng?????i L???p Phi???u<br>Ph Th???o</b><br><span>K??,ghi r?? h??? t??n</span> </center>
                                </div>
                                <div class="col-xs-2">
                                    <center> <b>Ng?????i Giao H??ng</b><br><span>K??,ghi r?? h??? t??n</span> </center>
                                </div>
                                <div class="col-xs-2">
                                    <center> <b>Th??? Kho <br>Ph Th???o</b><br><span>K??,ghi r?? h??? t??n</span> </center>
                                </div>
                                <div class="col-xs-2">
                                    <center> <b>K??? To??n Tr?????ng</b><br><span>K??,ghi r?? h??? t??n</span> </center>
                                </div>
                                <div class="col-xs-3">
                                    <center> <b>Gi??m ?????c</b><br><span>K??,ghi r?? h??? t??n</span> </center>
                                </div>
                            </div>
                        </div>';
                    }
                }
                return response()->json(['data'=>$data]);
            }
        }
    }
    
    public function create(){
        $this->_data['warehouses'] = $this->getWarehouses();
        $this->_data['suppliers'] = $this->getSupplier();
        return view('admin.wms.imports.create',$this->_data);
    }

    public function store(Request $request){
        $valid = Validator::make($request->all(), [
            'products'          => 'required',
            'data.store_code'   =>  'required'
            ], [
            'products.required' => 'Vui l??ng ch???n s???n ph???m',
            'data.store_code.required' => 'Vui l??ng ch???n kho h??ng',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $wms_import  = new WMS_Import;

            if($request->data){
                foreach($request->data as $field => $value){
                    $wms_import->$field = $value;
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
                $unit  = (int)$value['unit'];
                $product = [];
                if( isset($products[$id][$unit]) ){
                    $product['product_id']    =   $id;
                    $product['product_code']  =   $products[$id][$unit]['code'];
                    $product['product_title'] =   $products[$id][$unit]['title'];
                    $product['product_qty']   =   $products[$id][$unit]['qty'];
                    $product['product_price'] =   $products[$id][$unit]['price'];
                    $product['unit']      =   $unit;
                    
                    $sumPrice       += $products[$id][$unit]['price']*$products[$id][$unit]['qty'];
                    $sumQty         += $products[$id][$unit]['qty'];
                    $dataInsert[]   = new WMS_Import_Detail($product);

                    if( $inventory = WMS_Inventory::where('store_code',$wms_import->store_code)->where('product_id',$id)->where('unit',$unit)->orderBy('id','desc')->first() ){
                        $product['import']    =   $inventory->import + $product['product_qty'];
                        $product['export']    =   $inventory->export;
                        $product['inventory'] =   $inventory->inventory + $product['product_qty'];
                    } else {
                        $product['import']    =   $product['product_qty'];
                        $product['export']    =   0;
                        $product['inventory'] =   $product['product_qty'];
                    }
                    $product['supplier_id'] = @$wms_import->supplier_id;
                    $product['store_code'] = $wms_import->store_code;
                    $dataInventory[]   = $product;

                    unset($products[$id][$unit]);
                }
            }

            $wms_import->code          =    time();
            $wms_import->import_qty    =    (int)$sumQty;
            $wms_import->import_price  =    floatval($sumPrice);
            $wms_import->user_id       =    Auth::id();
            $wms_import->priority      =    (int)str_replace('.', '', $request->priority);
            $wms_import->status        =    ($request->status) ? implode(',',$request->status) : '';
            $wms_import->type          =    $this->_data['type'];
            $wms_import->created_at    =    new DateTime();
            $wms_import->updated_at    =    new DateTime();
            $wms_import->save();
            $wms_import->code          =    strtoupper(update_code($wms_import->id,'PN'));
            $wms_import->save();
            $wms_import->details()->saveMany($dataInsert);
            WMS_Inventory::insert($dataInventory);
            return redirect()->route('admin.wms_import.index',['type'=>$this->_data['type']])->with('success','Th??m d??? li???u <b>'.$wms_import->code.'</b> th??nh c??ng');
        }
        
    }

    public function edit(Request $request, $id){
        $this->_data['item'] = WMS_Import::find($id);
        $this->_data['warehouses'] = $this->getWarehouses();
        $this->_data['suppliers'] = $this->getSupplier();
        if ($this->_data['item'] !== null) {
            $this->_data['products'] = $this->_data['item']->details()->get();
            return view('admin.wms.imports.edit',$this->_data);
        }
        return redirect()->route('admin.wms_import.index',['type'=>$this->_data['type']])->with('danger', 'D??? li???u kh??ng t???n t???i');
    }

    public function update(Request $request, $id){
        return redirect( $request->redirects_to )->with('danger', 'D??? li???u kh??ng ???????c ph??p thay ?????i');
    }

    public function delete(Request $request, $id){
        $wms_import = WMS_Import::find($id);
        if ($wms_import !== null) {
            if($request->data){
                foreach($request->data as $field => $value){
                    $wms_import->$field = $value;
                }
            }
            $wms_import->type          = $this->_data['type'];
            $wms_import->updated_at    = new DateTime();
            $wms_import->save();
            WMS_Import_Detail::whereIn('id',$wms_import->details()->pluck('id')->toArray())->update(['status' => 'cancel']);

            return redirect()->route('admin.wms_import.index',['type'=>$this->_data['type']])->with('success', 'H???y phi???u <b>'.$wms_import->code.'</b> th??nh c??ng');
        }
        return redirect()->route('admin.wms_import.index',['type'=>$this->_data['type']])->with('danger', 'D??? li???u kh??ng t???n t???i');
    }

    public function getWarehouses($type='default'){
        return DB::table('wms_stores')
            ->where('type',$type)
            ->orderBy('priority','asc')
            ->orderBy('id','desc')
            ->get();
    }

    public function getSupplier($type='provider'){
        return DB::table('suppliers')
            ->where('type',$type)
            ->orderBy('priority','asc')
            ->orderBy('id','desc')
            ->get();
    }
    
}