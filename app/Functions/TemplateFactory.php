<?php
namespace App\Functions;
use Illuminate\Support\Facades\DB;
use App\Functions\Facades\Tool;
use Carbon\Carbon;

class TemplateFactory {
	public function getTemplateProductPrice($regular_price, $sale_price){
		if( $regular_price > 0 && $sale_price == 0){
			$price = '<span class="new">'.get_currency_vn($regular_price,'đ').'</span>';
		}elseif($sale_price > 0){
			$price = '<span class="new">'.get_currency_vn($sale_price,'đ').'</span><span class="old">'.get_currency_vn($regular_price,'đ').'</span>';
		}else{
			$price = '<span class="new">'.__('site.contact').'</span>';
		}
		return $price;
		
	}

    public function getTemplateProductSearch($item,$type='',$show=4,$moreClass=''){
        if($type == '') $type = $item->type;
        $link = (@$item->link) ? $item->link : route('frontend.home.product',['slug' => $item->slug]);

        if($show==6){ $class = "col-lg-2 col-md-3 col-sm-4 col-6"; }
        elseif($show==4){ $class = "col-md-3 col-sm-4 col-6"; }
        elseif($show==3){ $class = "col-md-4 col-sm-6 col-6"; }
        elseif($show==2){ $class = "col-sm-6 col-6 col-wide"; }
        elseif($show==1){ $class = "col-12"; }

        $promotions = config('promotions');
        if( count( $promotions ) > 0 ){
            foreach( $promotions as $promotion ){
                if( $promotion['coupon_amount'] <= 0) continue;
                if( in_array($item->id, explode(',', ($promotion['product_limit'] ? $promotion['product_limit'] : '') ) ) ){
                    break;
                }
                if( in_array($item->id, explode(',', ($promotion['product_id'] ? $promotion['product_id'] : '') ) ) || in_array($item->category_id, explode(',', ($promotion['category_id'] ? $promotion['category_id'] : '') ) ) ){
                    if($promotion['change_conditions_type'] == 'discount_from_total_cart'){
                        $item->sale_price = $item->wholesale_price - $promotion['coupon_amount'];
                    }elseif($promotion['change_conditions_type'] == 'percentage_discount_from_total_cart' && $promotion['coupon_amount'] < 100){
                        $item->sale_price = $item->wholesale_price - (($item->wholesale_price * $promotion['coupon_amount'])/100);
                    }
                    break;
                }
            }
        }

        $template = '
            <div class="search-item">
                <a href="'.$link.'" class="link">
                <div class="image">
                    <img src="'. ( $item->image && file_exists(public_path('/uploads/products/'.$item->image)) ? asset( '/uploads/products/'.get_thumbnail($item->image, '_medium') ) : asset('noimage/350x350') ) .'" alt="'.$item->alt.'" />
                </div>
                <div class="info">
                    <h2 class="title">'.$item->title.'</h2>
                    <div class="price">
                        '.self::getTemplateProductPrice($item->wholesale_price, $item->sale_price).'
                    </div>
                </div>
                </a>
            </div>
        ';
        return $template;
    }

	public function getTemplateProduct($item,$type='',$show=4,$moreClass=''){
        if($type == '') $type = $item->type;
		$link = (@$item->link) ? $item->link : route('frontend.home.product',['slug' => $item->slug]);

        $attrs = [];
        $attributes = $item->attributes ? json_decode($item->attributes,true) : [];
        foreach($attributes as $attribute){
            if( $attribute['name'] !== null || $attribute['value'] !== null )
                $attrs[] = $attribute['name'].''.$attribute['value'];
        }
        $attrs = '<div class="desc">'.implode(',', $attrs).'</div>';

        $arrStatus = explode(',',$item->status);
        $status = '<div class="icons">';
        if(in_array('new', $arrStatus)){ $status .= '<span class="new">New</span>'; }
        if(in_array('hot', $arrStatus)){ $status .= '<span class="hot">Hot</span>'; }

        $promotions = config('promotions');
        if( count( $promotions ) > 0 ){
            foreach( $promotions as $promotion ){
                if( $promotion['coupon_amount'] <= 0) continue;
                if( in_array($item->id, explode(',', ($promotion['product_limit'] ? $promotion['product_limit'] : '') ) ) ){
                    break;
                }
                if( in_array($item->id, explode(',', ($promotion['product_id'] ? $promotion['product_id'] : '') ) ) || in_array($item->category_id, explode(',', ($promotion['category_id'] ? $promotion['category_id'] : '') ) ) ){
                    if($promotion['change_conditions_type'] == 'discount_from_total_cart'){
                        $item->sale_price = $item->wholesale_price - $promotion['coupon_amount'];
                    }elseif($promotion['change_conditions_type'] == 'percentage_discount_from_total_cart' && $promotion['coupon_amount'] < 100){
                        $item->sale_price = $item->wholesale_price - (($item->wholesale_price * $promotion['coupon_amount'])/100);
                    }
                    break;
                }
            }
        }


        // if($item->sale_price > 0){
        //     $status .= '<span class="sale">Giảm '.(int)(100-($item->sale_price/$item->wholesale_price)*100).'%</span>';
        // }
        $status .= '</div>';

        if( @$item->filters ){
        	$labels = '<div class="labels d-flex justify-content-between">';
        	$filters = explode(',',$item->filters);
        	if( in_array(20, $filters) ) {
        		$labels .= '<div class="hp"> 1HP </div>';
        	} elseif( in_array(22, $filters) ) {
        		$labels .= '<div class="hp"> 1.5HP </div>';
        	} elseif( in_array(21, $filters) ) {
        		$labels .= '<div class="hp"> 2HP </div>';
        	} elseif( in_array(23, $filters) ) {
        		$labels .= '<div class="hp"> 2.5HP </div>';
        	} elseif( in_array(31, $filters) ) {
        		$labels .= '<div class="hp"> 3HP </div>';
        	} elseif( in_array(42, $filters) ) {
        		$labels .= '<div class="hp"> 3.5HP </div>';
        	} elseif( in_array(32, $filters) ) {
        		$labels .= '<div class="hp"> 4HP </div>';
        	} elseif( in_array(43, $filters) ) {
        		$labels .= '<div class="hp"> 4.5HP </div>';
        	} elseif( in_array(33, $filters) ) {
        		$labels .= '<div class="hp"> 5HP </div>';
        	} elseif( in_array(35, $filters) ) {
        		$labels .= '<div class="hp"> 5.5HP </div>';
        	} elseif( in_array(34, $filters) ) {
        		$labels .= '<div class="hp"> 6HP </div>';
        	} elseif( in_array(39, $filters) ) {
        		$labels .= '<div class="hp"> 8HP </div>';
        	} elseif( in_array(40, $filters) ) {
        		$labels .= '<div class="hp"> 9HP </div>';
        	} elseif( in_array(41, $filters) ) {
        		$labels .= '<div class="hp"> 10HP </div>';
        	} elseif( in_array(147, $filters) ) {
        		$labels .= '<div class="hp"> Trên 10HP </div>';
        	}
        	if( in_array(25, $filters) ) {
        		$labels .= '<div class="inverter"> Inverter </div>';
        	}

        	$labels .= '</div>';
        } else {
        	$labels = '';
        }

        if($show==6){ $class = "col-lg-2 col-md-3 col-sm-4 col-6"; }
        elseif($show==4){ $class = "col-md-3 col-sm-4 col-6"; }
        elseif($show==3){ $class = "col-md-4 col-sm-6 col-6"; }
        elseif($show==2){ $class = "col-sm-6 col-6 col-wide"; }
        elseif($show==1){ $class = "col-12"; }
		$template = '
            <div class="'.$class.' '.$moreClass.'">
                <div class="product-item">
                    <div class="image">
                    	'.$labels.'
                        <a href="'.$link.'"><img src="'. ( $item->image && file_exists(public_path('/uploads/products/'.$item->image)) ? asset( '/uploads/products/'.get_thumbnail($item->image, '_small') ) : asset('noimage/350x350') ) .'" alt="'.$item->alt.'" /></a>
                    </div>
                    <div class="info">
                        <h2 class="title">'.$item->title.'</h2>
                    	'.(in_array('sale', $arrStatus) ? '<span class="d-block mb-2"><img src="https://dienmaygiasi.vn/public/themes/default/images/label-tet.png" style="width: 140px; height: 20px;" ></span>' : '').'
                        <a href="#" class="sub-title">'.$item->supplier.'</a>
                        <span class="text-warning mr-2 d-inline-block my-1"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star-half-o" aria-hidden="true"></i></span><span style="font-size:0.7rem">4.5/5 đánh giá</span>
                            
                    </div>
                    <div class="price">
                        '.self::getTemplateProductPrice(@$item->wholesale_price, @$item->sale_price).'
                    </div>
                    '.$attrs.'
                    '.$status.'
                    <div class="action">
                        <a href="#" class="btn add-to-wishlist tooltips" data-style="default" data-container="body" data-placement="top" data-original-title="Yêu thích" data-ajax="id='. $item->id .'"> <i class="fa fa-heart-o"></i> </a>
                        <a href="#" class="btn tooltips add-to-cart" data-style="default" data-container="body" data-placement="top" data-original-title="Giỏ hàng" data-ajax="id='. $item->id .'"> <i class="fa fa-shopping-cart"></i> </a>
                        <a href="#" class="btn tooltips" data-style="default" data-container="body" data-placement="top" data-original-title="Xem nhanh" > <i class="fa fa-search"></i> </a>
                    </div>
                    <a href="'.$link.'" class="link"></a>
                </div>
            </div>
		';

		return $template;
    }
    
    public function getTemplateProductSale($item,$type='',$show=4,$moreClass=''){
        if($type == '') $type = $item->type;
		$link = (@$item->link) ? $item->link : route('frontend.home.product',['slug' => $item->slug]);

        $attrs = [];
        $attributes = $item->attributes ? json_decode($item->attributes,true) : [];
        foreach($attributes as $attribute){
            if( $attribute['name'] !== null || $attribute['value'] !== null )
                $attrs[] = $attribute['name'].''.$attribute['value'];
        }
        $attrs = '<div class="desc">'.implode(',', $attrs).'</div>';

        $arrStatus = explode(',',$item->status);
        $status = '<div class="icons">';
        if(in_array('new', $arrStatus)){ $status .= '<span class="new">New</span>'; }
        if(in_array('hot', $arrStatus)){ $status .= '<span class="hot">Hot</span>'; }

        $promotions = config('promotions');
        if( count( $promotions ) > 0 ){
            foreach( $promotions as $promotion ){
                if( $promotion['coupon_amount'] <= 0) continue;
                if( in_array($item->id, explode(',', ($promotion['product_limit'] ? $promotion['product_limit'] : '') ) ) ){
                    break;
                }
                if( in_array($item->id, explode(',', ($promotion['product_id'] ? $promotion['product_id'] : '') ) ) || in_array($item->category_id, explode(',', ($promotion['category_id'] ? $promotion['category_id'] : '') ) ) ){
                    if($promotion['change_conditions_type'] == 'discount_from_total_cart'){
                        $item->sale_price = $item->wholesale_price - $promotion['coupon_amount'];
                    }elseif($promotion['change_conditions_type'] == 'percentage_discount_from_total_cart' && $promotion['coupon_amount'] < 100){
                        $item->sale_price = $item->wholesale_price - (($item->wholesale_price * $promotion['coupon_amount'])/100);
                    }
                    break;
                }
            }
        }


        // if($item->sale_price > 0){
        //     $status .= '<span class="sale">Giảm '.(int)(100-($item->sale_price/$item->wholesale_price)*100).'%</span>';
        // }
        $status .= '</div>';

        if( @$item->filters ){
        	$labels = '<div class="labels d-flex justify-content-between">';
        	$filters = explode(',',$item->filters);
        	if( in_array(20, $filters) ) {
        		$labels .= '<div class="hp"> 1HP </div>';
        	} elseif( in_array(22, $filters) ) {
        		$labels .= '<div class="hp"> 1.5HP </div>';
        	} elseif( in_array(21, $filters) ) {
        		$labels .= '<div class="hp"> 2HP </div>';
        	} elseif( in_array(23, $filters) ) {
        		$labels .= '<div class="hp"> 2.5HP </div>';
        	} elseif( in_array(31, $filters) ) {
        		$labels .= '<div class="hp"> 3HP </div>';
        	} elseif( in_array(42, $filters) ) {
        		$labels .= '<div class="hp"> 3.5HP </div>';
        	} elseif( in_array(32, $filters) ) {
        		$labels .= '<div class="hp"> 4HP </div>';
        	} elseif( in_array(43, $filters) ) {
        		$labels .= '<div class="hp"> 4.5HP </div>';
        	} elseif( in_array(33, $filters) ) {
        		$labels .= '<div class="hp"> 5HP </div>';
        	} elseif( in_array(35, $filters) ) {
        		$labels .= '<div class="hp"> 5.5HP </div>';
        	} elseif( in_array(34, $filters) ) {
        		$labels .= '<div class="hp"> 6HP </div>';
        	} elseif( in_array(39, $filters) ) {
        		$labels .= '<div class="hp"> 8HP </div>';
        	} elseif( in_array(40, $filters) ) {
        		$labels .= '<div class="hp"> 9HP </div>';
        	} elseif( in_array(41, $filters) ) {
        		$labels .= '<div class="hp"> 10HP </div>';
        	} elseif( in_array(147, $filters) ) {
        		$labels .= '<div class="hp"> Trên 10HP </div>';
        	}
        	if( in_array(25, $filters) ) {
        		$labels .= '<div class="inverter"> Inverter </div>';
        	}

        	$labels .= '</div>';
        } else {
        	$labels = '';
        }

        if($show==6){ $class = "col-lg-2 col-md-3 col-sm-4 col-6"; }
        elseif($show==4){ $class = "col-md-3 col-sm-4 col-6"; }
        elseif($show==3){ $class = "col-md-4 col-sm-6 col-6"; }
        elseif($show==2){ $class = "col-sm-6 col-6 col-wide"; }
        elseif($show==1){ $class = "col-12"; }
		$template = '
            <div class="'.$class.' '.$moreClass.'">
                <div class="product-item">
                    <div class="image">
                    	'.$labels.'
                        <a href="'.$link.'"><img src="'. ( $item->image && file_exists(public_path('/uploads/products/'.$item->image)) ? asset( 'public/uploads/products/'.get_thumbnail($item->image, '_small') ) : asset('noimage/350x350') ) .'" alt="'.$item->alt.'" /></a>
                    </div>
                    <div class="info">
                        <h2 class="title">'.$item->title.'</h2>
                        <a href="#" class="sub-title">'.$item->supplier.'</a>
                        <span class="text-warning mr-2 d-inline-block my-1"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star-half-o" aria-hidden="true"></i></span><span style="font-size:0.7rem">4.5/5 đánh giá</span>
                    </div>
                    <div class="price">
                        '.self::getTemplateProductPrice(@$item->wholesale_price, @$item->sale_price).'
                    </div>
                    '.$attrs.'
                    '.$status.'
                    <div class="action">
                        <a href="#" class="btn add-to-wishlist tooltips" data-style="default" data-container="body" data-placement="top" data-original-title="Yêu thích" data-ajax="id='. $item->id .'"> <i class="fa fa-heart-o"></i> </a>
                        <a href="#" class="btn tooltips add-to-cart" data-style="default" data-container="body" data-placement="top" data-original-title="Giỏ hàng" data-ajax="id='. $item->id .'"> <i class="fa fa-shopping-cart"></i> </a>
                        <a href="#" class="btn tooltips" data-style="default" data-container="body" data-placement="top" data-original-title="Xem nhanh" > <i class="fa fa-search"></i> </a>
                    </div>
                    <a href="'.$link.'" class="link"></a>
                </div>
            </div>
		';

		return $template;
	}

    public function getTemplatePost($item,$type='',$show=4,$moreClass=''){
        if($type == '') $type = $item->type;
        $link = ($item->link) ? $item->link : route('frontend.home.page',['type' => $type, 'slug' => $item->slug]);
        if($show==6){ $class = "col-lg-2 col-md-3 col-sm-4 col-6 col-wide"; }
        elseif($show==4){ $class = "col-md-3 col-sm-4 col-6 col-wide"; }
        elseif($show==3){ $class = "col-md-4 col-sm-6 col-6 col-wide"; }
        elseif($show==2){ $class = "col-sm-6 col-6 col-wide"; }
        elseif($show==1){ $class = "col-12"; }
        $template = '
            <div class="'.$class.' '.$moreClass.'">
                <div class="post-item">
                    <div>
                    <a class="image" href="'.$link.'"><img src="'. ( $item->image && file_exists(public_path('/uploads/posts/'.$item->image)) ? asset( 'public/uploads/posts/'.get_thumbnail($item->image) ) : asset('noimage/330x220') ) .'" alt="'.$item->alt.'" /></a>
                    </div>
                    <div class="desc">
                        <h3 class="title"><a href="'.$link.'">'.$item->title.'</a></h3>
                        <p>'.substr($item->description,0,100).'</p>
                        <p><a href="'.$link.'" class="post-link">Xem Chi Tiết</a></p>
                    </div>
                </div>
            </div>
        ';

        return $template;
    }

    public function getTemplateCollection($item,$type='',$show=4,$moreClass=''){
        if($type == '') $type = $item->type;
        $link = ($item->link) ? $item->link : route('frontend.home.page',['type' => $type, 'slug' => $item->slug]);
        if($show==6){ $class = "col-lg-2 col-md-3 col-sm-4 col-6"; }
        elseif($show==4){ $class = "col-md-3 col-sm-4 col-6 col-wide"; }
        elseif($show==3){ $class = "col-md-4 col-sm-6 col-6 col-non-padding"; }
        elseif($show==2){ $class = "col-sm-6 col-6 col-wide"; }
        elseif($show==1){ $class = "col-12"; }
        $template = '
            <div class="collection-item '.$class.' '.$moreClass.'">
                <div class="image">
                    <img src="'. ( $item->image && file_exists(public_path('/uploads/posts/'.$item->image)) ? asset( 'public/uploads/posts/'.$item->image ) : asset('noimage/500x500') ) .'" alt="'.$item->alt.'" />
                    <div class="desc">
                        <div>
                            <h3 class="title"><a href="'.$link.'">'.$item->title.'</a></h3>
                            <p class="social">
                                <a href="#" target="_blank"><span class="fa fa-facebook"></span></a>
                                <a href="#" target="_blank"><span class="fa fa-twitter"></span></a>
                                <a href="#" target="_blank"><span class="fa fa-vimeo"></span></a>
                                <a href="#" target="_blank"><span class="fa fa-pinterest"></span></a>
                                <a href="#" target="_blank"><span class="fa fa-google"></span></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        ';

        return $template;
    }

    public function getTemplateSinglePost($item,$type='',$show=4,$moreClass=''){
        if($type == '') $type = $item->type;
        $link = ($item->link) ? $item->link : route('frontend.home.page',['type' => $type, 'slug' => $item->slug]);
        if($show==6){ $class = "col-lg-2 col-md-3 col-sm-4 col-6 col-wide"; }
        elseif($show==4){ $class = "col-md-3 col-sm-4 col-6 col-wide"; }
        elseif($show==3){ $class = "col-md-4 col-sm-6 col-6 col-wide"; }
        elseif($show==2){ $class = "col-sm-6 col-6 col-wide"; }
        elseif($show==1){ $class = "col-12"; }
        $template = '
            <div class="'.$class.' '.$moreClass.'">
                <div class="single-post">
                    <img src="'. ( $item->image && file_exists(public_path('/uploads/posts/'.$item->image)) ? asset( 'public/uploads/posts/'.get_thumbnail($item->image) ) : asset('noimage/330x220') ) .'" alt="'.$item->alt.'" />
                    <h2 class="title">'.$item->title.'</h2>
                    <p>'.substr($item->description,0,100).'</p>
                </div>
            </div>
        ';

        return $template;
    }

    public function getTemplateComment($data,$parent=0,$lvl=0){
        $result = '';
        if( isset($data[$parent]) ){
            if( $parent==0 ){
                $result .= '<div class="timeline comment-list">';
            }else{
                $result .= '<div class="timeline comment-list">';
                krsort($data[$parent]);
            }
            foreach($data[$parent] as $k=>$v){
                $id=$v->id;
                $result .= '<div class="timeline-item">';
                $result .= '
                    <div class="timeline-badge">
                        <div class="timeline-icon">
                            <i class="icon-user"></i>
                        </div>
                        <div class="timeline-badge-name">'.$v->name.'</div>
                        <div class="timeline-badge-time font-grey-cascade">'.Tool::niceTime($v->created_at).'</div>
                    </div>
                    <div class="timeline-wrap">
                        <div class="timeline-body">
                            <div class="timeline-body-arrow"> </div>
                            <div class="timeline-body-head">
                                <div class="timeline-body-head-caption">
                                    '.( $parent == 0 ? Tool::buildRating($v->score) : '').'
                                    <span class="timeline-body-title font-blue-madison">'.$v->title.'</span>
                                </div>
                                <div class="timeline-body-head-actions">
                                    '.($lvl < 1 ? '<a href="#" class="btn reply" data-parent="'.$v->id.'" data-product="'.( @$v->product_id ? $v->product_id : '0' ).'" data-post="'.( @$v->post_id ? $v->post_id : '0' ).'">'.__('site.reply').'</a>' : '').'</div>
                            </div>
                            <div class="timeline-body-content '.( $parent != 0 ? 'mt-0' : '').'">
                                <div class="font-grey-cascade">'.$v->description.'</div>
                            </div>
                            
                        </div>';
                        $result .= self::getTemplateComment($data,$id,$lvl+1);
                $result .= '</div>';
                $result .= '</div>';
            }
            $result .= '</div>';
        }
        return $result;
        $result = '';
        if( isset($data[$parent]) ){
            if( $parent==0 ){
                $result .= '<ul class="comment-list">';
            }else{
                $result .= '<ul>';
                krsort($data[$parent]);
            }
            foreach($data[$parent] as $k=>$v){
                $id=$v->id;
                $result .= '<li>';
                $result .= '
                    <div class="single-comment clearfix" data-lvl="'.$id.'"">
                        <div class="image float-left"><img src="'.asset('noimage/50x50').'" alt="" class="img-circle"></div>
                        <div class="content">
                            <div class="head">
                                <div class="author-time">
                                    <h4>'.$v->name.'</h4>
                                    <span>'.Tool::niceTime($v->created_at).'</span>
                                </div>
                                
                            </div>
                            <p>'.$v->description.'</p>
                        </div>
                    </div>
                ';
                $result .= self::getTemplateComment($data,$id,$lvl+1);
                $result .= '</li>';
            }
            $result .= '</ul>';
        }
        return $result;
    }
}