<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Member;
use DateTime;

class MemberController extends Controller
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
        $this->_data['siteconfig'] = config('siteconfig.member');
        $this->_data['path'] = $this->_data['siteconfig']['path'];
        $this->_data['pageTitle'] = $this->_data['siteconfig'][$this->_data['type']]['page-title'];
    }

    public function index()
    {
        $this->_data['items'] = Member::where('type',$this->_data['type'])->orderBy('priority','asc')->orderBy('id','desc')->paginate(25);
        return view('admin.members.index',$this->_data);
    }

    public function create(){
        return view('admin.members.create',$this->_data);
    }

    public function store(Request $request){
        $valid = Validator::make($request->all(), [
            'data.name' => 'required',
            'data.email' => 'required|email|unique:members,email',
            // 'data.username' => 'required|alpha_dash|unique:members,username',
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
            $member  = new Member;

            if($request->data){
                foreach($request->data as $field => $value){
                    $member->$field = $value;
                }
            }
            $member->password = bcrypt($request->password);
            $member->remember_token = str_random(10);
            $member->priority       = (int)str_replace('.', '', $request->priority);
            $member->status         = ($request->status) ? implode(',',$request->status) : '';
            $member->type           = $this->_data['type'];;
            $member->created_at     = new DateTime();
            $member->updated_at     = new DateTime();
            $member->save();

            return redirect()->route('admin.member.index')->with('success', 'Thêm người dùng <b>'. $member->name .'</b> thành công');
        }
    }

    public function edit($id){
        $this->_data['item'] = Member::find($id);
        if ($this->_data['item'] !== null) {
            return view('admin.members.edit',$this->_data);
        }
        return redirect()->route('admin.member.index')->with('danger', 'Không tìm thấy người dùng này');
    }

    public function update(Request $request, $id){
        $valid = Validator::make($request->all(), [
            'data.name' => 'required',
            'data.email' => 'required|email|unique:members,email,'.$id,
            // 'data.username' => 'required|alpha_dash|unique:members,username,'.$id,
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
            $member = Member::find($id);
            if ($member !== null) {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $member->$field = $value;
                    }
                }
                if ($request->has('password')) {
                    $member->password = bcrypt($request->password);
                }
                $member->priority       = (int)str_replace('.', '', $request->priority);
                $member->status         = ($request->status) ? implode(',',$request->status) : '';
                $member->updated_at     = new DateTime();
                $member->save();
                
                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$member->name.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Không tìm thấy người dùng này');
        }
    }

    public function delete($id){
        $member = Member::find($id);
        if ($member !== null) {
            $member->delete();
            return redirect()->route('admin.member.index')->with('success', 'Xóa người dùng <b>'. $member->name .'</b> thành công');
        }
        return redirect()->route('admin.member.index')->with('danger', 'Không tìm thấy người dùng này');
    }

}
