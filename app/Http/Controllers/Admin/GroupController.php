<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Group;
use App\Role;
use App\Permission;

use DateTime;

class GroupController extends Controller
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
        $this->_data['items'] = Group::orderBy('priority','asc')->orderBy('id','desc')->paginate(25);
        return view('admin.groups.index',$this->_data);
    }
    
    public function create(){
        $this->_data['roles'] = Role::where('name','!=','admin')->orderBy('priority','asc')->orderBy('id','desc')->get();
        return view('admin.groups.create', $this->_data);
    }

    public function store(Request $request){
        $valid = Validator::make($request->all(), [
            'display_name'            => 'required',
            'name'  =>  'unique:groups,name'
        ], [
            'display_name.required'               => 'Vui lòng nhập Tên',
            'name.unique'               => 'Slug đã tồn tại, vui lòng nhập slug khác',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $group  = new Group;

            $group->display_name = $request->display_name;
            if( !isset($request->name) || $request->name == ''){
                $group->name      = str_slug($request->display_name);
            }else{
                $group->name       = str_slug($request->name);
            }
            $group->description = $request->description;
            $group->role_permission = $request->roles;
            $group->priority       = (int)str_replace('.', '', $request->priority);
            $group->status         = ($request->status) ? implode(',',$request->status) : '';
            $group->created_at     = new DateTime();
            $group->updated_at     = new DateTime();
            $group->save();
            // Assign Roles with Permissions
            if( $request->roles ){
                foreach( $request->roles as $role => $perms){
                    $dataPerms = [];
                    foreach( $perms as $perm){
                        $dataPerms[]['name'] = $role.'-'.$perm.'-'.$group->id;
                    }
                    Permission::insert($dataPerms);
                    $lastIds = Permission::orderBy('id', 'desc')->take(count($dataPerms))->pluck('id');


                    $role = Role::where('name',$role)->first();
                    $dataInsert = [];
                    foreach($lastIds as $key => $lastId){
                        $dataInsert[$key]['role_id'] = $role->id;
                        $dataInsert[$key]['permission_id'] = $lastId;
                    }
                    DB::table('permission_role')->insert($dataInsert);
                }
                
            }
            return redirect()->route('admin.group.index')->with('success','Thêm dữ liệu <b>'.$group->display_name.'</b> thành công');
        }
        
    }

    public function edit($id){
        $this->_data['item'] = Group::find($id);
        if ($this->_data['item'] !== null) {
            $this->_data['roles'] = Role::where('name','!=','admin')->orderBy('priority','asc')->orderBy('id','desc')->get();
            return view('admin.groups.edit',$this->_data);
        }
        return redirect()->route('admin.group.index')->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'display_name'            => 'required',
            'name'  =>  'unique:groups,name,'.$id,
        ], [
            'display_name.required'               => 'Vui lòng nhập Tên',
            'name.unique'               => 'Slug đã tồn tại, vui lòng nhập slug khác',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $group = Group::find($id);
            if ($group !== null) {

                if($group->role_permission !== null){
                    $roles = '';
                    foreach(array_keys($group->role_permission) as $name){
                        $roles .= Role::where('name',$name)->pluck('id')->first().',';
                    }
                    $roles = explode(',',trim($roles,','));

                    if($roles !== null){
                        DB::table('role_user')->whereIn('role_id',$roles)->delete();
                    }
                }
                
                $group->display_name = $request->display_name;
                if( !isset($request->name) || $request->name == ''){
                    $group->name      = str_slug($request->display_name);
                }else{
                    $group->name       = str_slug($request->name);
                }

                $group->description = $request->description;
                $group->role_permission = $request->roles;
                $group->priority       = (int)str_replace('.', '', $request->priority);
                $group->status         = ($request->status) ? implode(',',$request->status) : '';
                $group->updated_at     = new DateTime();
                $group->save();

                $deleteIds = Permission::where('name','like','%'.$id)->pluck('id');
                DB::table('permissions')->whereIn('id',$deleteIds)->delete();
                DB::table('permission_role')->whereIn('permission_id',$deleteIds)->delete();
                // Assign Roles with Permissions
                if( $request->roles ){
                    $roleIds = [];
                    foreach( $request->roles as $role => $perms){
                        $dataPerms = [];
                        foreach( $perms as $perm){
                            $dataPerms[]['name'] = $role.'-'.$perm.'-'.$group->id;
                        }
                        Permission::insert($dataPerms);
                        $lastIds = Permission::orderBy('id', 'desc')->take(count($dataPerms))->pluck('id');
                        $role = Role::where('name',$role)->first();
                        $dataInsert = [];
                        foreach($lastIds as $key => $lastId){
                            $dataInsert[$key]['role_id'] = $role->id;
                            $dataInsert[$key]['permission_id'] = $lastId;
                        }
                        DB::table('permission_role')->insert($dataInsert);
                        $roleIds[] = $role->id;
                    }
                    $users = $group->users()->get();
                    if($users){
                        foreach($users as $user){
                            $user->roles()->sync($roleIds);
                        }
                    }
                } else {
                    $users = $group->users()->get();
                    if($users){
                        foreach($users as $user){
                            $user->roles()->sync([]);
                        }
                    }
                }

                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$group->display_name.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
    }

    public function delete($id){
        $group = Group::find($id);
        $deleteIds = Permission::where('name','like','%'.$id)->pluck('id');
        DB::table('permissions')->whereIn('id',$deleteIds)->delete();
        DB::table('permission_role')->whereIn('permission_id',$deleteIds)->delete();

        $users = $group->users()->get()->toArray();
        if($users){
            foreach($users as $user){
                $user->roles()->sync([]);
            }
        }

        $deleted = $group->display_name;
        if ($group !== null) {
            if( $group->delete() ){
                return redirect()->route('admin.group.index')->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.group.index')->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.group.index')->with('danger', 'Dữ liệu không tồn tại');
    }
}
