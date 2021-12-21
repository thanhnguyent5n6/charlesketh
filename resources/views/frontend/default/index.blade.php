@extends('frontend.default.app')
@section('content')
<div class="container">
    <div class="row index-suggest">
        <div  class="col small-12 large-12">
            <div class="col-inner">
                <div class="container section-title-container">
                    <h3 class="section-title section-title-center"><b></b>
                        <span class="section-title-main" style="color:rgb(179, 152, 81);">Đề xuất của chúng tôi</span>
                        <b></b>
                    </h3>
                </div>
                <p style="text-align: center;">
                    <span style="font-size: 95%; color: #808080;">
                        <em>Ngoài 2 chuyên mục lớn Vang và Whisky, Quý vị sẽ khám phá nhanh thế giới đồ uống khác tại các danh mục nổi bật.</em>
                    </span>
                </p>
                <div class="banner-grid-wrapper row">
                    @foreach($category_tops as $top)
                        <div class="col-md-6 col-sm-12">
                            <div class="banner-grid-wrapper-content">
                            <a href="{{ $top->slug }}">
                                <div class="cate-title">
                                    <span>{{ $top->title }}</span>
                                </div>
                                <img src="{{ ( $top->image && file_exists(public_path('/uploads/categories/'.$top->image)) ? asset( '/uploads/categories/'.$top->image ) : asset('noimage/335x260') ) }}" alt="{{ $top->alt }}" /></a>
                            </div>
                        </div>
                    @endforeach
                    <div class="line"></div>
                    @foreach($category_bottom as $bot)
                        <div class="col-md-3 col-sm-6">
                            <div class="banner-grid-wrapper-content">
                            <a href="{{ $bot->slug }}">
                                <div class="cate-title">
                                    <span>{{ $bot->title }}</span>
                                </div>
                                <img src="{{ ( $bot->image && file_exists(public_path('/uploads/categories/'.$bot->image)) ? asset( '/uploads/categories/'.$bot->image ) : asset('noimage/335x260') ) }}" alt="{{ $bot->alt }}" /></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row index-suggest">
        <div  class="col small-12 large-12">
            <div class="col-inner">
                <div class="container section-title-container">
                    <h3 class="section-title section-title-center"><b></b>
                        <span class="section-title-main" style="color:rgb(179, 152, 81);">Sản phẩm nổi bật</span>
                        <b></b>
                    </h3>
                </div>
                <div class="banner-grid-wrapper row">
                    <div class="row m-0 bg-white">
                        @forelse($products as $val)
                            {!! get_template_product($val,'san-pham',4,'p-0') !!}
                        @empty
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<section class="page-section py-3">
    <div class="container">
        <div class="row">
            <div class="col-xl-12 col-12 sm-order-first">
                <?php /*
                <br>  
                <h2 class="section-title-line"><span class="circle-animate"></span> Giá Tốt Mỗi Ngày</h2> 
                {{--<img src="/public/images/gia-tot.jpg" alt="giá tốt mỗi ngày">--}}             
                <div class="row m-0 bg-white">
                    @forelse($products as $val)
                        {!! get_template_product($val,'san-pham',4,'p-0') !!}
                    @empty
                    @endforelse
                </div> 
                */ ?>
                <?php /*<section class="page-section section-tabs">
                    <div class="position-relative">
                        @forelse($category_tabs as $tab)
                        @php $product_tabs = get_product_by_category($tab->id,$tab->type); @endphp
                        <h2 class="section-title-line">{{ $tab->title }}</h2>
                        {{--
                        <ul class="nav">
                            @forelse($category_tabs as $tab)
                            <li class="nav-item">
                                <a href="#tab-{{ $tab->id }}" class="nav-link" data-toggle="tab" data-ajax="act=products|category_id={{ $tab->id }}">{{ $tab->title }}</a></li>
                            @empty
                            @endforelse
                        </ul>
                        --}}
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="tab-content bg-white">
                                    <div class="tab-pane fade show active" id="tab-0">
                                        <div class="row m-0">
                                        @forelse($product_tabs as $val)
                                            {!! get_template_product($val,'san-pham',4,'p-0') !!}
                                        @empty
                                        @endforelse
                                        </div>
                                    </div>
                                </div>
                                @if( $tab->image && file_exists(public_path('/uploads/categories/'.$tab->image) ) )
                                <div class="pt-3"><a href="{{ $tab->link }}"><img src="{{ asset( 'public/uploads/categories/'.$tab->image ) }}" alt="{{ $tab->alt }}" /></a></div>
                                @endif
                            </div>
                        </div>
                        @empty
                        @endforelse
                    </div>
                </section> */ ?>
            </div>
            <div class="col-12">
                @php
                    $about = get_pages('gioi-thieu',$lang);
                @endphp
                {!! $about->contents !!}
            </div>
        </div>
    </div>
</section>

@php $deals = get_photos('deal',$lang); @endphp
<section class="deal-section pb-3">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="slick-deals">
                @forelse($deals as $deal)
                    <div>
                        <a href="{{ $deal->link }}" class="d-block px-3"><img src="{{ ( $deal->image && file_exists(public_path('/uploads/photos/'.$deal->image)) ? asset( 'public/uploads/photos/'.$deal->image ) : asset('noimage/335x260') ) }}" alt="{{ $deal->alt }}" /></a>
                    </div>
                @empty
                @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

@endsection