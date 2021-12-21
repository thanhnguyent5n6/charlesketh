<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\WMS_Store;
use App\WMS_Inventory;

use DateTime;

class WMS_StoreController extends Controller
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
        $this->_data['siteconfig'] = config('siteconfig.wms.store');
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
        $this->_data['items'] = DB::table('wms_stores as A')
            ->where('A.type',$this->_data['type'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->paginate(25);

        return view('admin.wms.stores.index',$this->_data);
    }
    
    public function create(){
        return view('admin.wms.stores.create',$this->_data);
    }

    public function store(Request $request){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'data.name'            => 'required',
            'data.code'            => 'required|unique:wms_stores,code',
        ], [
            'data.name.required'               => 'Vui lòng nhập Tên Kho',
            'data.code.required'                 => 'Vui lòng nhập Mã Kho',
            'data.code.unique'                 => 'Mã Kho đã tồn tại',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $wms_store  = new WMS_Store;

            if($request->data){
                foreach($request->data as $field => $value){
                    $wms_store->$field = $value;
                }
            }
            
            $wms_store->priority       = (int)str_replace('.', '', $request->priority);
            $wms_store->status         = ($request->status) ? implode(',',$request->status) : '';
            $wms_store->type           = $this->_data['type'];
            $wms_store->created_at     = new DateTime();
            $wms_store->updated_at     = new DateTime();
            $wms_store->save();
            return redirect()->route('admin.wms_store.index',['type'=>$this->_data['type']])->with('success','Thêm dữ liệu <b>'.$wms_store->name.'</b> thành công');
        }
        
    }

    public function edit($id){
        $this->_data['item'] = WMS_Store::find($id);
        if ($this->_data['item'] !== null) {
            return view('admin.wms.stores.edit',$this->_data);
        }
        return redirect()->route('admin.wms_store.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'data.name'            => 'required',
            'data.code'            => 'required|unique:wms_stores,code,'.$id,
        ], [
            'data.name.required'               => 'Vui lòng nhập Tên Kho',
            'data.code.required'                 => 'Vui lòng nhập Mã Kho',
            'data.code.unique'                 => 'Mã Kho đã tồn tại',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $wms_store = WMS_Store::find($id);
            if ($wms_store !== null) {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $wms_store->$field = $value;
                    }
                }

                $wms_store->priority       = (int)str_replace('.', '', $request->priority);
                $wms_store->status         = ($request->status) ? implode(',',$request->status) : '';
                $wms_store->type           = $this->_data['type'];
                $wms_store->updated_at     = new DateTime();
                $wms_store->save();

                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$wms_store->name.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
    }

    public function delete($id){
        $wms_store = WMS_Store::find($id);
        $deleted = $wms_store->name;
        if ($wms_store !== null) {
            if( $wms_store->delete() ){
                return redirect()->route('admin.wms_store.index',['type'=>$this->_data['type']])->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.wms_store.index',['type'=>$this->_data['type']])->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.wms_store.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function inventory(Request $request) {

        $this->_data['stores'] = $this->getWarehouses();
        $ids = WMS_Inventory::select(DB::raw('max(id) as id'))->groupBy(['store_code','product_id','unit'])->get()->toArray();
        $this->_data['items'] = WMS_Inventory::whereIn('id',$ids)->latest()->paginate(25);

        return view('admin.wms.stores.inventory',$this->_data);
    }

    public function getWarehouses($type='default'){
        return DB::table('wms_stores')
            ->where('type',$type)
            ->orderBy('priority','asc')
            ->orderBy('id','desc')
            ->get();
    }
}
