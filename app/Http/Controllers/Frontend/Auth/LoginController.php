<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('guest:member')->except('logout');
    }

    public function showLoginForm(){
        return view('frontend.auth.login');
    }

    public function logout(Request $request){
        $this->guard('member')->logout();
        // $request->session()->invalidate();
        return redirect('/');
    }

    public function guard(){
        return \Auth::guard('member');
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return ['type'=>'success', 'icon'=>'check', 'message'=>__('account.sign_in_success'), 'redirect'=>url('/')];
    }

}
