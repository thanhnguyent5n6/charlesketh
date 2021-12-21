<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class WishListController extends Controller
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
            $this->_data = set_type($request->type,$lang);
            $this->_data['lang'] = $lang;
            $this->_data['meta_seo'] = set_meta_tags($lang);
            View::share('siteconfig', config('siteconfig'));
            $this->_data['wishlist'] = is_array($wishlist = json_decode($request->cookie('wishlist'), true)) ? $wishlist : [];
            if (count($this->_data['wishlist']) > 0) {
                $this->_data['countWishList'] = count($this->_data['wishlist']);
                
            }else{
                $this->_data['countWishList'] = 0;
            }
            $cart = is_array($cart = json_decode($request->cookie('cart'), true)) ? $cart : [];
            if (count($cart) > 0) {
                $this->_data['countCart'] = count($cart);
                
            }else{
                $this->_data['countCart'] = 0;
            }
            return $next($request);
        });
        
    }

    public function index(Request $request){
        $this->_data['site']['title'] = __('site.wishlist');
        $this->_data['breadcrumb'] = '<li> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
        $this->_data['breadcrumb'] .= '<li> <a href="'.url('/gio-hang').'"> '.$this->_data['site']['title'].' </a> </li>';
        if( count($this->_data['wishlist']) > 0 ){
            $this->_data['products'] = DB::table('products as A')
                ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
                ->select('A.id','A.code','A.regular_price','A.sale_price','A.link','A.image','A.alt','A.category_id','A.user_id','A.type','B.title', 'B.slug')
                ->where('B.language',$this->_data['lang'])
                ->whereIn('A.id',$this->_data['wishlist'])
                ->orderBy('A.priority','asc')
                ->orderBy('A.id','desc')
                ->paginate(config('settings.product_per_page'));
        }else{
            $this->_data['products'] = [];
        }
        return view('frontend.default.products', $this->_data);
    }

    public function addToWishList(Request $request){
        $id = $request->id;
        if ($request->ajax() && is_numeric($id)) {
            if( !in_array($id,$this->_data['wishlist']) ){
                array_unshift($this->_data['wishlist'],$id);
            }
            $countWishList = count($this->_data['wishlist']);
            $cookieWishList = cookie('wishlist', json_encode($this->_data['wishlist']), 1440);
            return response()->json([
                    'type'    =>  'success',
                    'title' =>  '',
                    'message' => __('cart.wishlist_added'),
                    'countWishList' => $countWishList
                ])->withCookie($cookieWishList);
        }else{
            return response()->json([
                'type'    =>  'warning',
                'title' =>  '',
                'message' => __('cart.failing')
            ]);
        }
    }

    public function deleteWishList(Request $request){
        $key = $request->key;
        if ($request->ajax() && is_numeric($key)) {
            if ( isset($this->_data['wishlist'][$key]) ) {
                unset($this->_data['wishlist'][$key]);
            }
            $countWishList = count($this->_data['wishlist']);
            $cookieWishList = cookie('wishlist', json_encode($this->_data['wishlist']), 720);
            return response()->json([
                'key' => $key,
                'type'    =>  'success',
                'title' =>  '',
                'message' => __('cart.wishlist_deleted'),
                'countWishList' => $countWishList
            ])->withCookie($cookieWishList);
        }else{
            return response()->json([
                'type'    =>  'error',
                'title' =>  '',
                'message' => __('cart.not_exist')
            ]);
        }
    }

    public function deleteAll(Request $request){
        $cookieWishList = cookie('wishlist', '', 720);
        return redirect()->route('frontend.home.wishlist')->withCookie($cookieWishList);
    }

    
}
