<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;

// use Illuminate\Support\Facades\Log;

use App\Category;
use App\CategoryLanguage;
use Cache;
use Session;
use Carbon\Carbon;

class HomeController extends Controller
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
            $this->_data['suppliers'] = get_suppliers();
            $this->_data['bannerTop'] = get_photos('banner-top');
            View::share('siteconfig', config('siteconfig'));
            $this->_data['priceService'] = session('price-service') && session('price-service') == 'an' ? session('price-service','hien') : session('price-service','an');
            return $next($request);
        });
		if (!$request->server('HTTP_USER_AGENT')) {
		    return abort(400);
		}
		// dd($request->server());
    }

    public function index(Request $request){

        $this->_data['category_tops'] = DB::table('categories as A')
            ->leftjoin('category_languages as B', 'A.id', '=', 'B.category_id')
            ->select('A.*','B.title','B.slug')
            ->whereRaw('FIND_IN_SET(\'publish\',A.status) AND FIND_IN_SET(\'index\',A.status)')
            ->where('B.language',$this->_data['lang'])
            ->where('A.type','san-pham')
            ->orderBy('A.priority','asc')
            ->limit(2)
            ->get();
        $this->_data['category_bottom'] = DB::table('categories as A')
            ->leftjoin('category_languages as B', 'A.id', '=', 'B.category_id')
            ->select('A.*','B.title','B.slug')
            ->whereRaw('FIND_IN_SET(\'publish\',A.status) AND FIND_IN_SET(\'index\',A.status)')
            ->where('B.language',$this->_data['lang'])
            ->where('A.type','san-pham')
            ->orderBy('A.priority','asc')
            ->limit(4)
            ->offset(2)
            ->get();

        $this->_data['products'] = DB::table('products as A')
            ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
            ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
            ->select('A.*','B.title','B.slug','B.description','B.attributes','C.name as supplier')
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->where('B.language',$this->_data['lang'])
            ->where('A.type','san-pham')
            ->orderBy('A.wholesale_price','asc')
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->limit(8)
            ->get();

        $this->_data['posts'] = DB::table('posts as A')
            ->leftjoin('post_languages as B', 'A.id', '=', 'B.post_id')
            ->select('A.*','B.title','B.slug','B.description')
            ->where('B.language',$this->_data['lang'])
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->where('A.type','tin-tuc')
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->limit(10)
            ->get();
        return view('frontend.default.index', $this->_data);
    }

    public function laygia(){
        $this->_data['site']['title'] = __('Lấy giá sản phẩm');
        $this->_data['breadcrumb'] = '<li class="breadcrumb-item"> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item"> <a href="'.url('/lay-gia-san-pham').'"> '.$this->_data['site']['title'].' </a> </li>';
        return view('frontend.default.laygia', $this->_data);
    }

    public function sitemapIndex(){
        SitemapIndex::create()
        ->add(url('/category-sitemap.xml'))
        ->add(url('/product-sitemap.xml'))
        ->add(url('/post-sitemap.xml'))
        ->writeToFile(public_path('sitemap.xml'));
        return response(file_get_contents(public_path('sitemap.xml')), 200, ['Content-Type' => 'application/xml']);
    }

    public function sitemap($slug){
        $sitemap = Sitemap::create();
        if($slug == 'category'){
            foreach($this->_data['categories'] as $category){
                $sitemap->add(route('frontend.home.product',['slug'=>$category->slug]));
            }
        }
        if($slug == 'product'){
            $products = DB::table('products as A')
                ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
                ->select('A.*','B.slug')
                ->where('B.language','vi')
                ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                ->where('A.type','san-pham')
                ->orderBy('A.priority','asc')
                ->orderBy('A.id','desc')
                ->get();;
            foreach($products as $product){
                $sitemap->add(route('frontend.home.product',['slug'=>$product->slug]));
            }
        }
        if($slug == 'post'){
            $posts = DB::table('posts as A')
                ->leftjoin('post_languages as B', 'A.id', '=', 'B.post_id')
                ->select('A.type','B.slug')
                ->where('B.language','vi')
                ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                ->orderBy('A.priority','asc')
                ->orderBy('A.id','desc')
                ->get();
            foreach($posts as $post){
                $sitemap->add(route('frontend.home.page',['type'=>$post->type,'slug'=>$post->slug]));
            }
        }
        $sitemap->writeToFile(public_path($slug.'-sitemap.xml'));
        return response(file_get_contents(public_path($slug.'-sitemap.xml')), 200, ['Content-Type' => 'application/xml']);
    }

    public function coupon(){
        $this->_data['site']['title'] = __('Coupon');
        $this->_data['breadcrumb'] = '<li class="breadcrumb-item"> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item"> <a href="'.url('/coupon').'"> '.$this->_data['site']['title'].' </a> </li>';
        
        return view('frontend.default.coupon',$this->_data);
    }

    public function static($supplier,$filter){
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active"> Sản phẩm </li>';

        $supplier = DB::table('suppliers')
            ->whereRaw('FIND_IN_SET(\'publish\',status)')
            ->where('slug',$supplier)
            ->where('type','default')
            ->first();

        $filter = DB::table('categories as A')
            ->leftjoin('category_languages as B', 'A.id', '=', 'B.category_id')
            ->select('A.*','B.title','B.slug')
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->where('B.language',$this->_data['lang'])
            ->where('B.slug',$filter)
            ->where('A.type','filter')
            ->first();
            
        if( $supplier && $filter ){
            $this->_data['products'] = DB::table('products as A')
                ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
                ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
                ->select('A.*','B.title','B.slug','B.description','B.attributes','C.name as supplier')
                ->where('B.language',$this->_data['lang'])
                ->whereRaw('FIND_IN_SET(\''.$filter->id.'\',A.filters) AND FIND_IN_SET(\'publish\',A.status)')
                ->where('A.supplier_id',$supplier->id)
                ->where('A.type','san-pham')
                ->orderBy('A.wholesale_price','asc')
                ->orderBy('A.priority','asc')
                ->orderBy('A.id','desc')
                ->paginate(config('settings.product_per_page') ? config('settings.product_per_page') : 24);

            return view('frontend.default.products',$this->_data);
        }
        return abort(404);
    }

    public function contact(){
        $this->_data['site']['title'] = __('site.contact');
        $this->_data['breadcrumb'] = '<li class="breadcrumb-item"> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item"> <a href="'.url('/lien-he').'"> '.$this->_data['site']['title'].' </a> </li>';
        $this->_data['contact'] = get_pages('lien-he',$this->_data['lang']);
        if( $this->_data['contact'] && $this->_data['contact']->meta_seo !='' ){
            $current_seo = json_decode($this->_data['contact']->meta_seo);
            $current_seo->title ? $this->_data['meta_seo']->title = $current_seo->title : '';
            $current_seo->keywords ? $this->_data['meta_seo']->keywords = $current_seo->keywords : '';
            $current_seo->description ? $this->_data['meta_seo']->description = $current_seo->description : '';
        }

        if(@config('settings.google_coordinates')){
            $coordinates = str_replace(['(',')'],'',config('settings.google_coordinates'));
            $coordinates = explode(', ',$coordinates);
        }else{
            $coordinates = explode(', ',config('siteconfig.general.google_coordinates'));
        }
        
        return view('frontend.default.contact',$this->_data);
    }

    public function search(Request $request, $slug){
        $this->_data['site']['title'] = __('site.search');
        $this->_data['site']['class'] = 'site-search';

        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active"> '.$this->_data['site']['title'].' </li>';

        if( $request->ajax() ){
        	$data = '<ul>';
        	$products = DB::table('products as A')
	            ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
	            ->select('A.*','B.title','B.slug')
	            ->whereRaw('(B.title LIKE \'%'.$slug.'%\' OR B.slug LIKE \'%'.str_slug($slug).'%\') AND FIND_IN_SET(\'publish\',A.status)')
	            ->where('B.language',$this->_data['lang'])
                ->where('A.type','san-pham')
	            ->orderBy('A.wholesale_price','asc')
	            ->orderBy('A.priority','asc')
	            ->orderBy('A.id','desc')
	            ->limit(4)
	            ->get();

	        $categories = DB::table('categories as A')
	            ->leftjoin('category_languages as B', 'A.id', '=', 'B.category_id')
	            ->select('A.*','B.title','B.slug')
	            ->whereRaw('(B.title LIKE \'%'.$slug.'%\' OR B.slug LIKE \'%'.str_slug($slug).'%\') AND FIND_IN_SET(\'publish\',A.status)')
	            ->where('B.language',$this->_data['lang'])
                ->where('A.type','san-pham')
	            ->orderBy('A.priority','asc')
	            ->orderBy('A.id','desc')
	            ->limit(4)
	            ->get();

	        $attributes = DB::table('attributes as A')
	            ->leftjoin('attribute_languages as B', 'A.id', '=', 'B.attribute_id')
	            ->select('A.*','B.title','B.slug')
	            ->whereRaw('(B.title LIKE \'%'.$slug.'%\' OR B.slug LIKE \'%'.str_slug($slug).'%\') AND FIND_IN_SET(\'publish\',A.status)')
	            ->where('B.language',$this->_data['lang'])
                ->where('A.type','product_tags')
	            ->orderBy('A.priority','asc')
	            ->orderBy('A.id','desc')
	            ->limit(4)
	            ->get();
            
            if($products){
                foreach( $products as $product ){
                    $data .= '<li>'.get_template_product_search($product,'san-pham',1,'').'</li>';
                }
            }

            if($categories){
                foreach( $categories as $category ){
                	$link = url('/'.$category->slug);
                    $data .= '<li> <a href="'.$link.'"><i class="pe-7s-albums"></i> '.$category->title.'</a></li>';
                }
            }
            
            if($attributes){
                foreach( $attributes as $attribute ){
            		$link = url('/'.$attribute->slug);
                    $data .= '<li> <a href="'.$link.'"><i class="pe-7s-ticket"></i> '.$attribute->title.'</a></li>';
                }
            }

            if( strlen($data) < 10 ){
                $data .= '<li> <a href="javascript:;">Không tìm thấy dữ liệu</a></li>';
            }

            $data .= '</ul>';
            Session::put('search',$slug);
            return response()->json(['data'=>$data]);
        }
        $name = Session::get('search') ? str_slug(Session::get('search')) != $slug ? $slug : Session::get('search') : $slug;

        $this->_data['breadcrumb'] .= $name ? '<li class="breadcrumb-item active"> '.$name.' </li>' : '';

        $idTags = DB::table('attribute_languages')
            ->whereRaw('(title LIKE \'%'.$name.'%\' OR slug LIKE \'%'.str_slug($slug).'%\')')
            ->where('language',$this->_data['lang'])
            ->pluck('attribute_id');
        
        $whereRaw = '(B.title LIKE \'%'.$name.'%\' OR B.slug LIKE \'%'.str_slug($slug).'%\') AND FIND_IN_SET(\'publish\',A.status)';
        if($idTags){
        	$idProducts = DB::table('product_attribute')->whereIn('attribute_id',$idTags)->pluck('product_id')->toArray();
            if($idProducts){
        		$whereRaw = '(B.title LIKE \'%'.$name.'%\' OR B.slug LIKE \'%'.str_slug($slug).'%\' OR A.id IN ('.implode(',', $idProducts).')) AND FIND_IN_SET(\'publish\',A.status)';
        	}
        }

        $this->_data['products'] = DB::table('products as A')
            ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
            ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
            ->select('A.*','B.title','B.slug','B.description','B.attributes','C.name as supplier')
            ->where('B.language',$this->_data['lang'])
            ->whereRaw($whereRaw)
            ->where('A.type','san-pham')
            ->orderBy('A.wholesale_price','asc')
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->paginate(config('settings.product_per_page') ? config('settings.product_per_page') : 24);

        return view('frontend.default.products',$this->_data);
    }

    public function promotion(){
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active"> Khuyến mãi </li>';
        $promotions = config('promotions');
        if( count( $promotions ) > 0 ){
        	$categories = [];
        	$products = [];
        	$limits = [];
            foreach( $promotions as $promotion ){
                if( $promotion['coupon_amount'] <= 0) continue;
                if( $promotion['category_id'] ){
                	$categories = array_unique(array_merge($categories,explode(',',$promotion['category_id'])));
                }
                if( $promotion['product_id'] ){
                	$products = array_unique(array_merge($products,explode(',',$promotion['product_id'])));
                }
                if( $promotion['product_limit'] ){
                	$limits = array_unique(array_merge($limits,explode(',',$promotion['product_limit'])));
                }

            }

            $this->_data['products'] = DB::table('products as A')
            ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
            ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
            ->select('A.*','B.title','B.slug','B.description','B.attributes','C.name as supplier')
            ->where('B.language',$this->_data['lang'])
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->where(function($query) use ($categories, $products){
                if($categories){
                    $query->whereIn('A.category_id',$categories);
                }
                if($categories){
                    $query->orWhereIn('A.product_id',$products);
                }
            })
            ->whereNotIn('A.product_id',$limits)
            ->where('A.type','san-pham')
            ->orderBy('A.wholesale_price','asc')
            ->orderBy('A.id','desc')
            ->paginate(config('settings.product_per_page') ? config('settings.product_per_page') : 24);

        }else{
        	$this->_data['products'] = [];
        }
        return view('frontend.default.products',$this->_data);
    }

    public function supplier($slug){
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active"> Hãng sản xuất </li>';

        $this->_data['supplier'] = DB::table('suppliers')
            ->whereRaw('FIND_IN_SET(\'publish\',status)')
            ->where('slug',$slug)
            ->where('type','default')
            ->first();

        if( $this->_data['supplier'] && @$this->_data['supplier']->meta_seo !='' ){
            $current_seo = json_decode($this->_data['supplier']->meta_seo);
            $current_seo->title ? $this->_data['meta_seo']->title = $current_seo->title : '';
            $current_seo->keywords ? $this->_data['meta_seo']->keywords = $current_seo->keywords : '';
            $current_seo->description ? $this->_data['meta_seo']->description = $current_seo->description : '';
        }

        if( $this->_data['supplier'] ){
            $supplier_id = $this->_data['supplier']->id;
            $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active"> '.$this->_data['supplier']->name.' </li>';

            $this->_data['products'] = DB::table('products as A')
                ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
                ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
                ->select('A.*','B.title','B.slug','B.description','B.attributes','C.name as supplier')
                ->where('B.language',$this->_data['lang'])
                ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                ->where('A.supplier_id',$supplier_id)
                ->where('A.type','san-pham')
                ->orderBy('A.wholesale_price','asc')
                ->orderBy('A.priority','asc')
                ->orderBy('A.id','desc')
                ->paginate(config('settings.product_per_page') ? config('settings.product_per_page') : 24);

            return view('frontend.default.products',$this->_data);
        }
        return abort(404);
    }

    public function product(Request $request, $slug){
        $arrSlug = explode('-', $slug);
        $supplier_slug = end($arrSlug);

        $supplier = DB::table('suppliers')
            ->where('slug',$supplier_slug)
            ->whereRaw('FIND_IN_SET(\'publish\',status)')
            ->where('type','default')
            ->first();

        if($supplier){
            $this->_data['supplier'] = $supplier;
            $slug = str_replace('-'.$supplier_slug, '', $slug);
        }

        $this->_data['category'] = DB::table('categories as A')
            ->leftjoin('category_languages as B', 'A.id','=','B.category_id')
            ->select('A.*', 'B.title', 'B.slug', 'B.contents', 'B.meta_seo')
            ->where('B.language', $this->_data['lang'])
            ->where('B.slug',$slug)
            ->where('A.type','san-pham')
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->first();

        if( $this->_data['category'] ){

            $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active"> '.$this->_data['category']->title.' </li>';
            $category_id = $this->_data['category']->id;
            $filter_id = $this->_data['category']->filters ? explode(',',$this->_data['category']->filters) : [];
            $supplier_id = $this->_data['category']->suppliers ? explode(',',$this->_data['category']->suppliers) : [];

            $children = Category::find($this->_data['category']->id)->children()->pluck('id')->toArray();
            $children[] = $category_id;

            $this->_data['subCategory'] = DB::table('categories')->where('parent',$category_id)->where('type','san-pham')->whereRaw('FIND_IN_SET(\'publish\',status)')->count();

            if( $this->_data['category']->meta_seo !='' && !$supplier ){
                $current_seo = json_decode($this->_data['category']->meta_seo);
                $current_seo->title ? $this->_data['meta_seo']->title = $current_seo->title : '';
                $current_seo->keywords ? $this->_data['meta_seo']->keywords = $current_seo->keywords : '';
                $current_seo->description ? $this->_data['meta_seo']->description = $current_seo->description : '';
            }

            if( count($filter_id) > 0 ){
                $filters = DB::table('categories as A')
                    ->leftjoin('category_languages as B', 'A.id', '=', 'B.category_id')
                    ->select('A.*','B.title','B.slug')
                    ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                    ->where('B.language',$this->_data['lang'])
                    ->where(function($query) use ($filter_id){
                        $query->whereIn('A.id',$filter_id)->orWhereIn('A.parent',$filter_id);
                    })
                    ->where('A.type','filter')
                    ->orderBy('A.priority','asc')
                    ->get();

                if( $filters !== null ){
                    foreach($filters as $filter){
                        $parent=$filter->parent;
                        $this->_data['filters'][$parent][]=$filter;
                    }
                }
            }else{
                $this->_data['filters'] = [];
            }

            if( count($supplier_id) > 0 ){
                $this->_data['suppliers'] = DB::table('suppliers')
                    ->whereIn('id',$supplier_id)
                    ->whereRaw('FIND_IN_SET(\'publish\',status)')
                    ->where('type','default')
                    ->orderBy('priority','asc')
                    ->get();
            }

            $this->_data['products'] = DB::table('products as A')
                ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
                ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
                ->select('A.*','B.title','B.slug','B.description','B.attributes','C.name as supplier')
                ->where('B.language',$this->_data['lang'])
                ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                ->whereIn('A.category_id',$children)
                ->where(function($query) use ($supplier){
                    if($supplier){
                        $query->where('A.supplier_id',$supplier->id);
                    }
                })
                ->where('A.type','san-pham')
                ->orderBy('A.wholesale_price','asc')
                ->orderBy('A.id','desc')
                ->paginate(config('settings.product_per_page') ? config('settings.product_per_page') : 20);
            if($category_id == 345){
            	return response()->view('frontend.default.product_cats',$this->_data);
            }else{
            	return response()->view('frontend.default.products',$this->_data);
            }
        }

        $this->_data['tag'] = DB::table('attributes as A')
            ->leftjoin('attribute_languages as B', 'A.id','=','B.attribute_id')
            ->select('A.*', 'B.title', 'B.slug', 'B.contents', 'B.meta_seo')
            ->where('B.language', $this->_data['lang'])
            ->where('B.slug',$slug)
            ->where('A.type','product_tags')
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->first();

        if( $this->_data['tag'] ){
            $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active"> '.$this->_data['tag']->title.' </li>';
            $tag_id = $this->_data['tag']->id;
            $idProducts = DB::table('product_attribute')->where('attribute_id',$tag_id)->pluck('product_id')->toArray();

            if( $this->_data['tag']->meta_seo !='' ){
                $current_seo = json_decode($this->_data['tag']->meta_seo);
                $current_seo->title ? $this->_data['meta_seo']->title = $current_seo->title : '';
                $current_seo->keywords ? $this->_data['meta_seo']->keywords = $current_seo->keywords : '';
                $current_seo->description ? $this->_data['meta_seo']->description = $current_seo->description : '';
            }

            $this->_data['products'] = DB::table('products as A')
                ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
                ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
                ->select('A.*','B.title','B.slug','B.description','B.attributes','C.name as supplier')
                ->where('B.language',$this->_data['lang'])
                ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                ->whereIn('A.id',$idProducts)
                ->where('A.type','san-pham')
                ->orderBy('A.wholesale_price','asc')
                ->orderBy('A.id','desc')
                ->paginate(config('settings.product_per_page') ? config('settings.product_per_page') : 24);

            return response()->view('frontend.default.products',$this->_data);
        }


        $promotions = config('promotions');
        if( count( $promotions ) > 0 ){
            
            $categories = [];
            $products = [];
            $limits = [];
            $flag = 0;
            foreach( $promotions as $promotion ){
                if( $promotion['coupon_amount'] <= 0) continue;
                if( $promotion['slug'] == $slug ){
                    $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active">'.$promotion['title'].'</li>';
                    if( $promotion['category_id'] ){
                        $categories = array_unique(array_merge($categories,explode(',',$promotion['category_id'])));
                    }
                    if( $promotion['product_id'] ){
                        $products = array_unique(array_merge($products,explode(',',$promotion['product_id'])));
                    }
                    if( $promotion['product_limit'] ){
                        $limits = array_unique(array_merge($limits,explode(',',$promotion['product_limit'])));
                    }
                    $flag = 1;
                    break;
                }

            }
            if($flag){
	            $this->_data['products'] = DB::table('products as A')
	            ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
	            ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
                ->select('A.*','B.title','B.slug','B.description','B.attributes','C.name as supplier')
                ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
	            ->where('B.language',$this->_data['lang'])
	            ->where(function($query) use ($categories, $products){
	                if($categories){
	                    $query->whereIn('A.category_id',$categories);
	                }
	                if($categories){
	                    $query->orWhereIn('A.product_id',$products);
	                }
	            })
                ->whereNotIn('A.product_id',$limits)
	            ->where('A.type','san-pham')
	            ->orderBy('A.wholesale_price','asc')
	            ->orderBy('A.id','desc')
	            ->paginate(config('settings.product_per_page') ? config('settings.product_per_page') : 24);
	            return response()->view('frontend.default.products',$this->_data);
            }
        }

        $this->_data['product'] = DB::table('products as A')
            ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
            ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
            ->select('A.*','B.title','B.description','B.contents','B.specifications','B.attributes','B.meta_seo','C.name as supplier')
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->where('B.language',$this->_data['lang'])
            ->where('B.slug',$slug)
            ->where('A.type','san-pham')
            ->first();
        if( $this->_data['product'] ){
        	// Log::info('User failed to login.', $request->server());
            if( $this->_data['product']->meta_seo !='' ){
                $current_seo = json_decode($this->_data['product']->meta_seo);
                $current_seo->title ? $this->_data['meta_seo']->title = $current_seo->title : '';
                $current_seo->keywords ? $this->_data['meta_seo']->keywords = $current_seo->keywords : '';
                $current_seo->description ? $this->_data['meta_seo']->description = $current_seo->description : '';
                $this->_data['product']->image ? $this->_data['meta_seo']->image = asset('uploads/products/'.get_thumbnail($this->_data['product']->image, '_medium')) : '';
            }

            $promotions = config('promotions');
            if( count( $promotions ) > 0 ){
                foreach( $promotions as $promotion ){
                    if( $promotion['coupon_amount'] <= 0) continue;
                    if( in_array($this->_data['product']->id, explode(',', ($promotion['product_limit'] ? $promotion['product_limit'] : '') ) ) ){
                        break;
                    }
                    if( in_array($this->_data['product']->id, explode(',', ($promotion['product_id'] ? $promotion['product_id'] : '') ) ) || in_array($this->_data['product']->category_id, explode(',', ($promotion['category_id'] ? $promotion['category_id'] : '') ) ) ){
                        if($promotion['change_conditions_type'] == 'discount_from_total_cart'){
                            $this->_data['product']->sale_price = $this->_data['product']->wholesale_price - $promotion['coupon_amount'];
                        }elseif($promotion['change_conditions_type'] == 'percentage_discount_from_total_cart' && $promotion['coupon_amount'] < 100){
                            $this->_data['product']->sale_price = $this->_data['product']->wholesale_price - (($this->_data['product']->wholesale_price * $promotion['coupon_amount'])/100);
                        }
                        break;
                    }
                }
            }

            $client_ip = $request->getClientIp();
            if(!Cache::has($client_ip.'_product_view_'.$this->_data['product']->id)){
                $this->_data['product']->viewed += 1;
                DB::table('products')->where('id',$this->_data['product']->id)->increment('viewed',1);
                Cache::add($client_ip.'_product_view_'.$this->_data['product']->id,$this->_data['product']->viewed,5);
            }
            $viewed = is_array($viewed = json_decode($request->cookie('viewed'), true)) ? $viewed : [];
            if( !in_array($this->_data['product']->id,$viewed) ){
                array_unshift($viewed,$this->_data['product']->id);
            }
            $cookieViewed = cookie('viewed', json_encode($viewed), 1440);

            $this->_data['images'] = get_media($this->_data['product']->attachments);
            $this->_data['attributes'] = $this->_data['product']->attributes ? json_decode($this->_data['product']->attributes,true) : [];
            $this->_data['colors'] = get_attributes('product_colors');
            $this->_data['sizes'] = get_attributes('product_sizes');
            $this->_data['tags'] = get_attributes('product_tags');
            $this->_data['category'] = CategoryLanguage::where('language',$this->_data['lang'])->where('id',$this->_data['product']->category_id)->first();

            if( $this->_data['product']->filters ){
                $filter_id = explode(',',$this->_data['product']->filters);
                $filters = DB::table('categories as A')
                    ->leftjoin('category_languages as B', 'A.id', '=', 'B.category_id')
                    ->select('A.*','B.title','B.slug')
                    ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                    ->where('B.language',$this->_data['lang'])
                    ->where(function($query) use ($filter_id){
                        $query->whereIn('A.id',$filter_id)->orWhereIn('A.parent',$filter_id);
                    })
                    ->where('A.type','filter')
                    ->orderBy('A.priority','asc')
                    ->get();

                if( $filters !== null ){
                    foreach($filters as $filter){
                        $name = CategoryLanguage::where('language',$this->_data['lang'])->where('id',$filter->parent)->first()->title;
                        $this->_data['filters'][$name][]=$filter;
                    }
                }
            } else {
                $this->_data['filters'] = [];
            }

            $comments = DB::table('comments')
                ->whereRaw('FIND_IN_SET(\'publish\',status)')
                ->where('product_id',$this->_data['product']->id)
                ->orderBy('parent','asc')
                ->orderBy('id','desc')
                ->get();
            if($comments !== null){
                foreach($comments as $value){
                    $parent=$value->parent;
                    $this->_data['comments'][$parent][]=$value;
                }
            }else{
                $this->_data['comments'] = [];
            }
            $this->_data['countComment'] = count($comments);

            if( $this->_data['product']->related_to && $this->_data['product']->category_id == 12 ){
                $this->_data['relatedTo'] = DB::table('products as A')
                    ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
                    ->whereIn('A.id',explode(',',$this->_data['product']->related_to))
                    ->select('A.*','B.slug')
                    ->orderBy('A.priority','asc')
                    ->orderBy('A.id','desc')
                    ->get();
            } else {
                $this->_data['relatedTo'] = [];
            }

            $this->_data['products'] = DB::table('products as A')
                ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
                ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
                ->select('A.*','B.title', 'B.slug', 'B.attributes', 'C.name as supplier')
                ->where('B.language',$this->_data['lang'])
                ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                ->where('A.id','!=',$this->_data['product']->id)
                ->where('A.category_id',$this->_data['product']->category_id)
                ->where('A.type','san-pham')
                ->orderBy('A.wholesale_price','asc')
                ->orderBy('A.priority','asc')
                ->orderBy('A.id','desc')
                ->limit(15)
                ->get();
            $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active"> '.$this->_data['product']->title.' </li>';
            return response()->view('frontend.default.page-product',$this->_data)->cookie($cookieViewed);
        }
        return abort(404);

    }

    public function category($type,$slug){
        $this->_data['category'] = DB::table('categories as A')
            ->leftjoin('category_languages as B', 'A.id','=','B.category_id')
            ->select('A.*', 'B.title', 'B.slug', 'B.contents', 'B.meta_seo')
            ->where('B.language', $this->_data['lang'])
            ->where('B.slug',$slug)
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->where('A.type',$type)
            ->first();

        if( $this->_data['category'] && $this->_data['category']->meta_seo !='' ){
            $current_seo = json_decode($this->_data['category']->meta_seo);
            $current_seo->title ? $this->_data['meta_seo']->title = $current_seo->title : '';
            $current_seo->keywords ? $this->_data['meta_seo']->keywords = $current_seo->keywords : '';
            $current_seo->description ? $this->_data['meta_seo']->description = $current_seo->description : '';
        }

        if( $this->_data['category'] ){
            $category_id = $this->_data['category']->id;
            
            $this->_data['breadcrumb'] .= '<li class="breadcrumb-item active"> '.$this->_data['category']->title.' </li>';
            if($this->_data['template'] == 'product'){
                $this->_data['products'] = DB::table('products as A')
                    ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
                    ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
                    ->select('A.id','A.code','A.regular_price','A.sale_price','A.link','A.image','A.alt','A.category_id','A.user_id','A.type','A.status','A.viewed','B.title','B.slug','B.description','B.attributes','C.name as supplier')
                    ->where('B.language',$this->_data['lang'])
                    ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                    ->where('A.category_id',$category_id)
                    ->where('A.type',$type)
                    ->orderBy('A.priority','asc')
                    ->orderBy('A.id','desc')
                    ->paginate(config('settings.product_per_page') ? config('settings.product_per_page') : 20);

                return view('frontend.default.products',$this->_data);

            }elseif($this->_data['template'] == 'post'){
                $this->_data['posts'] = DB::table('posts as A')
                    ->leftjoin('post_languages as B', 'A.id', '=', 'B.post_id')
                    ->select('A.id','A.link','A.image','A.alt','A.updated_at','B.title','B.slug','B.description')
                    ->where('B.language',$this->_data['lang'])
                    ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                    ->where('A.category_id',$category_id)
                    ->where('A.type',$type)
                    ->orderBy('A.priority','asc')
                    ->orderBy('A.id','desc')
                    ->paginate(config('settings.post_per_page') ? config('settings.post_per_page') : 10);
                return view('frontend.default.posts',$this->_data);
            }
        }
        return abort(404);
    }

    public function archive(Request $request,$type){
        $params['type'] = $type;
        if($this->_data['template'] == 'product'){
            $whereRaw = 'FIND_IN_SET(\'publish\',A.status)';

            if($request->keyword !=''){
                $whereRaw .= ' AND B.title LIKE \'%'.$request->keyword.'%\'';
                $params['keyword'] = $request->keyword;
            }
            if($request->tag){
                $this->_data['tag'] = DB::table('attributes as A')
                    ->leftjoin('attribute_languages as B', 'A.id','=','B.attribute_id')
                    ->select('A.*','B.title','B.slug')
                    ->where('B.slug', $request->tag)
                    ->where('B.language', $this->_data['lang'])
                    ->where('A.type','product_tags')
                    ->first();
                $idProducts = DB::table('product_attribute')->where('attribute_id',$this->_data['tag']->id)->pluck('product_id')->toArray();
                if($idProducts) $whereRaw .= ' AND A.id IN ('.implode(',', $idProducts).')';
                else $whereRaw .= ' AND A.id IN (0)';
                $params['tag'] = $request->tag;
            }

            if($request->color){
                $this->_data['color'] = DB::table('attributes as A')
                    ->leftjoin('attribute_languages as B', 'A.id','=','B.attribute_id')
                    ->select('A.*','B.title','B.slug')
                    ->where('B.slug', $request->color)
                    ->where('B.language', $this->_data['lang'])
                    ->where('A.type','product_colors')
                    ->first();
                $idProducts = DB::table('product_attribute')->where('attribute_id',$this->_data['color']->id)->pluck('product_id')->toArray();
                if($idProducts) $whereRaw .= ' AND A.id IN ('.implode(',', $idProducts).')';
                else $whereRaw .= ' AND A.id IN (0)';
                $params['color'] = $request->color;
            }

            $this->_data['products'] = DB::table('products as A')
                ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
                ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
                ->select('A.id','A.code','A.regular_price','A.sale_price','A.link','A.image','A.alt','A.category_id','A.user_id','A.type','A.status','A.viewed','B.title','B.slug','B.description','B.attributes','C.name as supplier')
                ->where('B.language',$this->_data['lang'])
                ->whereRaw($whereRaw)
                ->where('A.type',$type)
                ->orderBy('A.priority','asc')
                ->orderBy('A.id','desc')
                ->paginate(config('settings.product_per_page') ? config('settings.product_per_page') : 24);
            $this->_data['products']->withPath( route('frontend.home.archive', $params ) );
            return view('frontend.default.products',$this->_data);
        }elseif($this->_data['template'] == 'post'){
            $this->_data['posts'] = DB::table('posts as A')
                ->leftjoin('post_languages as B', 'A.id', '=', 'B.post_id')
                ->select('A.id','A.link','A.image','A.alt','A.updated_at','B.title','B.slug','B.description')
                ->where('B.language',$this->_data['lang'])
                ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                ->where('A.type',$type)
                ->orderBy('A.priority','asc')
                ->orderBy('A.id','desc')
                ->paginate(config('settings.post_per_page') ? config('settings.post_per_page') : 10);
            return view('frontend.default.posts',$this->_data);
        }elseif($this->_data['template'] == 'page'){
            $this->_data['page'] = get_pages($type);
            if( $this->_data['page'] && $this->_data['page']->meta_seo !='' ){
                $current_seo = json_decode($this->_data['page']->meta_seo);
                $current_seo->title ? $this->_data['meta_seo']->title = $current_seo->title : '';
                $current_seo->keywords ? $this->_data['meta_seo']->keywords = $current_seo->keywords : '';
                $current_seo->description ? $this->_data['meta_seo']->description = $current_seo->description : '';
            }
            return view('frontend.default.page',$this->_data);
        }
        return abort(404);
    }

    public function page(Request $request, $type,$slug){
        if($this->_data['template'] == 'product'){
            $this->_data['product'] = DB::table('products as A')
                ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
                ->select('A.*','B.title','B.description','B.contents','B.specifications','B.attributes','B.meta_seo')
                ->where('B.language',$this->_data['lang'])
                ->where('B.slug',$slug)
                ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                ->where('A.type',$type)
                ->first();

            if( $this->_data['product'] && $this->_data['product']->meta_seo !='' ){
                $current_seo = json_decode($this->_data['product']->meta_seo);
                $current_seo->title ? $this->_data['meta_seo']->title = $current_seo->title : '';
                $current_seo->keywords ? $this->_data['meta_seo']->keywords = $current_seo->keywords : '';
                $current_seo->description ? $this->_data['meta_seo']->description = $current_seo->description : '';
                $this->_data['product']->image ? $this->_data['meta_seo']->image = asset('uploads/products/'.get_thumbnail($this->_data['product']->image, '_medium')) : '';
            }

            if( $this->_data['product'] ){
                $client_ip = $request->getClientIp();
                if(!Cache::has($client_ip.'_product_view_'.$this->_data['product']->id)){
                    $this->_data['product']->viewed += 1;
                    DB::table('products')->where('id',$this->_data['product']->id)->increment('viewed',1);
                    Cache::add($client_ip.'_product_view_'.$this->_data['product']->id,$this->_data['product']->viewed,5);
                }
                $viewed = is_array($viewed = json_decode($request->cookie('viewed'), true)) ? $viewed : [];
                if( !in_array($this->_data['product']->id,$viewed) ){
                    array_unshift($viewed,$this->_data['product']->id);
                }
                $cookieViewed = cookie('viewed', json_encode($viewed), 1440);

                $this->_data['images'] = get_media($this->_data['product']->attachments);
                $this->_data['attributes'] = $this->_data['product']->attributes ? json_decode($this->_data['product']->attributes,true) : [];
                $this->_data['colors'] = get_attributes('product_colors');
                $this->_data['sizes'] = get_attributes('product_sizes');
                $this->_data['tags'] = get_attributes('product_tags');

                $comments = DB::table('comments')
                    ->where('product_id',$this->_data['product']->id)
                    ->whereRaw('FIND_IN_SET(\'publish\',status)')
                    ->orderBy('parent','asc')
                    ->orderBy('id','desc')
                    ->get();
                if($comments !== null){
                    foreach($comments as $value){
                        $parent=$value->parent;
                        $this->_data['comments'][$parent][]=$value;
                    }
                }else{
                    $this->_data['comments'] = [];
                }
                $this->_data['countComment'] = count($comments);

                $this->_data['products'] = DB::table('products as A')
                    ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
                    ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
                    ->select('A.*','B.title', 'B.slug', 'B.attributes', 'C.name as supplier')
                    ->where('B.language',$this->_data['lang'])
                    ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                    ->where('A.id','!=',$this->_data['product']->id)
                    ->where('A.category_id',$this->_data['product']->category_id)
                    ->where('A.type',$type)
                    ->orderBy('A.priority','asc')
                    ->orderBy('A.id','desc')
                    ->limit(15)
                    ->get();
                return response()->view('frontend.default.page-product',$this->_data)->cookie($cookieViewed);
            }
        }elseif($this->_data['template'] == 'post'){
            $this->_data['post'] = DB::table('posts as A')
                ->leftjoin('post_languages as B', 'A.id', '=', 'B.post_id')
                ->select('A.*','B.title','B.description','B.contents','B.meta_seo')
                ->where('B.language',$this->_data['lang'])
                ->where('B.slug',$slug)
                ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                ->where('A.type',$type)
                ->first();
            if( $this->_data['post'] && $this->_data['post']->meta_seo !='' ){
                $current_seo = json_decode($this->_data['post']->meta_seo);
                $current_seo->title ? $this->_data['meta_seo']->title = $current_seo->title : '';
                $current_seo->keywords ? $this->_data['meta_seo']->keywords = $current_seo->keywords : '';
                $current_seo->description ? $this->_data['meta_seo']->description = $current_seo->description : '';
                $this->_data['post']->image ? $this->_data['meta_seo']->image = asset('uploads/posts/'.$this->_data['post']->image) : '';
            }
            if( $this->_data['post'] ){
                $client_ip = $request->getClientIp();
                if(!Cache::has($client_ip.'_post_view_'.$this->_data['post']->id)){
                    $this->_data['post']->viewed += 1;
                    DB::table('posts')->where('id',$this->_data['post']->id)->increment('viewed',1);
                    Cache::add($client_ip.'_post_view_'.$this->_data['post']->id,$this->_data['post']->viewed,5);
                }
                $viewed = is_array($viewed = json_decode($request->cookie('viewed'), true)) ? $viewed : [];
                if( !in_array($this->_data['post']->id,$viewed) ){
                    array_unshift($viewed,$this->_data['post']->id);
                }
                $cookieViewed = cookie('viewed', json_encode($viewed), 1440);
                
                $this->_data['author'] = DB::table('users')->select('name')->where( 'id',$this->_data['post']->user_id )->first();

                $this->_data['category'] = DB::table('category_languages')->select('title','slug')->where('language',$this->_data['lang'])
                    ->where('category_id',$this->_data['post']->category_id )->first();

                $comments = DB::table('comments')
                    ->where('post_id',$this->_data['post']->id)
                    ->whereRaw('FIND_IN_SET(\'publish\',status)')
                    ->orderBy('parent','asc')
                    ->orderBy('id','desc')
                    ->get();
                if($comments !== null){
                    foreach($comments as $value){
                        $parent=$value->parent;
                        $this->_data['comments'][$parent][]=$value;
                    }
                }else{
                    $this->_data['comments'] = [];
                }
                $this->_data['countComment'] = count($comments);

                $this->_data['posts'] = DB::table('posts as A')
                    ->leftjoin('post_languages as B', 'A.id', '=', 'B.post_id')
                    ->select('A.id','A.link','A.image','A.alt','A.updated_at','B.title','B.slug','B.description')
                    ->where('B.language',$this->_data['lang'])
                    ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                    ->where('A.id','!=',$this->_data['post']->id)
                    ->where('A.category_id',$this->_data['post']->category_id)
                    ->where('A.type',$type)
                    ->orderBy('A.priority','asc')
                    ->orderBy('A.id','desc')
                    ->limit(15)
                    ->get();

                $this->_data['products'] = DB::table('products as A')
                    ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
                    ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
                    ->select('A.*','B.title','B.slug','B.description','B.attributes','C.name as supplier')
                    ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                    ->where('A.sale_price','!=',0)
                    ->where('B.language',$this->_data['lang'])
                    ->where('A.type','san-pham')
                    ->orderBy('A.wholesale_price','asc')
                    ->orderBy('A.priority','asc')
                    ->orderBy('A.id','desc')
                    ->limit(12)
                    ->get();
                return response()->view('frontend.default.page-post',$this->_data)->cookie($cookieViewed);
            }
        }
        return abort(404);
    }

    public function viewed(Request $request){
        $this->_data['site']['title'] = __('site.viewed');
        $this->_data['breadcrumb'] = '<li class="breadcrumb-item"> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item"> <a href="'.url('/san-pham-da-xem').'"> '.$this->_data['site']['title'].' </a> </li>';
        $viewed = is_array($viewed = json_decode($request->cookie('viewed'), true)) ? $viewed : [];
        if( count($viewed) > 0 ){
            $this->_data['products'] = DB::table('products as A')
                ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
                ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
                ->select('A.*','B.title','B.slug','B.description','B.attributes','C.name as supplier')
                ->where('B.language',$this->_data['lang'])
                ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
                ->whereIn('A.id',$viewed)
                ->where('A.type','san-pham')
                ->orderBy('A.wholesale_price','asc')
                ->orderBy('A.id','desc')
                ->paginate(config('settings.product_per_page') ? config('settings.product_per_page') : 24);
        }else{
            $this->_data['products'] = [];
        }
        return view('frontend.default.products', $this->_data);
    }

    public function sales(Request $request){
        $this->_data['site']['title'] = __('Sản phẩm khuyến mãi');
        $this->_data['breadcrumb'] = '<li class="breadcrumb-item"> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item"> <a href="'.url('/san-pham-khuyen-mai').'"> '.$this->_data['site']['title'].' </a> </li>';
        $this->_data['products'] = DB::table('products as A')
            ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
            ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
            ->select('A.*','B.title','B.slug','B.description','B.attributes','C.name as supplier')
            ->where('B.language',$this->_data['lang'])
            ->whereRaw('(FIND_IN_SET(\'publish\',A.status) AND FIND_IN_SET(\'sale\',A.status)) OR A.sale_price > 0')
            ->where('A.type','san-pham')
            ->orderBy('A.wholesale_price','asc')
            ->orderBy('A.id','desc')
            ->paginate(config('settings.product_per_page') ? config('settings.product_per_page') : 24);
        return view('frontend.default.product_sales', $this->_data);
    }

    public function compare(Request $request){
        $this->_data['site']['title'] = __('So sánh sản phẩm');
        $this->_data['breadcrumb'] = '<li class="breadcrumb-item"> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item"> <a href="'.url('/san-pham-da-xem').'"> '.$this->_data['site']['title'].' </a> </li>';
        return view('frontend.default.compare', $this->_data);
    }

    public function vesinhmaylanh(){
        $this->_data['site']['title'] = __('Vệ sinh máy lạnh');
        $this->_data['breadcrumb'] = '<li class="breadcrumb-item"> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
        $this->_data['breadcrumb'] .= '<li class="breadcrumb-item"> <a href="'.url('/ve-sinh-may-lanh').'"> '.$this->_data['site']['title'].' </a> </li>';
        return view('frontend.default.vesinhmaylanh', $this->_data);
    }
    
}
