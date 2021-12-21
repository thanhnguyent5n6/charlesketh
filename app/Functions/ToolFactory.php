<?php
namespace App\Functions;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
class ToolFactory {

    public function setType($type=''){
        $data['site']['title'] = __('site.home');
        $data['site']['class'] = 'site-home';
        $data['type'] = $type;

        switch($type){
            case "gioi-thieu":
                $data['site']['title'] = __('site.about');
                $data['template'] = "page";
                break;
            case "san-pham":
                $data['site']['title'] = __('site.product');
                $data['template'] = "product";
                break;
            case "dich-vu":
                $data['site']['title'] = __('site.service');
                $data['template'] = "post";
                break;
            case "tin-tuc":
                $data['site']['title'] = __('Tin tức');
                $data['template'] = "post";
                break;
            case "ho-tro-khach-hang":
                $data['site']['title'] = __('Hỗ trợ khách hàng');
                $data['template'] = "post";
                break;
            case "thong-tin-can-biet":
                $data['site']['title'] = __('Thông tin cần biết');
                $data['template'] = "post";
                break;
            case "doi-tac":
                $data['site']['title'] = "Đối tác";
                $data['template'] = "post";
                break;
            default:
                $data['template'] = "index";
                break;
        }
        $data['breadcrumb'] = '<li class="breadcrumb-item"> <a href="'.url('/').'">'.__('site.home').'</a> </li>';
        if($type !=''){
            $data['bg_breadcrumb'] = self::getPhotoByUrl(url()->current(),'background',app()->getLocale());
            $data['breadcrumb'] .= '<li class="breadcrumb-item"> <a href="'.url('/'.$type).'"> '.$data['site']['title'].' </a> </li>';
        }
        return $data;
    }

    public function setMetaTags($lang='vi'){
        $default_seo = self::getSeos(url()->current(),$lang);
        if(!$default_seo) $default_seo = self::getSeos(url('/'),$lang);
        $default_seo = json_decode(@$default_seo->meta_seo);
        $seodata['title'] = @$default_seo->title;
        $seodata['keywords'] = @$default_seo->keywords;
        $seodata['description'] = @$default_seo->description;
        $seodata['image'] = asset('uploads/photos/'.config('settings.logo'));
        return (object) $seodata;
    }

    public function checkRole($role){
        if ( auth()->user()->hasRole('admin') ) {
            return true;
        }elseif ( auth()->user()->hasRole($role) ) {
            return true;
        }else{
            return false;
        }
    }

    public function getThumbnail($filename, $suffix = '_small') {
        if ($filename) {
            return preg_replace("/(.*)\.(.*)/i", "$1{$suffix}.$2", $filename);
        }
        return '';
    }

    public function getCurrencyVN($number, $symbol = 'đ', $isPrefix = false) {
        if ($isPrefix) {
            return $symbol . number_format($number, 0, ',', '.');
        } else {
            return number_format($number, 0, ',', '.') . '<sup>' . $symbol . '</sup>';
        }
    }

    public function saveImage($path,$image,$thumbs = ['_small' => ['width' => 300, 'height' => 200 ]]) {
        if ( !empty($image) ) {
            $folderName = date('Y-m');
            $fileName = $image->getClientOriginalName();
            $fileExtension = $image->getClientOriginalExtension();
            $fileNameSlug = str_slug( str_replace('.'.$fileExtension, '', $fileName) );

            $fileName = $fileNameSlug.'.'.$fileExtension;

            if ( !file_exists(public_path($path.'/'. $folderName)) ) {
                mkdir(public_path($path.'/'.$folderName), 0755, true);
            }

            if( file_exists(public_path($path.'/'. $folderName.'/'.$fileName)) ){
                $fileNameSlug = $fileNameSlug.'_'.time();
                $fileName = $fileNameSlug.'.'.$fileExtension;
            }
            // Di chuyển file vào folder Uploads
            $imageName = "$folderName/$fileName";
            $image->move( public_path($path.'/'.$folderName), $fileName );

            // Tạo các hình ảnh theo tỉ lệ giao diện
            $createImage = function($suffix = '_small', $width = 300, $height = 200) use($path, $folderName, $imageName, $fileNameSlug, $fileExtension) {
                $thumbnailFileName = $fileNameSlug . $suffix . '.' . $fileExtension;
                if($width <= 0) $width = 300;
                if($height <= 0) $height = 200;
                Image::make(public_path($path.'/'.$imageName))
                    ->fit($width, $height, function ($c) {
                        $c->aspectRatio();
                        $c->upsize();
                    })
                    ->save( public_path($path.'/'.$folderName.'/'.$thumbnailFileName) )
                    ->destroy();
            };
            if($thumbs !== null){
                foreach($thumbs as $k => $v){
                    if( $v['width'] !== null && $v['height'] !== null ){
                        $createImage($k,$v['width'],$v['height']);
                    }
                }
            }
            return $imageName;
        }
    }

    public function deleteImage($path,$thumbs) {
        if (!is_dir(public_path($path)) && file_exists(public_path($path))) {
            unlink(public_path($path));
            $deleteAllImages = function($sizeArr) use($path) {
                foreach ($sizeArr as $size) {
                    if (!is_dir(public_path(get_thumbnail($path, $size))) && file_exists(public_path(get_thumbnail($path, $size)))) {
                        unlink(public_path(get_thumbnail($path, $size)));
                    }
                }
            };
            if($thumbs !== null)
                $deleteAllImages(array_keys($thumbs));
        }
    }

    public function countOrders($type,$status){
        return DB::table('orders')
            ->where('status_id',$status)
            ->where('type',$type)
            ->count();
    }

    public function getCategories($type,$lang='vi'){
        return DB::table('categories as A')
            ->leftjoin('category_languages as B', 'A.id','=','B.category_id')
            ->select('A.*', 'A.parent', 'A.icon', 'A.image', 'A.alt', 'B.title', 'B.slug', 'B.description')
            ->where('B.language', $lang)
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->where('A.priority','>',0)
            ->where('A.type',$type)
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();
    }

    public function getPosts($type,$lang='vi'){
        return DB::table('posts as A')
            ->leftjoin('post_languages as B', 'A.id','=','B.post_id' )
            ->select('A.*','B.slug','B.title','B.description')
            ->where('B.language',$lang)
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->where('A.type',$type)
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();
    }

    public function getProductByCategory($category_id,$type,$lang='vi'){
        return DB::table('products as A')
            ->leftjoin('product_languages as B', 'A.id', '=', 'B.product_id')
            ->leftjoin('suppliers as C', 'C.id', '=', 'A.supplier_id')
            ->select('A.*','B.title','B.slug','B.description','B.attributes','C.name as supplier')
            ->where('B.language',$lang)
            ->whereRaw('FIND_IN_SET(\'publish\',A.status) AND FIND_IN_SET(\'index\',A.status)')
            ->where('A.category_id',$category_id)
            ->where('A.type',$type)
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->limit(8)
            ->get();
    }

    public function getPhotos($type,$lang='vi'){
        return DB::table('photos as A')
            ->leftjoin('photo_languages as B', 'A.id','=','B.photo_id' )
            ->select('A.*','B.title','B.description')
            ->where('B.language',$lang)
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->where('A.type',$type)
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();
    }

    public function getPhotoByUrl($url,$type,$lang='vi'){
        return DB::table('photos as A')
            ->leftjoin('photo_languages as B', 'A.id','=','B.photo_id' )
            ->select('A.*','B.title','B.description')
            ->where('B.language',$lang)
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->where('A.type',$type)
            ->where('A.link',$url)
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->first();
    }

    public function getLinks($type,$lang='vi'){
        return DB::table('links as A')
            ->leftjoin('link_languages as B', 'A.id','=','B.link_id' )
            ->select('A.*','B.title','B.description')
            ->where('B.language',$lang)
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->where('A.type',$type)
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();
    }

    public function getPages($type,$lang='vi'){
        return DB::table('pages as A')
            ->leftjoin('page_languages as B', 'A.id','=','B.page_id' )
            ->select('A.*','B.slug','B.title','B.description','B.contents','B.meta_seo')
            ->where('B.language',$lang)
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->where('A.type',$type)
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->first();
    }

    public function getSeos($url,$lang='vi'){
        return DB::table('seos as A')
            ->leftjoin('seo_languages as B', 'A.id','=','B.seo_id' )
            ->select('A.*','B.slug','B.title','B.meta_seo')
            ->where('B.language',$lang)
            ->whereRaw('FIND_IN_SET(\'publish\',A.status)')
            ->where('A.link', $url )
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->first();
    }

    public function getAttributes($type,$lang='vi',$limit=100){
        return DB::table('attributes as A')
            ->leftjoin('attribute_languages as B', 'A.id','=','B.attribute_id')
            ->select('A.*','B.title','B.slug')
            ->where('B.language', $lang)
            ->where('A.type',$type)
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->limit($limit)
            ->get();
    }

    public function getAttributesMostUsedByProduct($type,$limit=5,$lang='vi'){
        $listID = DB::table('product_attribute')
            ->selectRaw('attribute_id, count(attribute_id) as used')
            ->where('type',$type)
            ->groupBy('attribute_id')
            ->orderBy('used','desc')
            ->pluck('attribute_id')
            ->toArray();
            
        return DB::table('attributes as A')
            ->leftjoin('attribute_languages as B', 'A.id','=','B.attribute_id')
            ->select('A.*','B.title','B.slug')
            ->where('B.language', $lang)
            ->whereIn('A.id',$listID)
            ->where('A.type',$type)
            ->orderBy('A.priority','asc')
            ->orderBy('A.id','desc')
            ->get();
    }

    public function getMediaLibrary($attachments){
        $arrID = explode(',',$attachments);
        return DB::table('media_libraries')
            ->select('*')
            ->whereIn('id',$arrID)
            ->orderBy('priority','asc')
            ->orderBy('id','desc')
            ->get();
    }

    public function getSuppliers($type){
        return DB::table('suppliers')
            ->whereRaw('FIND_IN_SET(\'publish\',status)')
            ->where('type',$type)
            ->orderBy('priority','asc')
            ->get();
    }

    public function getUser($id,$type){
        return DB::table('users')
            ->where('id',$id)
            ->first();
    }

    public function getTableAttribute($table,$field,$name,$id){
        return DB::table($table)
            ->where($field,$id)
            ->first()->$name;
    }

    public function updateCode($id,$prefix){
        $strlen = strlen($id);
        if($strlen==1){ $code = $prefix."0000".$id;
        } else if($strlen==2){ $code = $prefix."000".$id;
        } else if($strlen==3){ $code = $prefix."00".$id;
        } else if($strlen==4){ $code = $prefix."0".$id;
        } else{ $code = $prefix.$id; }
        return $code;
    }

    public function niceTime($date){
        if(empty($date)) {
            return __('site.no_date');
        }
        
        $periods         = __('site.periods');
        $lengths         = array("60","60","24","7","4.35","12","10");
        
        $now             = time();
        $unix_date         = strtotime($date);
        
           // check validity of date
        if(empty($unix_date)) {    
            return __('site.bad_date');
        }

        // is it future date or past date
        if($now > $unix_date) {    
            $difference     = $now - $unix_date;
            $tense         = __('site.ago');
            
        } else {
            $difference     = $unix_date - $now;
            $tense         = __('site.from_now');
        }
        
        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }
        
        $difference = round($difference);
        
        if($difference != 1) {
            $periods[$j].= __('site.many_second');
        }
        
        return "$difference $periods[$j] {$tense}";
    }

    public function buildRating($score=0,$class='active'){
        $result =   '<span class="rating">';
        for($i=0;$i<5;$i++){
            if($i<$score){
                $result .= '<i class="fa fa-star '.$class.'" data-rate="'.($i+1).'"></i>';
            }else{
                $result .= '<i class="fa fa-star" data-rate="'.($i+1).'"></i>';
            }
        }
        $result .=  '</span>';
        return $result;
    }

    public function getComments($data,$parent=0,$lvl=0){
        $result = '';
        if( isset($data[$parent]) ){
            if( $parent==0 ){
                $result .= '<div class="timeline">';
            }else{
                $result .= '<div class="timeline">';
                krsort($data[$parent]);
            }
            foreach($data[$parent] as $k=>$v){
                $id=$v->id;
                $result .= '<div id="record-'.$v->id.'" class="timeline-item '.($v->status == '' ? 'disabled' : '').'">';
                $result .= '
                    <div class="timeline-badge">
                        <div class="timeline-icon">
                            <i class="'.($v->status == '' ? 'icon-user-unfollow font-red-haze' : 'icon-user-following font-green-haze').'"></i>
                        </div>
                        <div class="timeline-badge-name">'.$v->name.'</div>
                        <div class="timeline-badge-time font-grey-cascade">'.self::niceTime($v->created_at).'</div>
                    </div>
                    <div class="timeline-wrap">
                        <div class="timeline-body">
                            <div class="timeline-body-arrow"> </div>
                            <div class="timeline-body-head">
                                <div class="timeline-body-head-caption">
                                    '.( $parent == 0 ? self::buildRating($v->score) : '').'
                                    <a href="javascript:;" class="timeline-body-title font-blue-madison">'.$v->title.'</a>
                                </div>
                                <div class="timeline-body-head-actions">
                                    <a href="#" class="btn btn-circle green btn-outline btn-sm btn-comment-expand"> <i class="fa fa-angle-down"></i> </a>
                                    <div class="btn-group">
                                        <button class="btn btn-circle green btn-outline btn-sm dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false"> Hành động
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <li>
                                                <a href="#" class="btn-comment-reply" data-parent="'.$v->id.'" data-category="'.( @$v->category_id ? $v->category_id : '0' ).'" data-product="'.( @$v->product_id ? $v->product_id : '0' ).'" data-post="'.( @$v->post_id ? $v->post_id : '0' ).'">Trả lời</a>
                                            </li>
                                            <li>
                                                <a href="#" class="btn-comment-status" data-ajax="act=update_status|table=comments|id='.$v->id.'|col=status|val=publish"> Hiển thị </a>
                                            </li>
                                            <li class="divider"> </li>
                                            <li>
                                                <a href="#" class="btn-comment-delete" data-id="'.$v->id.'"> Xóa </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-body-content">
                                <span class="font-grey-cascade">'.$v->description.'</span>
                            </div>
                        </div>';
                        $result .= self::getComments($data,$id,$lvl+1);
                $result .= '</div>';
                $result .= '</div>';
            }
            $result .= '</div>';
        }
        return $result;
    }

    public function getProductInWarehouses($store = '',$type = 'default'){
        $products = [];

        $whereRawImport = 'FIND_IN_SET(\'publish\',status)';
        $whereRawExport = 'FIND_IN_SET(\'publish\',status)';

        if($store){
            $import_ids = DB::table('wms_imports')->where('store_code',$store)->pluck('id')->toArray();
            $export_ids = DB::table('wms_exports')->where('store_code',$store)->pluck('id')->toArray();

            $whereRawImport .= ' AND import_id in('.implode(',',$import_ids).')';
            $whereRawExport .= ' AND export_id in('.implode(',',$export_ids).')';
        }

        $items = DB::table('wms_import_details')
            ->select('product_id','product_code','product_qty','product_title','product_price','unit')
            ->whereRaw($whereRawImport)->get();
            
        if( $items !== null ){
            foreach( $items as $item ){
                $code  = $item->product_code;
                $unit  = $item->unit;
                @$products[$code][$unit]['id'] = $item->product_id;
                @$products[$code][$unit]['title'] = $item->product_title;
                @$products[$code][$unit]['price'] = (int)$item->product_price;
                @$products[$code][$unit]['import'] += (int)$item->product_qty;
                @$products[$code][$unit]['export'] = 0;
            }
        }

        if(@$export_ids){
            $items = DB::table('wms_export_details')
                ->select('product_id','product_code','product_qty','unit')
                ->whereRaw($whereRawExport)
                ->get();
            if( $items !== null ){
                foreach( $items as $item ){
                    $code  = $item->product_code;
                    $unit  = $item->unit;
                    @$products[$code][$unit]['export'] += (int)$item->product_qty;
                }
            }
        }

        return $products;
    }
}