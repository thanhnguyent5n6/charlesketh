<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;
use App\Setting;
use App\User;
use App\Order;
use App\OrderDetail;
use App\Coupon;

use DateTime;
class CartController extends Controller
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
            $this->_data['categories'] = get_categories('san-pham',$lang);
            $this->_data['cart'] = is_array($cart = json_decode($request->cookie('cart'), true)) ? $cart : [];
            $this->_data['coupon'] = is_array($coupon = json_decode($request->cookie('coupon'), true)) ? $coupon : [];
            View::share('siteconfig', config('siteconfig'));
            return $next($request);
        });
    }
    public function index(Request $request){
        $this->_data['site']['title'] = __('site.cart');
        $this->_data['site']['class'] = 'site-cart';
        $this->_data['breadcrumb'] = '<li class="breadcrumb-item"> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active"> '.$this->_data['site']['title'].' </li>';
        return view('frontend.default.cart', $this->_data);
    }
    public function tracking(Request $request){
        if($request->has('email') || $request->has('code') ){
            $valid = Validator::make($request->all(), [
                'code' => 'required',
                'email' => 'required|email',
            ], [
                'code.required' => __('validation.required', ['attribute'=>__('Mã đơn hàng')]),
                'email.required' => __('validation.required', ['attribute'=>'Email']),
                'email.email' => __('validation.email', ['attribute'=>'Email']),
            ]);
            if ($valid->fails()) {
                return redirect()->back()->withErrors($valid)->withInput();
            } else {
                $this->_data['site']['title'] = __('site.tracking');
                $this->_data['breadcrumb'] = '<li class="breadcrumb-item"> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
                $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active"> '.$this->_data['site']['title'].' </li>';
                $this->_data['item'] = Order::where('email',$request->email)->where('code',$request->code)->first();
                if ($this->_data['item'] !== null) {
                    $this->_data['products'] = $this->_data['item']->details()->get()->toArray();
                    return view('frontend.default.tracking',$this->_data);
                } else {
                    return redirect()->route('frontend.cart.tracking')->with('danger',__('site.no_data'));
                }
                
            }
        } else {
            $this->_data['item'] = null;
        }
        return view('frontend.default.tracking',$this->_data);
        
    }
    public function checkOut(Request $request){
        $this->_data['site']['title'] = __('site.checkout');
        $this->_data['breadcrumb'] = '<li class="breadcrumb-item"> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active">'.$this->_data['site']['title'].'</li>';
        $this->_data['payments'] = DB::table('posts as A')
            ->leftjoin('post_languages as B', 'A.id', '=', 'B.post_id')
            ->select('A.id','A.link','B.title','B.slug','B.description')
            ->where('B.language',$this->_data['lang'])
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->where('A.type','payment')
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();
        if(count($this->_data['cart']) > 0) {
            return view('frontend.default.checkout', $this->_data);
        }
        return redirect()->route('frontend.home.index');
        
    }
    public function placeOrder(Request $request){
        if (count($this->_data['cart']) > 0) {
            $valid = Validator::make($request->all(), [
                'name' => 'required',
                // 'address' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                // 'province_id' => 'required',
                // 'district_id' => 'required',
            ], [
                'name.required' => __('validation.required', ['attribute'=>__('site.name')]),
                // 'address.required' => __('validation.required', ['attribute'=>__('site.address')]),
                'email.required' => __('validation.required', ['attribute'=>'Email']),
                'email.email' => __('validation.email', ['attribute'=>'Email']),
                'phone.required' => __('validation.required', ['attribute'=>__('site.phone')]),
                // 'province_id.required' => "Vui lòng chọn Tỉnh / Thành phố",
                // 'district_id.required' => "Vui lòng chọn Quận / Huyện",
            ]);
            if ($valid->fails()) {
                return redirect()->back()->withErrors($valid)->withInput();
            } else {
                $shipping = $request->shipping ? $request->shipping : 0;
                $sumCartPrice = $sumOrderPrice = 0;
                $sumProQty = 0;
                $dataInsert = [];
                foreach($this->_data['cart'] as $key => $val){
                    $sumCartPrice           += $val['price']*$val['qty'];
                    $sumProQty              += $val['qty'];
                    $dataInsert[]   = new OrderDetail([
                        'product_id'    =>  $val['id'],
                        'product_title' =>  $val['title'],
                        'product_code'  =>  $val['code'],
                        'product_qty'   =>  $val['qty'],
                        'product_price' =>  $val['price'],
                    ]);
                }
                if( count($this->_data['coupon']) > 0 ){
                    if($this->_data['coupon']['change_conditions_type'] == 'percentage_discount_from_total_cart'){
                        $sumOrderPrice = $sumCartPrice - (($this->_data['coupon']['coupon_amount']/100)*$sumCartPrice);
                    }else{
                        $sumOrderPrice = $sumCartPrice - $this->_data['coupon']['coupon_amount'];
                    }
                    DB::table('coupons')->where('id',$this->_data['coupon']['id'])->increment('used',1);
                }else{
                    $sumOrderPrice = $sumCartPrice;
                }
                if($sumOrderPrice < 0) $sumOrderPrice = 0;
                $order = Order::create([
                    'code'          =>  time(),
                    'coupon_code'   =>  $this->_data['coupon'] ? $this->_data['coupon']['code'] : null,
                    'coupon_amount' =>  $this->_data['coupon'] ? $this->_data['coupon']['coupon_amount'] : 0,
                    'name'          =>  $request->name,
                    'address'       =>  $request->address,
                    'email'         =>  $request->email,
                    'phone'         =>  $request->phone,
                    'province_id'   =>  (int)$request->province_id,
                    'district_id'   =>  (int)$request->district_id,
                    'note'          =>  $request->order_note,
                    'payment_id'    =>  (int)$request->payment,
                    'shipping'      =>  (int)$shipping,
                    'subtotal'      =>  floatval($sumCartPrice),
                    'order_qty'     =>  (int)$sumProQty,
                    'order_price'   =>  floatval($sumOrderPrice + $shipping),
                    'member_id'     =>  auth()->guard('member')->check() ? auth()->guard('member')->id() : null,
                    'status_id'     =>  1,
                    'type'          =>  'online',
                    'created_at'    => new DateTime(),
                    'updated_at'    => new DateTime(),
                ]);
                $order->code = update_code($order->id,'DH');
                $order->save();
                $order->details()->saveMany($dataInsert);
                $cookieCart = cookie('cart','', 720);
                $cookieCoupon = cookie('coupon','', 720);
                if(@config('settings.email_username') !='') Mail::to($order->email)->send(new OrderConfirmation($order));
                return redirect()->route('frontend.cart.thankyou')->with('orderCode', $order->code)->withCookie($cookieCart)->withCookie($cookieCoupon);
            }
        }else{
            return redirect()->route('frontend.home.index');
        }
    }
    public function thankYou(){
        $this->_data['site']['title'] = __('cart.continue_shopping');
        $this->_data['breadcrumb'] = '<li> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
        $this->_data['breadcrumb'] .= '<li> <a href="'.url('/san-pham').'"> '.$this->_data['site']['title'].' </a> </li>';
        $this->_data['item'] = Order::where('code',session('orderCode'))->first();
        $this->_data['products'] = $this->_data['item']->details()->get()->toArray();
        return view('frontend.default.thankyou',$this->_data);
    }
    public function checkCoupon(){

        if($this->_data['coupon']['change_conditions_type'] == 'percentage_discount_from_total_cart'){
            $sale = $this->_data['coupon']['coupon_amount'].'%';
        }else{
            $sale = get_currency_vn($this->_data['coupon']['coupon_amount']);
        }
        $data = ['type' =>'success','icon' =>'check', 'message' => __('cart.coupon_sale',['attribute'=>$sale])];
        $sumCartPrice = 0;
        foreach ($this->_data['cart'] as $key => $val) {
            $sumCartPrice += $val['price']*$val['qty'];
        }
        if( $this->_data['coupon']['used'] >= $this->_data['coupon']['number_of_uses'] ){
            $data = [
                'type' =>'danger',
                'icon' =>'warning',
                'message'   =>  __('cart.coupon_over',['code'=>$this->_data['coupon']['code']])
            ];
        }
        if( $this->_data['coupon']['min_restriction_amount'] > 0 && $sumCartPrice < $this->_data['coupon']['min_restriction_amount'] ){
            $data = [
                'type' =>'danger',
                'icon' =>'warning',
                'message'   =>  __('cart.coupon_min',['code'=>$this->_data['coupon']['code'], 'price'=>get_currency_vn($this->_data['coupon']['min_restriction_amount'])])
            ];
        }
        if( $this->_data['coupon']['max_restriction_amount'] > 0 && $sumCartPrice > $this->_data['coupon']['max_restriction_amount'] ){
            $data = [
                'type' =>'danger',
                'icon' =>'warning',
                'message'   =>  __('cart.coupon_max',['code'=>$this->_data['coupon']['code'], 'price'=>get_currency_vn($this->_data['coupon']['max_restriction_amount'])])
            ];
        }
        if( $this->_data['coupon']['begin_at'] !== null && time() < strtotime($this->_data['coupon']['begin_at']) ){
            $data = [
                'type' =>'danger',
                'icon' =>'warning',
                'message'   =>  __('cart.coupon_begin',['code'=>$this->_data['coupon']['code'], 'date'=>$this->_data['coupon']['begin_at']])
            ];
        }
        if( $this->_data['coupon']['end_at'] !== null && time() > strtotime($this->_data['coupon']['end_at']) ){
            $data = [
                'type' =>'danger',
                'icon' =>'warning',
                'message'   =>  __('cart.coupon_end',['code'=>$this->_data['coupon']['code'], 'date'=>$this->_data['coupon']['end_at']])
            ];
        }
        $this->_data['coupon']['effective'] = $data;
        return $data;
    }
    public function getTotalPrice(){
        $sumCartPrice = $sumOrderPrice = 0;
        $miniCart = '<ul>';
        $data = [];
        foreach ($this->_data['cart'] as $key => $val) {
            $sumCartPrice += $val['price']*$val['qty'];
            $miniCart .= '<li id="pro-key-'.$key.'">
                <div class="single-cart clearfix">
                    <div class="cart-image"><a href="javascript:;"><img src="'.$val['image'].'" alt="" /></a></div>
                    <div class="cart-info">
                        <h5><a href="javascript:;">'.$val['title'].'</a></h5>
                        <p>'.__('cart.price').': '.get_currency_vn($val['price'],'').'</p>
                        <p>'.__('cart.quantity').': '.$val['qty'].'</p>
                    </div>
                    <a href="#" class="delete-cart" data-ajax="key='.$key.'" >×</a>
                </div>
            </li>';
        }
        if( count($this->_data['coupon']) > 0 ){
            $data = self::checkCoupon();
            if($data['type'] == 'danger'){
                $sumOrderPrice = $sumCartPrice;
            }else{
                if($this->_data['coupon']['change_conditions_type'] == 'percentage_discount_from_total_cart'){
                    $sumOrderPrice = $sumCartPrice - (($this->_data['coupon']['coupon_amount']/100)*$sumCartPrice);
                }else{
                    $sumOrderPrice = $sumCartPrice - $this->_data['coupon']['coupon_amount'];
                }
            }
        }else{
            $sumOrderPrice = $sumCartPrice;
        }
        if($sumOrderPrice < 0) $sumOrderPrice = 0;
        $miniCart .= '</ul>';
        return ['sumCartPrice' => $sumCartPrice, 'sumOrderPrice' => $sumOrderPrice, 'miniCart' => $miniCart, 'coupon' => $data];
    }
    public function coupon(Request $request){
        if ($request->ajax()) {
            // Chưa tạo cookie cho coupon
            $this->_data['coupon'] = Coupon::where('code',$request->code)->whereRaw('FIND_IN_SET(\'publish\',status)')->first();
            if( count($this->_data['coupon']) > 0 ){
                $data = self::checkCoupon();
                $totalPrice = self::getTotalPrice();
                $cookieCoupon = cookie('coupon', json_encode($this->_data['coupon']), 720);
                return response()->json([
                    'type'  =>  $data['type'],
                    'message'   =>  $data['message'],
                    'icon'  =>  $data['icon'],
                    'sumCartPrice' => get_currency_vn($totalPrice['sumCartPrice'],''),
                    'sumOrderPrice' => get_currency_vn($totalPrice['sumOrderPrice'],'')
                ])->withCookie($cookieCoupon);
            }
            return response()->json([
                'type'  =>  'danger',
                'message'   =>  'Dữ liệu không tồn tại',
                'icon'  =>  'warning',
            ]);
        }
    }
    public function checkInCart($id,$qty){
        $flag = 0;
        $max = count($this->_data['cart']);
        for($i=0; $i<$max; $i++){
            if( $this->_data['cart'][$i]['id'] == $id ){
                $this->_data['cart'][$i]['qty'] += $qty;
                $flag = 1;
            }
        }
        if($flag) return true;
        return false;
    }
    public function addToCart(Request $request){
        $id = $request->id;
        $qty = is_numeric($request->qty) && $request->qty > 0 ? $request->qty : 1;
        if ($request->ajax() && is_numeric($id)) {
            $product = DB::table('products as A')
                ->leftjoin('product_languages as B', 'A.id','=','B.product_id')
                ->select('A.*','B.title')
                ->where('A.id',$id)
                ->where('B.language', $this->_data['lang'])
                ->first();
            if ($product !== null) {

                $promotions = config('promotions');
                if( count( $promotions ) > 0 ){
                    foreach( $promotions as $promotion ){
                        if( $promotion['coupon_amount'] <= 0) continue;
                        if( in_array($product->id, explode(',', ($promotion['product_limit'] ? $promotion['product_limit'] : '') ) ) ){
                            break;
                        }
                        if( in_array($product->id, explode(',', ($promotion['product_id'] ? $promotion['product_id'] : '') ) ) || in_array($product->category_id, explode(',', ($promotion['category_id'] ? $promotion['category_id'] : '') ) ) ){
                            if($promotion['change_conditions_type'] == 'discount_from_total_cart'){
                                $product->sale_price = $product->wholesale_price - $promotion['coupon_amount'];
                            }elseif($promotion['change_conditions_type'] == 'percentage_discount_from_total_cart' && $promotion['coupon_amount'] < 100){
                                $product->sale_price = $product->wholesale_price - (($product->wholesale_price * $promotion['coupon_amount'])/100);
                            }
                            break;
                        }
                    }
                }
        
                if (count($this->_data['cart']) > 0) {
                    $max = count($this->_data['cart']);
                    if( !self::checkInCart($id,$qty) ){
                        $this->_data['cart'][$max] = [
                            'id' => $id,
                            'title' => $product->title,
                            'code' => $product->code,
                            'price' => $product->sale_price > 0 ? $product->sale_price : $product->wholesale_price,
                            'qty' => $qty,
                            'image' =>  $product->image ? asset('uploads/products/'.get_thumbnail($product->image)) : asset('noimage/300x300')
                        ];
                    }
                }else{
                    $this->_data['cart'][0] = [
                        'id' => $id,
                        'title' => $product->title,
                        'code' => $product->code,
                        'price' => $product->sale_price > 0 ? $product->sale_price : $product->wholesale_price,
                        'qty' => $qty,
                        'image' =>  $product->image ? asset('uploads/products/'.get_thumbnail($product->image)) : asset('noimage/300x300'),
                    ];
                }
                $totalPrice = self::getTotalPrice();
                $countCart = count($this->_data['cart']);
                $cookieCart = cookie('cart', json_encode($this->_data['cart']), 720);
                $cookieCoupon = cookie('coupon', json_encode($this->_data['coupon']), 720);
                
                return response()->json([
                    'type'    =>  'success',
                    'title' =>  '',
                    'message' => __('cart.added'),
                    'countCart' => $countCart,
                    'sumCartPrice' => get_currency_vn($totalPrice['sumCartPrice'],''),
                    'sumOrderPrice' => get_currency_vn($totalPrice['sumOrderPrice'],''),
                    'miniCart'  =>  $totalPrice['miniCart']
                ])->withCookie($cookieCart)->withCookie($cookieCoupon);
            }else{
                return response()->json([
                    'type'    =>  'warning',
                    'title' =>  '',
                    'message' => __('cart.failing')
                ]);
            }
        }else{
            return response()->json([
                'type'    =>  'warning',
                'title' =>  '',
                'message' => __('cart.failing')
            ]);
        }
    }
    public function updateCart(Request $request){
        $key = $request->key;
        $qty = $request->qty;
        if ($request->ajax() && is_numeric($key) && is_numeric($qty)) {
            if (isset($this->_data['cart'][$key]) && $qty > 0) {
                $this->_data['cart'][$key]['qty'] = $qty;
                $this->_data['cart'][$key]['sumProPrice'] = $this->_data['cart'][$key]['qty'] * $this->_data['cart'][$key]['price'];
            } elseif (isset($this->_data['cart'][$key])) {
                unset($this->_data['cart'][$key]);
            }
            $totalPrice = self::getTotalPrice();
            $countCart = count($this->_data['cart']);
            $cookieCart = cookie('cart', json_encode($this->_data['cart']), 720);
            $cookieCoupon = cookie('coupon', json_encode($this->_data['coupon']), 720);
            
            return response()->json([
                'key' => $key,
                'type'    =>  'success',
                'title' =>  '',
                'message' => __('cart.updated'),
                'sumProPrice'  => get_currency_vn($this->_data['cart'][$key]['sumProPrice'],''),
                'countCart' => $countCart,
                'sumCartPrice' => get_currency_vn($totalPrice['sumCartPrice'],''),
                'sumOrderPrice' => get_currency_vn($totalPrice['sumOrderPrice'],''),
                'miniCart'  =>  $totalPrice['miniCart'],
                'coupon'  =>  $totalPrice['coupon']
            ])->withCookie($cookieCart)->withCookie($cookieCoupon);
        }
    }
    public function deleteCart(Request $request){
        $key = $request->key;
        if ($request->ajax() && is_numeric($key)) {
            if ( isset($this->_data['cart'][$key]) ) {
                unset($this->_data['cart'][$key]);
            }
            $totalPrice = self::getTotalPrice();
            $countCart = count($this->_data['cart']);
            $cookieCart = cookie('cart', json_encode($this->_data['cart']), 720);
            $cookieCoupon = cookie('coupon', json_encode($this->_data['coupon']), 720);
            
            return response()->json([
                'key' => $key,
                'type'    =>  'success',
                'title' =>  '',
                'message' => __('cart.deleted'),
                'countCart' => $countCart,
                'sumCartPrice' => get_currency_vn($totalPrice['sumCartPrice'],''),
                'sumOrderPrice' => get_currency_vn($totalPrice['sumOrderPrice'],''),
                'miniCart'  =>  $totalPrice['miniCart'],
                'coupon'  =>  $totalPrice['coupon']
            ])->withCookie($cookieCart)->withCookie($cookieCoupon);
        }else{
            return response()->json([
                'type'    =>  'error',
                'title' =>  '',
                'message' => __('cart.not_exist')
            ]);
        }
    }
    public function deleteAll(Request $request){
        $cookieCart = cookie('cart','', 720);
        $cookieCoupon = cookie('coupon','', 720);
        return redirect()->route('frontend.cart.index')->withCookie($cookieCart)->withCookie($cookieCoupon);
    }

    public function miniCart(){
        if(count($this->_data['cart']) > 0) {
            $totalPrice = self::getTotalPrice();
            $countCart = count($this->_data['cart']);
            $cookieCart = cookie('cart', json_encode($this->_data['cart']), 720);
            $cookieCoupon = cookie('coupon', json_encode($this->_data['coupon']), 720);
            return response()->json([
                'countCart' => $countCart,
                'sumCartPrice' => get_currency_vn($totalPrice['sumCartPrice'],''),
                'sumOrderPrice' => get_currency_vn($totalPrice['sumOrderPrice'],''),
                'miniCart'  =>  $totalPrice['miniCart']
            ])->withCookie($cookieCart)->withCookie($cookieCoupon);
        }else{
            return response()->json([
                'countCart' => 0,
                'sumCartPrice' => 0,
                'sumOrderPrice' => 0,
                'miniCart'  =>  ''
            ]);
        }
    }
    
}