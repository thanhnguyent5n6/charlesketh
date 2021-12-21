<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Supplier;

use DateTime;

class SupplierController extends Controller
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
        $this->_data['siteconfig'] = config('siteconfig.supplier');
        $this->_data['default_language'] = config('siteconfig.general.language');
        $this->_data['languages'] = config('siteconfig.languages');
        $this->_data['path'] = $this->_data['siteconfig']['path'];
        $this->_data['thumbs'] = $this->_data['siteconfig'][$this->_data['type']]['thumbs'];
        $this->_data['pageTitle'] = $this->_data['siteconfig'][$this->_data['type']]['page-title'];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(){
        $this->_data['items'] = DB::table('suppliers as A')
            ->where('A.type',$this->_data['type'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->paginate(25);

        return view('admin.suppliers.index',$this->_data);
    }
    
    public function create(){
        return view('admin.suppliers.create',$this->_data);
    }

    public function store(Request $request){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'data.name'            => 'required',
            'code'            => 'required|unique:suppliers,code',
            'image' => 'image|max:2048',
        ], [
            'data.name.required'               => 'Vui lòng nhập Tên Nhà Cung Cấp',
            'code.required'                 => 'Vui lòng nhập Mã Nhà Cung Cấp',
            'code.unique'                 => 'Mã Nhà Cung Cấp đã tồn tại',
            'image.image' => 'Không đúng chuẩn hình ảnh cho phép',
            'image.max' => 'Dung lượng vượt quá giới hạn cho phép là :max KB',
        ]);
        if ($valid->fails()) {
            if($request->ajax()){
                return response()->json(['type'=>'danger', 'icon'=>'warning', 'message'=>$valid->errors()->first()]);
            }
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $supplier  = new Supplier;

            if($request->data){
                foreach($request->data as $field => $value){
                    $supplier->$field = $value;
                }
            }

            if($request->hasFile('image')){
                $supplier->image = save_image($this->_data['path'],$request->file('image'),$this->_data['thumbs']);
            }

            $supplier->code           = strtoupper($request->code);
            $supplier->slug           = $request->slug ? str_slug($request->slug) : str_slug($request->data['name']);
            $supplier->priority       = (int)str_replace('.', '', $request->priority);
            $supplier->status         = ($request->status) ? implode(',',$request->status) : '';
            $supplier->type           = $this->_data['type'];
            $supplier->created_at     = new DateTime();
            $supplier->updated_at     = new DateTime();
            $supplier->save();

            if($request->ajax()){
                $items = Supplier::select('id','name')->where('type',$this->_data['type'])->orderBy('priority','asc')->orderBy('id','desc')->get();
                $newData = '';
                if($items){
                    foreach($items as $item){
                        $newData .= '<option value="'.$item->id.'" '.( ( $item->id == $supplier->id ) ? 'selected' : '' ).'> '.$item->name.' </option>';
                    }
                }
                return response()->json(['type'=>'success', 'icon'=>'check', 'message'=>'Thêm dữ liệu <b>'.$supplier->name.'</b> thành công', 'newData'=>$newData]);
            }

            return redirect()->route('admin.supplier.index',['type'=>$this->_data['type']])->with('success','Thêm dữ liệu <b>'.$supplier->name.'</b> thành công');
        }
        
    }

    public function edit($id){
        $this->_data['item'] = Supplier::find($id);
        if ($this->_data['item'] !== null) {
            return view('admin.suppliers.edit',$this->_data);
        }
        return redirect()->route('admin.supplier.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'data.name'            => 'required',
            'code'            => 'required|unique:suppliers,code,'.$id,
            'image' => 'image|max:2048',
        ], [
            'data.name.required'               => 'Vui lòng nhập Tên Nhà Cung Cấp',
            'code.required'                 => 'Vui lòng nhập Mã Nhà Cung Cấp',
            'code.unique'                 => 'Mã Nhà Cung Cấp đã tồn tại',
            'image.image' => 'Không đúng chuẩn hình ảnh cho phép',
            'image.max' => 'Dung lượng vượt quá giới hạn cho phép là :max KB',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $supplier = Supplier::find($id);
            if ($supplier !== null) {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $supplier->$field = $value;
                    }
                }

                if($request->hasFile('image')){
                    delete_image($this->_data['path'].'/'.$supplier->image,$this->_data['thumbs']);
                    $supplier->image = save_image($this->_data['path'],$request->file('image'),$this->_data['thumbs']);
                }

                $supplier->code           = strtoupper($request->code);
                $supplier->slug           = $request->slug ? str_slug($request->slug) : str_slug($request->data['name']);
                $supplier->priority       = (int)str_replace('.', '', $request->priority);
                $supplier->status         = ($request->status) ? implode(',',$request->status) : '';
                $supplier->type           = $this->_data['type'];
                $supplier->updated_at     = new DateTime();
                $supplier->save();

                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$supplier->name.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
    }

    public function delete($id){
        $supplier = Supplier::find($id);
        $deleted = $supplier->name;
        if ($supplier !== null) {
            delete_image($this->_data['path'].'/'.$supplier->image,$this->_data['thumbs']);
            if( $supplier->delete() ){
                return redirect()->route('admin.supplier.index',['type'=>$this->_data['type']])->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.supplier.index',['type'=>$this->_data['type']])->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.supplier.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }
}
