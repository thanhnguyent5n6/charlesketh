@extends('frontend.default.app')
@section('content')
<!-- PAGE SECTION START -->
<section class="page-section pb-4">
    <div class="container">
        <div class="row">
            <?php /*
            <div class="col-12 col-md-4 col-lg-3 single-post">
                <div class="sidebar">
                    <div class="sidebar-widget">
                        <h3 class="title bg-warning text-dark">KINH NGHIỆM MUA SẮM</h3>
                        <ul class="category">
                            <li><a href="/tin-tuc/huong-dan-su-dung" class="no-arrow"> <img src="/public/themes/default/images/005-smart.svg" alt="">Hướng Dẫn Sử Dụng</a>
                            </li>
                            <li><a href="/tin-tuc/cau-hoi-thuong-gap" class="no-arrow"> <img src="/public/themes/default/images/005-smart.svg" alt="">Câu Hỏi Thường Gặp</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            */ ?>
            <article class="col-12 col-md-8 col-lg-8">
                <div class="post-detail">
                    <h1 class="title">{{ $post->title }}</h1>
                    <div class="meta">
                        {{--<span><a href="{{ url('/'.$type.'/'.$category->slug) }}"><i class="fa fa-tags"></i> {{ @$category->title }} </a></span>--}}
                        <span><a><i class="fa fa-user"></i> {{ @$author->name }}</a></span>
                        <span><a><i class="fa fa-eye"></i> {{ __('site.view') }} ({{ $post->viewed }})</a></span>
                    </div>
                    <div class="desc">
                        {{ $post->description }}
                    </div>
                    <div class="image">
                        <img alt="" src="{{ asset('uploads/posts/'.$post->image) }}">
                    </div>
                    <div class="content">{!! $post->contents !!}</div>
                     <div>
                         Đánh giá: <i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star-o" aria-hidden="true"></i>
                     </div>
                     <p>Like và chia sẻ:</p>
                     <div class="fb-like" data-href="{{ url()->current() }}" data-width="" data-layout="button_count" data-action="like" data-size="small" data-show-faces="true" data-share="true"></div>
                </div>
            </article>
                <div class="col-12 col-lg-3">
                <h3>Sản phẩm khuyến mãi</h3>
                <div class="row">
                	@forelse($products as $val)
	                    {!! get_template_product($val,'san-pham',2,'col-sm-4 col-md-3 col-lg-12 p-0') !!}
	                @empty
	                @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
<!-- PAGE SECTION END -->
@endsection