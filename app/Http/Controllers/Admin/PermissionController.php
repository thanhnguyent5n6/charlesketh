<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Permission;

use DateTime;

class PermissionController extends Controller
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
        $this->_data['items'] = Permission::paginate(25);
        return view('admin.permissions.index',$this->_data);
    }
    
    public function create(){
        return view('admin.permissions.create');
    }

    public function store(Request $request){
        // dd($request->all());
        $valid = Validator::make($request->all(), [
            'display_name'            => 'required',
            'name'  =>  'unique:permissions,name'
        ], [
            'display_name.required'               => 'Vui lòng nhập Tên',
            'name.unique'               => 'Slug đã tồn tại, vui lòng nhập slug khác',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $permission  = new Permission;

            $permission->display_name = $request->display_name;
            if( !isset($request->name) || $request->name == ''){
                $permission->name      = str_slug($request->display_name);
            }else{
                $permission->name       = str_slug($request->name);
            }
            $permission->description = $request->description;
            $permission->priority       = (int)str_replace('.', '', $request->priority);
            $permission->status         = ($request->status) ? implode(',',$request->status) : '';
            $permission->created_at     = new DateTime();
            $permission->updated_at     = new DateTime();
            $permission->save();
            return redirect()->route('admin.permission.index')->with('success','Thêm dữ liệu <b>'.$permission->display_name.'</b> thành công');
        }
        
    }

    public function edit($id){
        $this->_data['item'] = Permission::find($id);
        if ($this->_data['item'] !== null) {
            return view('admin.permissions.edit',$this->_data);
        }
        return redirect()->route('admin.permission.index')->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'display_name'            => 'required',
            'name'  =>  'unique:permissions,name,'.$id,
        ], [
            'display_name.required'               => 'Vui lòng nhập Tên',
            'name.unique'               => 'Slug đã tồn tại, vui lòng nhập slug khác',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $permission = Permission::find($id);
            if ($permission !== null) {
                
                $permission->display_name = $request->display_name;
                if( !isset($request->name) || $request->name == ''){
                    $permission->name      = str_slug($request->display_name);
                }else{
                    $permission->name       = str_slug($request->name);
                }
                $permission->description = $request->description;
                $permission->priority       = (int)str_replace('.', '', $request->priority);
                $permission->status         = ($request->status) ? implode(',',$request->status) : '';
                $permission->updated_at     = new DateTime();
                $permission->save();

                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$permission->display_name.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
    }

    public function delete($id){
        $permission = Permission::find($id);
        $deleted = $permission->display_name;
        if ($permission !== null) {
            if( $permission->delete() ){
                return redirect()->route('admin.permission.index')->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.permission.index')->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.permission.index')->with('danger', 'Dữ liệu không tồn tại');
    }
}
