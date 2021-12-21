<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Register;

use Excel;
use DateTime;

class RegisterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $_data;

    public function __construct(Request $request){
        $this->_data['type'] = (isset($request->type) && $request->type !='') ? $request->type : 'default';
        $this->_data['siteconfig'] = config('siteconfig.register');
        $this->_data['default_language'] = config('siteconfig.general.language');
        $this->_data['languages'] = config('siteconfig.languages');
        $this->_data['pageTitle'] = $this->_data['siteconfig'][$this->_data['type']]['page-title'];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request){
        $items = Register::where('type',$this->_data['type'])->orderBy('priority','asc')->orderBy('id','desc');
        if( $keyword = $request->keyword ){
            $items->where(function($query) use ($keyword){
                $query->where('name','like','%'.$keyword.'%')
                    ->orWhere('email','like','%'.$keyword.'%')
                    ->orWhere('phone','like','%'.$keyword.'%')
                    ->orWhere('address','like','%'.$keyword.'%');
            });
        }
        $this->_data['items'] = $items->paginate(25);

        return view('admin.registers.index',$this->_data);
    }
    
    public function create(){
        return view('admin.registers.create',$this->_data);
    }

    public function store(Request $request){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'image'            => 'image|max:2048'
        ], [
            'image.image'               => 'Không đúng chuẩn hình ảnh cho phép',
            'image.max'                 => 'Dung lượng vượt quá giới hạn cho phép là :max KB'
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $register  = new Register;

            if($request->data){
                foreach($request->data as $field => $value){
                    $register->$field = $value;
                }
            }
            
            $register->priority       = (int)str_replace('.', '', $request->priority);
            $register->status         = ($request->status) ? implode(',',$request->status) : '';
            $register->type           = $this->_data['type'];
            $register->created_at     = new DateTime();
            $register->updated_at     = new DateTime();
            $register->save();
            return redirect()->route('admin.register.index',['type'=>$this->_data['type']])->with('success','Thêm dữ liệu <b>'.$register->name.'</b> thành công');
        }
        
    }

    public function edit($id){
        $this->_data['item'] = Register::find($id);
        if ($this->_data['item'] !== null) {
            return view('admin.registers.edit',$this->_data);
        }
        return redirect()->route('admin.register.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'image' => 'image|max:2048',
        ], [
            'image.image' => 'Không đúng chuẩn hình ảnh cho phép',
            'image.max' => 'Dung lượng vượt quá giới hạn cho phép là :max KB',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $register = Register::find($id);
            if ($register !== null) {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $register->$field = $value;
                    }
                }

                $register->priority       = (int)str_replace('.', '', $request->priority);
                $register->status         = ($request->status) ? implode(',',$request->status) : '';
                $register->type           = $this->_data['type'];
                $register->updated_at     = new DateTime();
                $register->save();

                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$register->name.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
    }

    public function delete($id){
        $register = Register::find($id);
        $deleted = $register->name;
        if ($register !== null) {
            if( $register->delete() ){
                return redirect()->route('admin.register.index',['type'=>$this->_data['type']])->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.register.index',['type'=>$this->_data['type']])->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.register.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function export(Request $request){
        $filename = 'Danh-Sach-Khach-Hang-'.date('dmY');
        $fileExt = $request->extension;

        $items = Register::where('type',$this->_data['type'])->orderBy('priority','asc')->orderBy('id','desc');
        if( $keyword = $request->keyword ){
            $items->where(function($query) use ($keyword){
                $query->where('name','like','%'.$keyword.'%')
                    ->orWhere('email','like','%'.$keyword.'%')
                    ->orWhere('phone','like','%'.$keyword.'%')
                    ->orWhere('address','like','%'.$keyword.'%');
            });
        }
        $data = $items->get()->toArray();

        return Excel::create($filename, function($excel) use ($data) {
            $excel->sheet($this->_data['pageTitle'], function($sheet) use ($data) {
                $sheet->loadView('excel.registers')->with(['data'=>$data]);
            });
        })->download($fileExt);
    }
}
