<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Role;
use App\Permission;

use DateTime;

class RoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $_data;

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(){
        $this->_data['items'] = Role::where('name','!=','admin')->orderBy('priority','asc')->orderBy('id','desc')->paginate(25);
        return view('admin.roles.index',$this->_data);
    }
    
    public function create(){
        $this->_data['permissions'] = Permission::all();
        return view('admin.roles.create', $this->_data);
    }

    public function store(Request $request){
        $valid = Validator::make($request->all(), [
            'display_name'            => 'required',
            'name'	=>	'unique:roles,name'
        ], [
            'display_name.required'               => 'Vui lòng nhập Tên',
            'name.unique'               => 'Slug đã tồn tại, vui lòng nhập slug khác',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $role  = new Role;

            $role->display_name = $request->display_name;
            if( !isset($request->name) || $request->name == ''){
                $role->name      = str_slug($request->display_name);
            }else{
                $role->name       = str_slug($request->name);
            }
            $role->description = $request->description;
            $role->priority       = (int)str_replace('.', '', $request->priority);
            $role->status         = ($request->status) ? implode(',',$request->status) : '';
            $role->created_at     = new DateTime();
            $role->updated_at     = new DateTime();
            $role->save();
            
            return redirect()->route('admin.role.index')->with('success','Thêm dữ liệu <b>'.$role->display_name.'</b> thành công');
        }
        
    }

    public function edit($id){
        $this->_data['item'] = Role::find($id);
        if ($this->_data['item'] !== null) {
            return view('admin.roles.edit',$this->_data);
        }
        return redirect()->route('admin.role.index')->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'display_name'            => 'required',
            'name'	=>	'unique:roles,name,'.$id,
        ], [
            'display_name.required'               => 'Vui lòng nhập Tên',
            'name.unique'               => 'Slug đã tồn tại, vui lòng nhập slug khác',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $role = Role::find($id);
            if ($role !== null) {
                
                $role->display_name = $request->display_name;
	            if( !isset($request->name) || $request->name == ''){
	                $role->name      = str_slug($request->display_name);
	            }else{
	                $role->name       = str_slug($request->name);
	            }
	            $role->description = $request->description;
                $role->priority       = (int)str_replace('.', '', $request->priority);
                $role->status         = ($request->status) ? implode(',',$request->status) : '';
                $role->updated_at     = new DateTime();
                $role->save();

                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$role->display_name.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
    }

    public function delete($id){
        $role = Role::find($id);
        $deleted = $role->display_name;
        if ($role !== null) {
            if( $role->delete() ){
                return redirect()->route('admin.role.index')->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.role.index')->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.role.index')->with('danger', 'Dữ liệu không tồn tại');
    }
}
