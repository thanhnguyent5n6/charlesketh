<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Group;
use App\Role;
use DateTime;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    private $_data;

    public function __construct(Request $request)
    {
        $this->_data['type'] = (isset($request->type) && $request->type !='') ? $request->type : 'default';
    }

    public function index()
    {
    	$this->_data['items'] = User::where('id','!=',1)->where('type',$this->_data['type'])->orderBy('priority','asc')->orderBy('id','desc')->paginate(25);
        return view('admin.users.index',$this->_data);
    }

    public function create(){
        $this->_data['groups'] = Group::orderBy('priority','asc')->orderBy('id','desc')->get();
    	return view('admin.users.create',$this->_data);
    }

    public function store(Request $request){
        $valid = Validator::make($request->all(), [
            'data.name' => 'required',
            'data.email' => 'required|email|unique:users,email',
            // 'data.username' => 'required|alpha_dash|unique:users,username',
            'password' => 'required|min:6|confirmed'
        ], [
            'data.name.required' => 'Vui lòng nhập Họ Tên',
            'data.email.required' => 'Vui lòng nhập Email',
            'data.email.email' => 'Không đúng định dạng Email',
            'data.email.unique' => 'Email này đã trùng, vui lòng chọn Email khác',
            // 'data.username.required' => 'Vui lòng nhập Tên đăng nhập',
            // 'data.username.alpha_dash' => 'Tên đăng nhập không được chứa các ký tự đặc biệt',
            // 'data.username.unique' => 'Tên đăng nhập này đã trùng, vui lòng chọn tên khác',
            'password.required' => 'Vui lòng nhập Mật khẩu',
            'password.min' => 'Mật khẩu có ít nhất :min ký tự',
            'password.confirmed' => 'Confirm Mật khẩu không chính xác',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $user  = new User;

            if($request->data){
                foreach($request->data as $field => $value){
                    $user->$field = $value;
                }
            }
            $user->province_id    = (int)$user->province_id;
            $user->district_id    = (int)$user->district_id;

            $user->password       = bcrypt($request->password);
            $user->remember_token = str_random(10);
            $user->priority       = (int)str_replace('.', '', $request->priority);
            $user->status         = ($request->status) ? implode(',',$request->status) : '';
            $user->type           = $this->_data['type'];
            $user->created_at     = new DateTime();
            $user->updated_at     = new DateTime();
            $user->save();

            // Add Group
            if ($request->has('groups') && is_array($request->groups) && count($request->groups) > 0) {
                $groups = Group::whereIn('id',$request->groups)->pluck('role_permission')->toArray();
                if($groups !== null){
                    $roles = [];
                    foreach($groups as $key => $value){
                        foreach(array_keys($value) as $name){
                            if( $role = Role::where('name',$name)->first() ){
                                $roles[] = $role->id;
                            }
                        }
                    }
                    if($roles !== null){
                        $user->groups()->sync($request->groups);
                        $user->roles()->sync($roles);
                    }
                }
            }

            return redirect()->route('admin.user.index',['type'=>$this->_data['type']])->with('success', 'Thêm người dùng <b>'. $user->name .'</b> thành công');
        }
    }

    public function profile(){
        $this->_data['item'] = User::find( Auth::user()->id );
        if ($this->_data['item'] !== null) {
            return view('admin.users.profile',$this->_data);
        }
        return redirect()->route('admin.user.index')->with('danger', 'Không tìm thấy người dùng này');
    }

    public function updateProfile(Request $request, $id){
        $valid = Validator::make($request->all(), [
            'data.name' => 'required',
            'data.email' => 'required|email|unique:users,email,'.$id,
            // 'data.username' => 'required|alpha_dash|unique:users,username,'.$id,
            'password' => 'confirmed'
        ], [
            'data.name.required' => 'Vui lòng nhập Họ Tên',
            'data.email.required' => 'Vui lòng nhập Email',
            'data.email.email' => 'Không đúng định dạng Email',
            'data.email.unique' => 'Email này đã trùng, vui lòng chọn Email khác',
            // 'data.username.required' => 'Vui lòng nhập Tên đăng nhập',
            // 'data.username.alpha_dash' => 'Tên đăng nhập không được chứa các ký tự đặc biệt',
            // 'data.username.unique' => 'Tên đăng nhập này đã trùng, vui lòng chọn tên khác',
            'password.confirmed' => 'Confirm Mật khẩu không chính xác',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $user = User::find($id);
            if ($user !== null) {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $user->$field = $value;
                    }
                }
                if ($request->has('password')) {
                    $user->password = bcrypt($request->password);
                }
                $user->province_id    = (int)$user->province_id;
                $user->district_id    = (int)$user->district_id;
                $user->priority       = (int)str_replace('.', '', $request->priority);
                $user->status         = ($request->status) ? implode(',',$request->status) : '';
                $user->updated_at     = new DateTime();
                $user->save();

                return redirect()->route('admin.user.profile')->with('success','Cập nhật dữ liệu <b>'.$user->name.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Không tìm thấy người dùng này');
        }
    }

    public function edit($id){
        $this->_data['item'] = User::where('id','!=',1)->where('type',$this->_data['type'])->find($id);
        if ($this->_data['item'] !== null) {
            $this->_data['groups'] = Group::orderBy('priority','asc')->orderBy('id','desc')->get();
            $this->_data['user_group'] = $this->_data['item']->groups()->pluck('id')->toArray();
            return view('admin.users.edit',$this->_data);
        }
        return redirect()->route('admin.user.index',['type'=>$this->_data['type']])->with('danger', 'Không tìm thấy người dùng này');
    }

    public function update(Request $request, $id){
        $valid = Validator::make($request->all(), [
            'data.name' => 'required',
            'data.email' => 'required|email|unique:users,email,'.$id,
            // 'data.username' => 'required|alpha_dash|unique:users,username,'.$id,
            'password' => 'confirmed'
        ], [
            'data.name.required' => 'Vui lòng nhập Họ Tên',
            'data.email.required' => 'Vui lòng nhập Email',
            'data.email.email' => 'Không đúng định dạng Email',
            'data.email.unique' => 'Email này đã trùng, vui lòng chọn Email khác',
            // 'data.username.required' => 'Vui lòng nhập Tên đăng nhập',
            // 'data.username.alpha_dash' => 'Tên đăng nhập không được chứa các ký tự đặc biệt',
            // 'data.username.unique' => 'Tên đăng nhập này đã trùng, vui lòng chọn tên khác',
            'password.confirmed' => 'Confirm Mật khẩu không chính xác',
        ]);

        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $user = User::where('id','!=',1)->where('type',$this->_data['type'])->find($id);
            if ($user !== null) {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $user->$field = $value;
                    }
                }
                if ($request->has('password')) {
                    $user->password = bcrypt($request->password);
                }
                $user->province_id    = (int)$user->province_id;
                $user->district_id    = (int)$user->district_id;
                
                $user->priority       = (int)str_replace('.', '', $request->priority);
                $user->status         = ($request->status) ? implode(',',$request->status) : '';
                $user->updated_at     = new DateTime();
                $user->save();

                // Add Group
                if ($request->has('groups') && is_array($request->groups) && count($request->groups) > 0) {
                    $groups = Group::whereIn('id',$request->groups)->pluck('role_permission')->toArray();
                    if($groups !== null){
                        $roles = [];
                        foreach($groups as $key => $value){
                            foreach(array_keys($value) as $name){
                                if( $role = Role::where('name',$name)->first() ){
                                    $roles[] = $role->id;
                                }
                            }
                        }
                        $user->groups()->sync($request->groups);
                        $user->roles()->sync($roles);
                    }
                }else{
                    $user->groups()->sync([]);
                    $user->roles()->sync([]);
                }
                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$user->name.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Không tìm thấy người dùng này');
        }
    }

    public function delete($id){
    	$user = User::where('id','!=',1)->where('type',$this->_data['type'])->find($id);
        \App\Product::whereIn('id',$user->products()->pluck('id')->toArray())->update(['user_id' => 1]);
        \App\Post::whereIn('id',$user->posts()->pluck('id')->toArray())->update(['user_id' => 1]);
        
        if ($user !== null) {
            $user->delete();
            return redirect()->route('admin.user.index',['type'=>$this->_data['type']])->with('success', 'Xóa người dùng <b>'. $user->name .'</b> thành công');
        }
        return redirect()->route('admin.user.index',['type'=>$this->_data['type']])->with('danger', 'Không tìm thấy người dùng này');
    }

}
