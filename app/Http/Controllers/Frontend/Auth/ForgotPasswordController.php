<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:member');
    }

    public function showLinkRequestForm()
    {
        return view('frontend.auth.passwords.email');
    }

    protected function sendResetLinkResponse($response)
    {
        return ['type'=>'success', 'icon'=>'check', 'message'=>__($response)];
        // return back()->with('status', trans($response));
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return ['type'=>'danger', 'icon'=>'error', 'message'=>__($response)];
        // return back()->withErrors(['email' => trans($response)]);
    }

    public function broker()
    {
        return Password::broker('members');
    }
    
}
