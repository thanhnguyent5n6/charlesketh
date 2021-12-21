@extends('frontend.default.app')
@section('content')
<!-- PAGE SECTION START -->
<section class="page-section pb-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="product-details">
                    <div class="row">
                        <div class="col-md-5 offset-md-0 col-sm-8 offset-sm-2 col-12 mb-4">
                            <div class="image">
                                <div class="slick-product-image">
                                    <div>
                                        <img src="{{ ( $product->image && file_exists(public_path('/uploads/products/'.$product->image)) ? asset( '/uploads/products/'.$product->image ) : asset('noimage/500x450') ) }}" alt="{{ $product->alt }}" />
                                    </div>
                                    @forelse($images as $image)
                                    <div>
                                        <img src="{{ ( $image->image && file_exists(public_path('/uploads/products/'.$image->image)) ? asset( '/uploads/products/'.$image->image ) : asset('noimage/500x450') ) }}" alt="{{ $image->alt }}" />
                                    </div>
                                    @empty
                                    @endforelse
                                </div>
                                <div class="slick-product-thumb">
                                    <div>
                                        <div class="pd-10">
                                            <a href="javascript:;"><img src="{{ ( $product->image && file_exists(public_path('/uploads/products/'.$product->image)) ? asset('uploads/products/'.$product->image) : asset('noimage/100x80') ) }}" alt="{{ $product->alt }}" /></a>
                                        </div>
                                    </div>
                                    @forelse($images as $image)
                                    <div>
                                        <div class="pd-10">
                                            <a href="javascript:;"><img src="{{ ( $image->image && file_exists(public_path('/uploads/products/'.$image->image)) ? asset('uploads/products/'.$image->image) : asset('noimage/100x80') ) }}" alt="{{ $image->alt }}" /></a>
                                        </div>
                                    </div>
                                    @empty
                                    @endforelse
                                </div>
                                @if( @$filters )
                                <div class="row no-gutters mt-3">
							    @foreach(@$filters as $key => $filter)
							        <div class="col-4 p-3 text-center border border-warning" style="background-color: #f1f1f1">
							        	<p class="font-weight-bold">{{ $key }}</p>
						                @forelse($filter as $key => $val)
						                <span>{{ $val->title }}</span>
						                @empty
						                @endforelse
							        </div>
							    @endforeach
							    </div>
							    @endif
                            </div>
                        </div>
                        <div class="col-md-7 col-sm-12 col-12 mb-3">
                            <div class="info">
                                <ul>
                                    <li class="mb-1"><h1 class="title">{{ $product->title }}</h1></li>
                                    <li class="pt-0 mb-3">
                                        <span><b>Mã sản phẩm:</b> {{ $product->code }}</span> 
                                        <span class="ml-2"><b>Đánh giá:</b> <span class="text-warning"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star-half-o" aria-hidden="true"></i></span>4.5/5</span>
                                    </li>
                                    @if(@$relatedTo)
                                    <li class="pt-0 mb-3">
                                    	<p><b>Xem thêm:</b> Sản Phẩm Cùng Loại</p>
                                        <div class="row m-0">
                                            @forelse($relatedTo as $related)
                                            <div class="col-4 col-md-2 text-center sp-related mb-1">
                                                <div class="custom-control custom-radio custom-control-inline mr-0">
                                                    <input type="radio" class="custom-control-input" value="{{ $related->id }}" id="product{{ $related->id }}" onclick="location.href='{{ route('frontend.home.product',['slug'=>$related->slug]) }}'" />
                                                    <label class="custom-control-label w-auto" for="product{{ $related->id }}" style="line-height: 20px; white-space: nowrap;">
                                                        @php
                                                            if($related->filters){
                                                                $filters = explode(',',$related->filters);
                                                                if( in_array(20, $filters) ) {
                                                                    echo '1HP';
                                                                } elseif( in_array(22, $filters) ) {
                                                                    echo '1.5HP';
                                                                } elseif( in_array(21, $filters) ) {
                                                                    echo '2HP';
                                                                } elseif( in_array(23, $filters) ) {
                                                                    echo '2.5HP';
                                                                } elseif( in_array(31, $filters) ) {
                                                                    echo '3HP';
                                                                } elseif( in_array(42, $filters) ) {
                                                                    echo '3.5HP';
                                                                } elseif( in_array(32, $filters) ) {
                                                                    echo '4HP';
                                                                } elseif( in_array(43, $filters) ) {
                                                                    echo '4.5HP';
                                                                } elseif( in_array(33, $filters) ) {
                                                                    echo '5HP';
                                                                } elseif( in_array(35, $filters) ) {
                                                                    echo '5.5HP';
                                                                } elseif( in_array(34, $filters) ) {
                                                                    echo '6HP';
                                                                } elseif( in_array(39, $filters) ) {
                                                                    echo '8HP';
                                                                } elseif( in_array(40, $filters) ) {
                                                                    echo '9HP';
                                                                } elseif( in_array(41, $filters) ) {
                                                                    echo '10HP';
                                                                } elseif( in_array(147, $filters) ) {
                                                                    echo 'Trên 10HP';
                                                                }
                                                            }
                                                        @endphp
                                                    </label>
                                                </div>
                                                <div>{!! $related->sale_price ? get_currency_vn($related->sale_price) : get_currency_vn($related->wholesale_price) !!}</div>
                                            </div>
                                            @empty
                                            @endforelse
                                        </div>
                                    </li>
                                    @endif
                                    <li>
                                        <span class="price">
                                            {!! get_template_product_price($product->wholesale_price,$product->sale_price) !!}
                                        </span>
                                        <p class="inven-pro">
                                        	@php
		                                    $status = explode(',',$product->status);
		                                    if( in_array('instock', $status) ){
		                                        $status_label = 'HÀNG CÓ SẴN';
		                                    } elseif( in_array('outstock', $status) ){
		                                        $status_label = 'TẠM HẾT HÀNG';
		                                    } elseif( in_array('demo', $status) ){
		                                        $status_label = 'BỎ MẪU';
		                                    } elseif( in_array('stop', $status) ){
		                                        $status_label = 'NGỪNG KINH DOANH';
		                                    } else {
		                                        $status_label = 'HÀNG CÓ SẴN';
		                                    }
		                                    @endphp

                                            
                                        </p>
                                        <p>Tình trạng: <b>{!! $status_label !!}</b></p>
                                    </li>

                                    <li>
                                        <div class="best-price pl-2 py-2 pr-3 mt-3">
                                            <span>CAM KẾT: GIÁ TỐT NHẤT THỊ TRƯỜNG</span>
                                        </div>
                                    </li>
                                    @if($attributes && ($attributes[0]['name'] !== null || $attributes[0]['value'] !== null) )
                                    <li class="gift-product mb-2 mt-4">
                                    	<img src="/public/themes/default/images/qua-tang.png" alt="quà tặng icon">
                                    	<div>
                                    @forelse($attributes as $attribute)
                                        @if( $attribute['name'] !== null || $attribute['value'] !== null )
                                        <label>{!! $attribute['name'] !!}</label>- {!! $attribute['value'] !!} 
                                        @endif
                                    @empty
                                    @endforelse
                                    	</div>
                                    </li>
                                    @endif

                                    @if($colors->count())
                                    <li>
                                        <label>{{ __('site.product_color') }}:</label>
                                        <div class="color-list">
                                            @forelse($colors as $key => $color)
                                            <button {!! ($key == 0) ? 'class="active"' : '' !!} style="background-color: {{ $color->value }};" data-id="{{ $color->id }}" ><i class="fa fa-check"></i></button>
                                            @empty
                                            @endforelse
                                        </div>
                                    </li>
                                    @endif

                                    @if($sizes->count())
                                    <li class="mb-3">
                                        <label>{{ __('site.product_size') }}:</label>
                                        <div class="size-list">
                                            @forelse($sizes as $key => $size)
                                            <button {!! ($key == 0) ? 'class="active"' : '' !!} data-id="{{ $size->id }}" ><i class="fa fa-check"></i> {{ $size->title }} </button>
                                            @empty
                                            @endforelse
                                        </div>
                                    </li>
                                    @endif

                                    <li class="mb-3 py-3 border-top border-bottom">
                                    	@if ( strpos($product->status,'banner') !== false )
                                        	<img class="mb-2" src="/public/images/khuyen-mai-lap-dat.jpg" alt="khuyến mãi lắp đặt">
                                        
                                        @endif
                                        
                                        <label>{{ __('cart.quantity') }}:</label>
                                        <div class="product-quantity">
                                            <input type="text" name="quantity" value="1">
                                        </div>
                                    </li>

                                    <li class="mb-3">
                                        <div class="d-inline-block">
                                            <a href="#" class="btn btn-success mr-3 add-to-cart ad-to-card" data-ajax="id={{ $product->id }}"><span class="d-block text-uppercase font-weight-bold p-0 mb-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" width="24" height="24"><path class="heroicon-ui" d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm1-9h2a1 1 0 0 1 0 2h-2v2a1 1 0 0 1-2 0v-2H9a1 1 0 0 1 0-2h2V9a1 1 0 0 1 2 0v2z"/></svg>

                                                </span><span>Thêm vào giỏ hàng</span>
                                            </a>

                                            <a href="#" id="add-to-cart" class="btn btn-danger mr-3 ad-to-card" data-ajax="id={{ $product->id }}"><span class="d-block text-uppercase font-weight-bold p-0 mb-1">MUA NGAY</span><span>Đặt Hàng Online Giá Siêu Tốt</span></a>
                                        </div>
                                    </li>

                                    <li class="ad-to-card-li mb-3">
                                        <a href="#modal-request" data-toggle="modal" class="btn phone-back btn-success text-uppercase position-relative request-call">
                                            <svg><rect></rect></svg>
                                            <span class="m-0 mb-1 d-block text-white"><i class="fa fa-volume-control-phone fa-fw"></i> Yêu cầu gọi lại tư vấn</span>
                                            <span class="text-white">Nhân viên CSKH gọi lại cho bạn</span>
                                        </a>
                                        <?php /*
                                        <a href="tel:0828100100" class="btn btn-danger mr-3 ad-to-card" title="đặt hàng" style="color: #217500;font-size:14px" id="call-mobile">
                                            <i class="fa fa-phone mr-2" aria-hidden="true"></i>
                                            <b>0828 100 100</b></a>
                                            <a href="tel:0828100200" title="đặt hàng" style="color: #217500;font-size:14px" id="call-mobile">- <b>0828 100 200</b></a>
                                        */ ?>
                                    </li>

                                    @if($product->description)
                                    <li class="bg-light p-3 mb-3">{!! nl2br($product->description) !!}</li>
                                    @endif

                                    <li>
                                        <label>Chia sẻ:</label>
                                        <div class="share-icons">
                                            <a target="_blank" class="facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}"><i class="fa fa-facebook"></i> facebook</a>
                                            <a target="_blank" class="twitter" href="https://twitter.com/home?status={{ url()->current() }}"><i class="fa fa-twitter"></i> twitter</a>
                                            <a target="_blank" class="google" href="https://plus.google.com/share?url={{ url()->current() }}"><i class="fa fa-google-plus"></i> google</a>
                                            <a target="_blank" class="pinterest" href="https://pinterest.com/pin/create/button/?url={{ url()->current() }}&media={{ asset('uploads/products/'.$product->image) }}&description={{ $product->description }}"><i class="fa fa-pinterest"></i> pinterest</a>
                                        </div>
                                    </li>
                                    <li>
                                        <i class="fa fa-arrow-left" aria-hidden="true"></i><input type="button" style="    border: none;background: none;margin-left: 8px;cursor: pointer;" value="Về trang trước" onclick="history.back(-1)" />
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="product-contents row">
                    <div class="col-md-8 col-sm-12 entry_pro">
                         <div>
                            <ul class="nav nav-tabs nav-pills" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tab-1">Giới thiệu sản phẩm</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="productTabContent">
                                <div class="tab-pane fade py-3 show active" role="tabpanel" id="tab-1">
                                    {!! $product->contents !!}
                                </div>
                            </div>
                        </div>
{{--                        <div>--}}
{{--                            <ul class="nav nav-tabs nav-pills" role="tablist">--}}
{{--                                <li class="nav-item">--}}
{{--                                    <a class="nav-link active" data-toggle="tab">Thông số kỹ thuật</a>--}}
{{--                                </li>--}}
{{--                            </ul>--}}
{{--                            <div class="tab-content" id="productTabContent">--}}
{{--                                <div class="tab-pane fade py-3 show active" role="tabpanel">--}}
{{--                                    {!! $product->specifications !!}--}}
{{--                                </div>  --}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div>
                            <ul class="nav nav-tabs nav-pills" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tab-3">Đánh giá & Hỏi đáp tư vấn sản phẩm</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="productTabContent">
                                <div class="tab-pane fade py-3 show active" role="tabpanel" id="tab-3">
                                    @include('frontend.default.blocks.comment')
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div> 
                    </div>
                    <div class="col-md-4 col-sm-12">
                        
                        @if(@$category)

                        {!! $category->contents !!}

                        @endif

                        <!-- <a href="https://dienmaygiasi.vn/may-giat-long-ngang-electrolux-ewf12844s-8kg-inverter"><img src="/public/images/banner-may-giat.jpg" alt="banner máy giặt"></a> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h2 class="section-title">{{ __('site.product_other') }}</h2>
                <div class="slick-product-other row">
                    @forelse($products as $val)
                    <div>
                        {!! get_template_product($val,$type,1) !!}
                    </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
<!-- PAGE SECTION END -->
<div class="modal fade" id="modal-request" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <div class="modal-body">
                <h5 class="title">{{ $product->title }}</h5>
                <div class="product-details">
                    <div class="row">
                        <?php /*
                        <div class="col-md-5 offset-md-0 col-sm-8 offset-sm-2 col-12 mb-4">
                            <div class="image">
                                <img src="{{ ( $product->image && file_exists(public_path('/uploads/products/'.$product->image)) ? asset( 'public/uploads/products/'.get_thumbnail($product->image,'_medium') ) : asset('noimage/500x450') ) }}" alt="{{ $product->alt }}" />
                            </div>
                        </div>
                        */ ?>
                        <div class="col-md-12 col-sm-12 col-12 mb-3">
                            <div class="info">
                                <ul>
                                    <li class="bg-danger mb-2"><span class="price text-white ml-2">Giá bán: {!! get_template_product_price($product->wholesale_price,$product->sale_price) !!}</span></li>
                                    <li class="pt-0">
                                        <span><b>Mã sản phẩm:</b> {{ $product->code }}</span> 
                                        <p><b>Đánh giá:</b> <span class="text-warning"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star-half-o" aria-hidden="true"></i></span>4.5/5</p>
                                    </li>
                                    <li class="best-price px-2 py-2">
                                        <img src="https://dienmaygiasi.vn/public/images/gold-medal.svg" alt="icon medal"> <span>CAM KẾT GIÁ TỐT NHẤT - RẺ HƠN MỌI SIÊU THỊ</span>
                                    </li>

                                    @forelse($attributes as $attribute)
                                        @if( $attribute['name'] !== null && $attribute['value'] !== null )
                                        <li><label>{!! $attribute['name'] !!}</label>: {!! $attribute['value'] !!} </li>
                                        @endif
                                    @empty
                                    @endforelse

                                    @if($product->description)
                                    <li class="bg-light p-3 mb-3 d-none d-md-block">{!! nl2br($product->description) !!}</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    <form class="request-form" role="form" method="POST" action="#">
                        <input type="hidden" name="title" value="{{ $product->title }}">
                        <div class="form-group row">
                            <div class="col-sm-4 pr-sm-0 mb-3"><input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Tên của bạn"></div>
                            <div class="col-sm-5 mb-3"><input type="text" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="Số điện thoại"></div>
                            <div class="col-sm-3 pl-sm-0 mb-3"><button type="button" class="btn btn-block btn-primary text-uppercase btn-ajax" data-ajax="act=call-back-request|type=call-back-request">Gọi lại ngay</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_script')
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "{{ $product->title }}",
  "image": [
    "{{ ( $product->image && file_exists(public_path('/uploads/products/'.$product->image)) ? asset( 'public/uploads/products/'.$product->image ) : asset('noimage/500x450') ) }}"
   ],
  "description": "{{ $product->description }}",
  "sku": "{{ $product->code }}",
  "mpn": "{{ $product->code }}",
  "brand": {
    "@type": "Thing",
    "name": "{{ @$product->supplier }}"
  },
  "review": {
    "@type": "Review",
    "reviewRating": {
      "@type": "Rating",
      "ratingValue": "4",
      "bestRating": "5"
    },
    "author": {
      "@type": "Person",
      "name": "Hoàng Oanh"
    }
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.5",
    "reviewCount": "89"
  },
  "offers": {
    "@type": "Offer",
    "url": "{{ url()->current() }}",
    "priceCurrency": "VND",
    "price": "@php
        if( $product->wholesale_price > 0 && $product->sale_price == 0){
			echo $product->wholesale_price;
		}elseif($product->sale_price > 0){
            echo $product->sale_price;
		}else{
			echo 0;
		}
    @endphp",
    "priceValidUntil": "{{ $product->created_at }}",
    "itemCondition": "https://schema.org/UsedCondition",
    "availability": "InStock",
    "seller": {
      "@type": "Organization",
      "name": "Hoàng Oanh"
    }
  }
}
</script>

@endsection