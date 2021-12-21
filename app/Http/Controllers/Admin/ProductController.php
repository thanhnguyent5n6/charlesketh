<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use App\Product;
use App\ProductLanguage;
use App\Category;
use App\Attribute;
use App\MediaLibrary;

use Excel;
use DateTime;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $_data;

    public function __construct(Request $request)
    {
        $this->_data['type'] = (isset($request->type) && $request->type !='') ? $request->type : 'default';
        $this->_data['siteconfig'] = config('siteconfig.product');
        $this->_data['default_language'] = config('siteconfig.general.language');
        $this->_data['languages'] = config('siteconfig.languages');
        $this->_data['path'] = $this->_data['siteconfig']['path'];
        $this->_data['thumbs'] = config('settings.thumbs.product') && array_sum(array_column(config('settings.thumbs.product'),'width')) > 0 ? config('settings.thumbs.product') : $this->_data['siteconfig'][$this->_data['type']]['thumbs'];
        $this->_data['pageTitle'] = $this->_data['siteconfig'][$this->_data['type']]['page-title'];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request){
        $whereRaw = "A.type='".$this->_data['type']."'";
        $whereRaw .= $request->category_id ? " and A.category_id=".$request->category_id : "";
        $whereRaw .= $request->title ? " and (A.code like '%".$request->title."%' or B.title like '%".$request->title."%') " : "";
        $whereRaw .= $request->created_at ? " and A.created_at like '%".$request->created_at."%'" : "";
        $whereRaw .= $request->status ? " and FIND_IN_SET('".$request->status."',A.status)" : "";
        $this->_data['oldInput'] = $request->all();
        $this->_data['categories'] = $this->getCategories($this->_data['type']);
        $this->_data['items'] = DB::table('products as A')
            ->leftjoin('product_languages as B', 'A.id','=','B.product_id')
            ->leftjoin('category_languages as C', 'A.category_id','=','C.category_id')
            ->select('A.*','B.title','C.title as category')
            ->whereRaw($whereRaw)
            ->where('B.language', $this->_data['default_language'])
            ->where('C.language', $this->_data['default_language'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->paginate(25);
        return view('admin.products.index',$this->_data);
    }

    public function price(Request $request){
        $whereRaw = "A.type='".$this->_data['type']."'";
        $whereRaw .= $request->category_id ? " and A.category_id=".$request->category_id : "";
        $whereRaw .= $request->title ? " and (A.code like '%".$request->title."%' or B.title like '%".$request->title."%') " : "";
        $whereRaw .= $request->created_at ? " and A.created_at like '%".$request->created_at."%'" : "";
        $whereRaw .= $request->status ? " and FIND_IN_SET('".$request->status."',A.status)" : "";
        $this->_data['oldInput'] = $request->all();
        $this->_data['categories'] = $this->getCategories($this->_data['type']);
        $this->_data['items'] = DB::table('products as A')
            ->leftjoin('product_languages as B', 'A.id','=','B.product_id')
            ->leftjoin('category_languages as C', 'A.category_id','=','C.category_id')
            ->select('A.*','B.title','C.title as category')
            ->whereRaw($whereRaw)
            ->where('B.language', $this->_data['default_language'])
            ->where('C.language', $this->_data['default_language'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->paginate(25);
        return view('admin.products.price',$this->_data);
    }

    public function mask(Request $request, $id){
        $item = DB::table('products as A')
            ->leftjoin('product_languages as B', 'A.id','=','B.product_id')
            ->select('A.*','B.title','B.description','B.attributes')
            ->where('B.language', $this->_data['default_language'])
            ->where('A.id',$id)
            ->first();

        $attributes = $item->attributes ? json_decode($item->attributes,true) : null;
        
        $width       = 1516;
        $height      = 2000;
        $center_x    = $width / 2;
        $center_y    = $height / 2;
        $max_len     = 30;
        $font_height = 50;

        $text = $item->title;
        $price = number_format($item->sale_price ? $item->sale_price : $item->regular_price, 0, ',', '.').'Đ';

        $lines = explode("\n", wordwrap($text, $max_len));
        $y     = $center_y - ((count($lines) - 1) * $font_height);
        $img   = Image::make(public_path('images/bgkm-'.$request->input('loai').'.jpg')); // public/images/mask_03.png

        // Mã sản phẩm
        $img->text($item->code, $center_x, 630, function($font){
            $font->file( public_path('fonts/GoogleSans-Bold.ttf') );
            $font->size(70);
            $font->color('#ff0000');
            $font->align('center');
            $font->valign('center');
        });

        // Giá tiền, chỉ thay đổi thông số 810
        $img->text($price, $center_x, 920, function($font){
            $font->file( public_path('fonts/GoogleSans-Bold.ttf') );
            $font->size(210);
            $font->color('#f8ef24');
            $font->align('center');
            $font->valign('center');
        });

        if($item->sale_price && $item->regular_price){
        	// Giá tiền bôi đen
        	$priceBlack = number_format($item->regular_price, 0, ',', '.');
	        $img->text($priceBlack, $center_x, 780, function($font){
	            $font->file( public_path('fonts/GoogleSans-Bold.ttf') );
	            $font->size(100);
	            $font->color('#000000');
	            $font->align('center');
	            $font->valign('center');
	        });
        }

        // Tên sản phẩm, xuống hàng dựa theo biến $max_len
        foreach ($lines as $line)
        {
            $img->text($line, $center_x, $y-550, function($font){

                $font->file( public_path('fonts/GoogleSans-Bold.ttf'));
                $font->size(90);
                $font->color('#fff');
                $font->align('center');
                $font->valign('center');

            });
            $y += $font_height * 2;
        }

        if($item->description){
	        $motas = explode("\n", wordwrap($item->description, 50));
	        $y     = 1110;
	        foreach ($motas as $line)
	        {
	            $img->text($line, $center_x, $y, function($font){

	                $font->file( public_path('fonts/GoogleSans-Bold.ttf'));
	                $font->size(50);
	                $font->color('#fff');
	                $font->align('center');
	                $font->valign('center');

	            });
	            $y += 65;
	        }
        }

        if($attributes){
            $y = 1750;
            foreach($attributes as $attribute){
                if( $attribute['value'] !== null ) {
                    $img->text($attribute['value'], $center_x, $y, function($font){
                        $font->file( public_path('fonts/GoogleSans-Bold.ttf') );
                        $font->size(50);
                        $font->color('#fff');
                        $font->align('center');
                        $font->valign('center');
                    });
                    $y += 65;
                }
            }
        }
        return $img->response();
    }

    public function maskSmall(Request $request, $id){
        $item = DB::table('products as A')
            ->leftjoin('product_languages as B', 'A.id','=','B.product_id')
            ->select('A.*','B.title','B.attributes')
            ->where('B.language', $this->_data['default_language'])
            ->where('A.id',$id)
            ->first();

        $attributes = $item->attributes ? json_decode($item->attributes,true) : null;
        
        $width       = 1516;
        $height      = 2000;
        $center_x    = $width / 4.4;
        $center_x_t  = $center_x - 68;
        $center_y    = $height / 2;
        $max_len     = 29;
        $font_height = 25;

        $text = $item->title;
        $price = number_format($item->regular_price, 0, ',', '.').'đ';

        $lines = explode("\n", wordwrap($text, $max_len));
        $y     = $center_y - ((count($lines) - 1) * $font_height);
        $img   = Image::make(public_path('images/bgkmsmall-'.$request->input('loai').'.jpg')); // public/images/mask_03.png

        // Giá tiền, chỉ thay đổi thông số 810
        $img->text($price, $center_x, 290, function($font){
            $font->file( public_path('fonts/GoogleSans-Bold.ttf') );
            $font->size(100);
            $font->color('#ff0000');
            $font->align('center');
            $font->valign('center');
        });

        if($item->sale_price && $item->wholesale_price){
        	// Giá tiền bôi đen
            $priceBlack = number_format($item->wholesale_price, 0, ',', '.').'đ';
	        $img->text($priceBlack, $center_x, 190, function($font){
	            $font->file( public_path('fonts/GoogleSans-Bold.ttf') );
	            $font->size(45);
	            $font->color('#fff');
	            $font->align('center');
	            $font->valign('center');
	        });
        }

        // Tên sản phẩm, xuống hàng dựa theo biến $max_len
        foreach ($lines as $line)
        {
            $img->text($line, $center_x_t, $y-890, function($font){

                $font->file( public_path('fonts/GoogleSans-Bold.ttf'));
                $font->size(35);
                $font->color('#2f3192');
                $font->align('left');
                $font->valign('left');

            });
            $y += $font_height * 2;
        }

        // if($attributes){
        //     $y = 1110;
        //     foreach($attributes as $attribute){
        //         if( $attribute['value'] !== null ) {
        //             $img->text($attribute['value'], $center_x, $y, function($font){
        //                 $font->file( public_path('fonts/GoogleSans-Bold.ttf') );
        //                 $font->size(70);
        //                 $font->color('#2f3192');
        //                 $font->align('center');
        //                 $font->valign('center');
        //             });
        //             $y += $font_height * 2;
        //         }
        //     }
        // }
        return $img->response();
    }

    public function maskCircle(Request $request, $id){
        $item = DB::table('products as A')
            ->leftjoin('product_languages as B', 'A.id','=','B.product_id')
            ->select('A.*','B.title','B.description','B.attributes')
            ->where('B.language', $this->_data['default_language'])
            ->where('A.id',$id)
            ->first();

        $attributes = $item->attributes ? json_decode($item->attributes,true) : null;

        $width       = 1516;
        $height      = 2000;
        $center_x    = $width / 2;
        $center_y    = $height / 2;
        $max_len     = 30;
        $font_height = 40;

        $text = $item->title;
        $price = number_format($item->sale_price ? $item->sale_price : $item->wholesale_price, 0, ',', '.').'đ';

        $lines = explode("\n", wordwrap($text, $max_len));
        $y     = $center_y - ((count($lines) - 1) * $font_height);
        $img   = Image::make(public_path('images/bgcircle-'.$request->input('loai').'.jpg')); // public/images/mask_03.png

        // Giá tiền, chỉ thay đổi thông số 810
        $img->text($price, $center_x, 850, function($font){
            $font->file( public_path('fonts/GoogleSans-Bold.ttf') );
            $font->size(170);
            $font->color('#ff0000');
            $font->align('center');
            $font->valign('center');
        });

        if($item->sale_price && $item->wholesale_price){
        	// Giá tiền bôi đen
        	$priceBlack = number_format($item->wholesale_price, 0, ',', '.').'đ';
	        $img->text($priceBlack, $center_x, 1000, function($font){
	            $font->file( public_path('fonts/GoogleSans-Bold.ttf') );
	            $font->size(100);
	            $font->color('#000000');
	            $font->align('center');
	            $font->valign('center');
	        });
        }

        // Tên sản phẩm, xuống hàng dựa theo biến $max_len
        foreach ($lines as $line)
        {
            $img->text($line, $center_x, $y-450, function($font){

                $font->file( public_path('fonts/GoogleSans-Bold.ttf'));
                $font->size(55);
                $font->color('#2f3192');
                $font->align('center');
                $font->valign('center');

            });
            $y += $font_height * 2;
        }

        if($item->description){
	        $motas = explode("\n", wordwrap($item->description, 50));
	        $y     = 1180;
	        foreach ($motas as $line)
	        {
	            $img->text($line, $center_x, $y, function($font){

	                $font->file( public_path('fonts/GoogleSans-Bold.ttf'));
	                $font->size(40);
	                $font->color('#000');
	                $font->align('center');
	                $font->valign('center');

	            });
	            $y += 65;
	        }
        }

        // if($attributes){
        //     $y = 1110;
        //     foreach($attributes as $attribute){
        //         if( $attribute['value'] !== null ) {
        //             $img->text($attribute['value'], $center_x, $y, function($font){
        //                 $font->file( public_path('fonts/GoogleSans-Bold.ttf') );
        //                 $font->size(70);
        //                 $font->color('#2f3192');
        //                 $font->align('center');
        //                 $font->valign('center');
        //             });
        //             $y += $font_height * 2;
        //         }
        //     }
        // }
        return $img->response();
    }
    
    public function ajax(Request $request){
        if($request->ajax()){
            $data['items'] = DB::table('products as A')
                ->leftjoin('product_languages as B', 'A.id','=','B.product_id')
                ->select('A.*','B.title')
                ->whereRaw("(A.code LIKE '%".$request->q."%' OR B.title LIKE '%".$request->q."%') AND FIND_IN_SET('publish',status)")
                ->where('B.language', $this->_data['default_language'])
                ->where('A.type',$request->t)
                ->orderBy('A.priority','asc')
                ->orderBy('A.id','desc')
                ->get();
            return response()->json($data);
        }
    }
    
    public function create(){
        $this->_data['suppliers'] = $this->getSupplier();
        $this->_data['filters'] = $this->getCategories('filter');
        $this->_data['categories'] = $this->getCategories($this->_data['type']);

        $this->_data['relatedTo'] = DB::table('products as A')
                ->leftjoin('product_languages as B', 'A.id','=','B.product_id')
                ->select('A.id','B.title')
                ->where('B.language', $this->_data['default_language'])
                ->where('A.type',$this->_data['type'])
                ->orderBy('A.priority','asc')
                ->orderBy('A.id','desc')
                ->get();

        if( config('siteconfig.attribute.'.$this->_data['type']) && config('siteconfig.attribute.'.$this->_data['type']) !='default'  ){
            foreach( config('siteconfig.attribute.'.$this->_data['type']) as $k => $v ){
                if( !$v ) continue;
                $this->_data['attrs'][$k] = $this->getAttributes($k);
            }
        }
        
    	return view('admin.products.create',$this->_data);
    }

    public function store(Request $request){
        // dd($request);
        $valid = Validator::make($request->all(), [
            'dataL.vi.title'   => 'required',
            'code'        => 'required|unique:products,code',
            'image'            => 'image|max:2048',
            'data.category_id' => 'exists:categories,id',
            'data.supplier_id' => 'required',
        ], [
            'dataL.vi.title.required'   => 'Vui lòng nhập Tên Sản Phẩm',
            'code.required'   => 'Vui lòng nhập Mã Sản Phẩm',
            'code.unique'          => 'Mã sản phẩm đã tồn tại, vui lòng nhập mã khác',
            'image.image'               => 'Không đúng chuẩn hình ảnh cho phép',
            'image.max'                 => 'Dung lượng vượt quá giới hạn cho phép là :max KB',
            'data.category_id.exists'   => 'Vui lòng chọn Danh mục',
            'data.supplier_id.required'   => 'Vui lòng chọn Nhà cung cấp',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $product  = new Product;

            if($request->data){
                foreach($request->data as $field => $value){
                    $product->$field = $value;
                }
            }

            if($request->hasFile('image')){
                $product->image = save_image( $this->_data['path'],$request->file('image'),$this->_data['thumbs'] );
            }

            if($request->hasFile('files')){
                $fileuploader = json_decode($request->input('fileuploader-list-files'),true);
                foreach($request->file('files') as $file){
                    $fileName  = $file->getClientOriginalName();
                    if( false !== $key = array_search($fileName, $request->attachment['name']) ){
                        $fileMime  = $file->getClientMimeType();
                        $fileSize      = $file->getClientSize();
                        $imageName = save_image( $this->_data['path'],$file, null);
                        
                        if( isset($fileuploader[$key]['editor']) ){
                            $newImg  = Image::make( public_path($this->_data['path'].'/'.$imageName) );
                            if( @$fileuploader[$key]['editor']['rotation'] ){
                                $rotation = -(int)$fileuploader[$key]['editor']['rotation'];
                                $newImg->rotate($rotation);
                            }
                            if( @$fileuploader[$key]['editor']['crop'] ){
                                $width  = round($fileuploader[$key]['editor']['crop']['width']);
                                $height = round($fileuploader[$key]['editor']['crop']['height']);
                                $left   = round($fileuploader[$key]['editor']['crop']['left']);
                                $top    = round($fileuploader[$key]['editor']['crop']['top']);
                                $newImg->crop($width,$height,$left,$top);
                            }
                            $newImg->save( public_path($this->_data['path'].'/'.$imageName) );
                        }

                        $media = MediaLibrary::create([
                            'image' => $imageName,
                            'alt'   => $request->attachment['alt'][$key],
                            'editor' => isset($fileuploader[$key]['editor']) ? $fileuploader[$key]['editor'] : '',
                            'mime_type' => $fileMime,
                            'type' => $this->_data['type'],
                            'size' => $fileSize,
                            'priority'   => $request->attachment['priority'][$key],
                        ]);
                        $media_list_id[] = $media->id;
                        unset($fileuploader[$key]);
                    }
                }
                $product->attachments = implode(',',$media_list_id);
            }
            $product->code           = strtoupper($request->code);
            $product->original_price = floatval(str_replace('.', '', $request->original_price));
            $product->regular_price  = floatval(str_replace('.', '', $request->regular_price));
            $product->vtp_price     = floatval(str_replace('.', '', $request->vtp_price));
            $product->sale_price     = floatval(str_replace('.', '', $request->sale_price));
            $product->wholesale_price = floatval(str_replace('.', '', $request->wholesale_price));
            $product->weight         = (int)str_replace('.', '', $request->weight);
            
            $product->filters        = ($request->filters) ? implode(',',$request->filters) : '';
            $product->related_to     = ($request->related_to) ? implode(',',$request->related_to) : '';
            $product->priority       = (int)str_replace('.', '', $request->priority);
            $product->status         = ($request->status) ? implode(',',$request->status) : '';
            $product->user_id        = Auth::id();
            $product->type           = $this->_data['type'];
            $product->created_at     = new DateTime();
            $product->updated_at     = new DateTime();
            $product->save();

            $dataL = [];
            $dataInsert = [];
            foreach($this->_data['languages'] as $lang => $val){
                if($request->dataL[$lang]){
                    foreach($request->dataL[$lang] as $fieldL => $valueL){
                        $dataL[$fieldL] = $valueL;
                    }
                }

                if( !isset($request->dataL[$this->_data['default_language']]['slug']) || $request->dataL[$this->_data['default_language']]['slug'] == ''){
                    $dataL['slug']       = str_slug($request->dataL[$this->_data['default_language']]['title']);
                }else{
                    $dataL['slug']       = str_slug($request->dataL[$this->_data['default_language']]['slug']);
                }
                $dataL['attributes'] = $request->input('attributes.'.$lang);
                $dataL['language']   = $lang;
                $dataInsert[]        = new ProductLanguage($dataL);
            }
            $product->languages()->saveMany($dataInsert);

            $attrs = [];
            if( config('siteconfig.attribute.'.$this->_data['type']) && config('siteconfig.attribute.'.$this->_data['type']) !='default'  ){
                foreach( config('siteconfig.attribute.'.$this->_data['type']) as $k => $v ){
                    if ( $v && $request->has($k) && is_array($request->$k) && count($request->$k) > 0) {
                        foreach ($request->$k as $l) {
                            $attrs[$l] = ['type' => $k];
                        }
                    }
                }
            }
            $product->attribute()->sync($attrs);

            return redirect()->route('admin.product.index',['type'=>$this->_data['type']])->with('success','Thêm dữ liệu <b>'.$product->languages[0]->title.'</b> thành công');
        }
        
    }

    public function edit($id){
        $this->_data['item'] = Product::find($id);
        if ($this->_data['item'] !== null) {
            $this->_data['suppliers'] = $this->getSupplier();
            $this->_data['categories'] = $this->getCategories($this->_data['type']);
            $this->_data['relatedTo'] = DB::table('products as A')
                ->leftjoin('product_languages as B', 'A.id','=','B.product_id')
                ->select('A.id','B.title')
                ->where('B.language', $this->_data['default_language'])
                ->where('A.type',$this->_data['type'])
                ->where('A.id', '!=', $id)
                ->orderBy('A.priority','asc')
                ->orderBy('A.id','desc')
                ->get();
            if($this->_data['item']->category_id){
                $category = Category::find($this->_data['item']->category_id);
                $filter_id = $category->filters ? explode(',',$category->filters) : [];
                $this->_data['filters'] = DB::table('categories as A')
                    ->leftjoin('category_languages as B', 'A.id', '=', 'B.category_id')
                    ->select('A.*','B.title','B.slug')
                    ->where('B.language',$this->_data['default_language'])
                    ->where(function($query) use ($filter_id){
                        $query->whereIn('A.id',$filter_id)->orWhereIn('A.parent',$filter_id);
                    })
                    ->where('A.type','filter')
                    ->orderBy('A.priority','asc')
                    ->get();
            } else {
                $this->_data['filters'] = $this->getCategories('filter');
            }
            
            if( config('siteconfig.attribute.'.$this->_data['type']) && config('siteconfig.attribute.'.$this->_data['type']) !='default'  ){
                foreach( config('siteconfig.attribute.'.$this->_data['type']) as $k => $v ){
                    if( !$v ) continue;
                    $this->_data['attrs'][$k] = $this->getAttributes($k);
                }
            }
            $this->_data['images'] = $this->getMediaLibrary($this->_data['item']['attachments']);
            return view('admin.products.edit',$this->_data);
        }
        return redirect()->route('admin.product.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function update(Request $request, $id){
        
        $valid = Validator::make($request->all(), [
            'dataL.vi.title' => 'required',
            'code' => 'required|unique:products,code,'.$id,
            'image' => 'image|max:2048',
            'data.category_id' => 'exists:categories,id',
            'data.supplier_id' => 'required',
        ], [
            'dataL.vi.title.required'    => 'Vui lòng nhập Tên Sản Phẩm',
            'code.required'   => 'Vui lòng nhập Mã Sản Phẩm',
            'code.unique' => 'Mã sản phẩm đã tồn tại, vui lòng nhập mã khác',
            'image.image' => 'Không đúng chuẩn hình ảnh cho phép',
            'image.max' => 'Dung lượng vượt quá giới hạn cho phép là :max KB',
            'data.category_id.exists' => 'Vui lòng chọn Danh mục',
            'data.supplier_id.required' => 'Vui lòng chọn Nhà cung cấp',
        ]);
        if ($valid->fails()) {
            return redirect()->back()->withErrors($valid)->withInput();
        } else {
            $product = Product::find($id);
            if ($product !== null) {
                if($request->data){
                	foreach($request->data as $field => $value){
                        $product->$field = $value;
                    }
                }

                if($request->hasFile('image')){
                    delete_image( $this->_data['path'].'/'.$product->image, $this->_data['thumbs'] );
                    $product->image = save_image( $this->_data['path'], $request->file('image'), $this->_data['thumbs'] );
                }

                $fileuploader = json_decode($request->input('fileuploader-list-files'),true);
                $media_list_id = [];
                
                if($request->media){
                    foreach($request->media['id'] as $key => $media_id){
                        $media = MediaLibrary::find($media_id);
                        if( isset($fileuploader[$key]['editor']) ){
                            $newImg = Image::make( public_path($this->_data['path'].'/'. $media->image) );
                            if( @$fileuploader[$key]['editor']['rotation'] ){
                                $rotation = -(int)$fileuploader[$key]['editor']['rotation'];
                                $newImg->rotate($rotation);
                            }
                            if( @$fileuploader[$key]['editor']['crop'] ){
                                $width = round($fileuploader[$key]['editor']['crop']['width']);
                                $height = round($fileuploader[$key]['editor']['crop']['height']);
                                $left = round($fileuploader[$key]['editor']['crop']['left']);
                                $top = round($fileuploader[$key]['editor']['crop']['top']);
                                $newImg->crop($width,$height,$left,$top);
                            }
                            $newImg->save( public_path($this->_data['path'].'/'. $media->image) );
                            $media->editor = $fileuploader[$key]['editor'];
                            
                        }
                        $media->priority = $request->media['priority'][$key];
                        $media->save();
                        $media_list_id[] = $media_id;
                        unset($fileuploader[$key]);
                    }
                    $fileuploader = array_values($fileuploader);
                }

                if($request->hasFile('files')){
                    
                    foreach($request->file('files') as $file){
                        $fileName  = $file->getClientOriginalName();
                        if( false !== $key = array_search($fileName, $request->attachment['name']) ){

                            $fileMime  = $file->getClientMimeType();
                            $fileSize      = $file->getClientSize();
                            $imageName = save_image( $this->_data['path'],$file, null);

                            if( isset($fileuploader[$key]['editor']) ){
                                $newImg  = Image::make( public_path($this->_data['path'].'/'.$imageName) );
                                if( @$fileuploader[$key]['editor']['rotation'] ){
                                    $rotation = -(int)$fileuploader[$key]['editor']['rotation'];
                                    $newImg->rotate($rotation);
                                }
                                if( @$fileuploader[$key]['editor']['crop'] ){
                                    $width  = round($fileuploader[$key]['editor']['crop']['width']);
                                    $height = round($fileuploader[$key]['editor']['crop']['height']);
                                    $left   = round($fileuploader[$key]['editor']['crop']['left']);
                                    $top    = round($fileuploader[$key]['editor']['crop']['top']);
                                    $newImg->crop($width,$height,$left,$top);
                                }
                                $newImg->save( public_path($this->_data['path'].'/'.$imageName) );
                            }

                            $media = MediaLibrary::create([
                                'image' => $imageName,
                                'alt'   => $request->attachment['alt'][$key],
                                'editor' => isset($fileuploader[$key]['editor']) ? $fileuploader[$key]['editor'] : '',
                                'mime_type' => $fileMime,
                                'type' => $this->_data['type'],
                                'size' => $fileSize,
                                'priority'   => $request->attachment['priority'][$key],
                            ]);
                            $media_list_id[] = $media->id;
                            unset($fileuploader[$key]);
                        }
                    }
                    
                }
                $product->code           = strtoupper($request->code);
                $product->attachments    = implode(',',$media_list_id);
                $product->original_price = floatval(str_replace('.', '', $request->original_price));
                $product->regular_price  = floatval(str_replace('.', '', $request->regular_price));
                $product->sale_price     = floatval(str_replace('.', '', $request->sale_price));
                $product->vtp_price      = floatval(str_replace('.', '', $request->vtp_price));
                $product->wholesale_price = floatval(str_replace('.', '', $request->wholesale_price));
                $product->weight         = (int)str_replace('.', '', $request->weight);
                
                $product->filters        = ($request->filters) ? implode(',',$request->filters) : '';
                $product->related_to     = ($request->related_to) ? implode(',',$request->related_to) : '';
                $product->priority       = (int)str_replace('.', '', $request->priority);
                $product->status         = ($request->status) ? implode(',',$request->status) : '';
                $product->type           = $this->_data['type'];
                $product->updated_at     = new DateTime();
                $product->save();
                $i = 0;
                foreach($this->_data['languages'] as $lang => $val){
                    $productLang = ProductLanguage::find($product->languages[$i]['id']);
                    if($request->dataL[$lang]){
                        foreach($request->dataL[$lang] as $fieldL => $valueL){
                            $productLang->$fieldL = $valueL;
                        }
                    }
                    if( !isset($request->dataL[$this->_data['default_language']]['slug']) || $request->dataL[$this->_data['default_language']]['slug'] == '' ){
                        $productLang->slug       = str_slug($request->dataL[$this->_data['default_language']]['title']);
                    }else{
                        $productLang->slug       = str_slug($request->dataL[$this->_data['default_language']]['slug']);
                    }
                    $productLang->attributes = $request->input('attributes.'.$lang);
                    $productLang->language   = $lang;
                    $productLang->save();
                    $i++;
                }
                $attrs = [];
                if( config('siteconfig.attribute.'.$this->_data['type']) && config('siteconfig.attribute.'.$this->_data['type']) !='default'  ){
                    foreach( config('siteconfig.attribute.'.$this->_data['type']) as $k => $v ){
                        if ( $v && $request->has($k) && is_array($request->$k) && count($request->$k) > 0) {
                            foreach ($request->$k as $l) {
                                $attrs[$l] = ['type' => $k];
                            }
                        }
                    }
                }
                $product->attribute()->sync($attrs);
                return redirect( $request->redirects_to )->with('success','Cập nhật dữ liệu <b>'.$product->languages[0]->title.'</b> thành công');
            }
            return redirect( $request->redirects_to )->with('danger', 'Dữ liệu không tồn tại');
        }
    }

    public function delete($id){
    	$product = Product::find($id);
        $deleted = $product->languages[0]->title;
        if ($product !== null) {
            delete_image($this->_data['path'].'/'.$product->image,$this->_data['thumbs']);
            if( $product->attachments ){
                $arrID = explode(',',$product->attachments);
                $medias = MediaLibrary::select('*')->whereIn('id',$arrID)->get();
                if( $medias !== null ){
                    foreach( $medias as $media ){
                        delete_image($this->_data['path'].'/'.$media->image,$this->_data['thumbs']);
                    }
                    MediaLibrary::destroy($arrID);
                }
            }
            if( $product->delete() ){
                return redirect()->route('admin.product.index',['type'=>$this->_data['type']])->with('success', 'Xóa dữ liệu <b>'.$deleted.'</b> thành công');
            }else{
                return redirect()->route('admin.product.index',['type'=>$this->_data['type']])->with('danger', 'Xóa dữ liệu bị lỗi');
            }
        }
        return redirect()->route('admin.product.index',['type'=>$this->_data['type']])->with('danger', 'Dữ liệu không tồn tại');
    }

    public function export(Request $request){
        $filename = 'Danh-Sach-San-Pham-'.date('dmY');
        $fileExt = $request->extension;

        $whereRaw = "A.type='".$this->_data['type']."'";
        $whereRaw .= $request->category_id ? " and A.category_id=".$request->category_id : "";
        $whereRaw .= $request->title ? " and (A.code like '%".$request->title."%' or B.title like '%".$request->title."%') " : "";
        $whereRaw .= $request->created_at ? " and A.created_at like '%".$request->created_at."%'" : "";
        $whereRaw .= $request->status ? " and FIND_IN_SET('".$request->status."',A.status)" : "";
        $data = DB::table('products as A')
            ->leftjoin('product_languages as B', 'A.id','=','B.product_id')
            ->select('A.*','B.title','B.specifications')
            ->whereRaw($whereRaw)
            ->where('B.language', $this->_data['default_language'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->limit(500)
            ->get()->toArray();

        return Excel::create($filename, function($excel) use ($data) {
            $excel->sheet('Sản phẩm', function($sheet) use ($data) {
            	$sheet->loadView('excel.products')->with(['data'=>$data]);
                // $sheet->fromArray( json_decode( json_encode($data), true) );
            });
        })->download($fileExt);
    }

    public function import(Request $request){
        $path = 'uploads/files';
        $file = save_image($path,$request->file('file'),null);
        $data = Excel::load(public_path($path.'/'.$file), function($reader) {})->get();
        $dataFail = [];
        foreach($data as $val){
        	if( $product = Product::where('code',$val->ma_sp)->first() ){
                $product->update(['wholesale_price' => floatval($val->gia_website), 'regular_price' => floatval($val->gia_cua_hang), 'sale_price' => floatval($val->gia_km)]);
        	} else {
        		$dataFail[] = $val->ma_sp;
        	}
        }
        if( count($dataFail) > 0 ){
        	dd($dataFail);
        } else {
        	return redirect()->back()->with('success','Cập nhật dữ liệu thành công');
        }
    }

    public function importNew(Request $request){
        $path = 'uploads/files';
        $file = save_image($path,$request->file('file'),null);
        $data = Excel::load(public_path($path.'/'.$file), function($reader) {})->get();
        $dataFail = [];
        foreach($data as $val){
        	if( $product = Product::where('code',$val->ma_sp)->first() ){
        		$dataFail[] = $val->ma_sp;
        	} else {
        		$product  = new Product;
        		$product->code           = strtoupper($val->ma_sp);
	            $product->wholesale_price = floatval($val->gia_ban);
	            $product->sale_price 	 = @$val->gia_km ? floatval($val->gia_km) : 0;
	            $product->category_id    = (int)$val->category_id;
	            $product->supplier_id    = (int)$val->supplier_id;
	            $product->user_id        = 1;
	            $product->priority       = 1;
	            $product->status         = 'publish';
	            $product->type           = 'san-pham';
	            $product->created_at     = new DateTime();
	            $product->updated_at     = new DateTime();
	            $product->save();

	            $product->languages()->create([
	            	'title'	=>	$val->ten_sp,
	            	'slug'	=>	str_slug($val->ten_sp),
	            	'language'	=>	'vi'
	            ]);
        	}
        }
        if( count($dataFail) > 0 ){
        	dd($dataFail);
        } else {
        	return redirect()->back()->with('success','Thêm dữ liệu thành công');
        }
    }

    public function getSupplier($type='default'){
        return DB::table('suppliers')
            ->where('type',$type)
            ->orderBy('priority','asc')
            ->orderBy('id','desc')
            ->get();
    }

    public function getCategories($type){
        return DB::table('categories as A')
            ->leftjoin('category_languages as B', 'A.id','=','B.category_id')
            ->select('A.id', 'A.parent', 'A.filters', 'B.title')
            ->whereRaw('(A.type = \''.$type.'\' or A.type = \'default\')')
            ->where('B.language', $this->_data['default_language'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();
    }

    public function getAttributes($type){
        return DB::table('attributes as A')
            ->leftjoin('attribute_languages as B', 'A.id','=','B.attribute_id')
            ->select('A.*','B.title')
            ->where('A.type',$type)
            ->where('B.language', $this->_data['default_language'])
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();
    }

    public function getMediaLibrary($attachments){
        $arrID = explode(',',$attachments);
        return MediaLibrary::select('*')->whereIn('id',$arrID)->orderBy('priority','asc')->orderBy('id','desc')->get();
    }
}
