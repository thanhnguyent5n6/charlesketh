<?php

namespace App\Http\Controllers\Socialite;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Member;
use Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')
            ->redirectUrl(route('login.google.callback'))
            ->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $member = Socialite::driver('google')->user();
        $member = Member::firstOrCreate([
            'oauth_id' =>  $member->getId(),
            'oauth_provider' =>  'google',
            'email' =>  $member->getEmail()
        ], [
            'name'  =>  $member->getName(),
            'username'  =>  $member->getEmail(),
            'email'  =>  $member->getEmail(),
            'oauth_id' =>  $member->getId(),
            'oauth_provider' =>  'google',
            'password'  =>  bcrypt($member->getId() . time() . $member->getEmail()),
            'type'  =>  'normal'
        ]);
        auth()->guard('member')->login($member);
        return redirect()->route('frontend.home.index');
    }
}
