<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Setting;
use App\Member;
use App\Order;
use App\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use Cache;
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

    public function __construct(Request $request){
        $this->middleware(function($request,$next){
            $lang = (session('lang')) ? session('lang') : config('settings.language');
            App::setLocale($lang);
            $this->_data = set_type($request->type);
            $this->_data['lang'] = $lang;
            $this->_data['meta_seo'] = set_meta_tags($lang);
            View::share('siteconfig', config('siteconfig'));
            return $next($request);
        });
    }

    public function index(){

    	return redirect()->route('frontend.member.profile');
        
        $this->_data['site']['title'] = __('site.member');
        $this->_data['site']['class'] = 'site-member';
        $this->_data['breadcrumb'] = '<li class="breadcrumb-item"> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active">'.$this->_data['site']['title'].'</li>';


        $id = auth()->guard('member')->user()->id;
        $this->_data['items'] = Order::where('member_id',$id)->get();
        return view('frontend.member.index', $this->_data);
    }

    public function profile(Request $request){
    	$this->_data['site']['title'] = __('account.profile');
        $this->_data['site']['class'] = 'site-member-profile';
        $this->_data['breadcrumb'] = '<li class="breadcrumb-item"> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item"> <a href="'.url('/member').'"> '.__('site.member').' </a> </li>';
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active">'.__('account.profile').'</li>';

        $id = auth()->guard('member')->user()->id;
        $member = Member::find($id);
        if ($request->isMethod('put')) {
            $valid = Validator::make($request->all(), [
                'data.name' => 'required',
                'password' => 'confirmed'
            ], [
                'data.name.required' => 'Vui l??ng nh???p H??? T??n',
                'password.confirmed' => 'Confirm M???t kh???u kh??ng ch??nh x??c',
            ]);
            if ($valid->fails()) {
                return redirect()->back()->withErrors($valid);
            } else {
                if($request->data){
                    foreach($request->data as $field => $value){
                        $member->$field = $value;
                    }
                }
                if ($request->has('password')) {
                    if( !Hash::check($request->oldpassword,$member->password) ){
                        return redirect()->back()->with('danger','M???t kh???u c?? kh??ng ch??nh x??c');
                    }
                    $member->password = bcrypt($request->password);
                }

                $member->updated_at     = new DateTime();
                $member->save();
                return redirect()->back()->with('success','C???p nh???t d??? li???u <b>'.$member->name.'</b> th??nh c??ng');
            }
        }
        $this->_data['member'] = $member;
        return view('frontend.member.profile', $this->_data);
    }

    public function orders(){

        $this->_data['breadcrumb'] = '<li class="breadcrumb-item"> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item"> <a href="'.url('/member').'"> '.__('site.member').' </a> </li>';
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active"> '.__('account.order').' </li>';

        $id = auth()->guard('member')->user()->id;
        $this->_data['items'] = Order::where('member_id',$id)->paginate(25);
        return view('frontend.member.orders', $this->_data);
    }

    public function orderDetail($id){

        $this->_data['breadcrumb'] = '<li class="breadcrumb-item"> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item"> <a href="'.url('/member').'"> '.__('site.member').' </a> </li>';
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active"> '.__('account.order_detail').' </li>';

        $member_id = auth()->guard('member')->user()->id;
        $this->_data['item'] = Order::where('member_id',$member_id)->where('id',$id)->first();
        if ($this->_data['item'] !== null) {
            $this->_data['products'] = $this->_data['item']->details()->get();
        }
        return view('frontend.member.order_detail', $this->_data);
    }
    
}
