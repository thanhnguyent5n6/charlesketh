<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Member;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Notifications\UserRegistered;
use Illuminate\Http\Request;

use DateTime;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:member');
    }

    public function showRegistrationForm()
    {
        return view('frontend.auth.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email|unique:members,email',
            'type' => 'required',
            'name' => 'required',
            // 'username' => 'required|alpha_dash|unique:members,username',
            'password' => 'required|min:6|confirmed',
            'policy' => 'required',
        ], [
            'email.required' => 'Vui lòng nhập Email',
            'email.email' => 'Không đúng định dạng Email',
            'email.unique' => 'Email này đã được sử dụng, vui lòng chọn Email khác',
            'type.required' => 'Vui lòng cho chúng tôi biết bạn là ai.',
            'name.required' => 'Vui lòng nhập Họ và Tên',
            // 'username.required' => 'Vui lòng nhập Tên đăng nhập',
            // 'username.alpha_dash' => 'Tên đăng nhập không được chứa các ký tự đặc biệt',
            // 'username.unique' => 'Tên đăng nhập này đã trùng, vui lòng chọn tên khác',
            // 'password.required' => 'Vui lòng nhập Mật khẩu',
            'password.min' => 'Mật khẩu có ít nhất :min ký tự',
            'password.confirmed' => 'Confirm Mật khẩu không chính xác',
            'policy.required' => 'Bạn chưa đồng ý Điều khoản dịch vụ & Chính sách bảo mật của chúng tôi',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return Member
     */
    protected function create(array $data)
    {
        $user = Member::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => @$data['address'] ? $data['address'] : null,
            'province_id' => @$data['province_id'] ? $data['province_id'] : 0,
            'district_id' => @$data['district_id'] ? $data['district_id'] : 0,
            // 'username' => $data['username'],
            'password' => bcrypt($data['password']),
            // 'remember_token' => str_random(10),
            'type' => $data['type'],
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),
        ]);
        if($user) session()->flash('success', 'Thêm dữ liệu <b>'.$user->name.'</b> thành công');
        return $user;
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard('member')->login($user);
        return $this->registered($request, $user) ? ['type'=>'danger', 'icon'=>'error', 'message'=>__('account.sign_up_fail')] : ['type'=>'success', 'icon'=>'check', 'message'=>__('account.sign_up_success')];
        // return ['type'=>'success', 'icon'=>'check', 'message'=>__('account.sign_up_success')];
    }

    protected function registered(Request $request, $user)
    {
        $user->notify(new UserRegistered($user));
    }
    
}
