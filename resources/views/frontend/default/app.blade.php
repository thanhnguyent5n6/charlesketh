<!Doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="robots" content="index, follow, noodp" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ @$meta_seo->title }}</title>
    <meta name="keywords" content="{{ @$meta_seo->keywords }}">
    <meta name="description" content="{{ @$meta_seo->description }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ @$meta_seo->title }}">
    <meta itemprop="description" content="{{ @$meta_seo->description }}">
    <meta itemprop="image" content="{{ @$meta_seo->image }}">
    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="{{ config('settings.site_name') }}">
    <meta name="twitter:title" content="{{ @$meta_seo->title }}">
    <meta name="twitter:description" content="{{ @$meta_seo->description }}">
    <meta name="twitter:image:src" content="{{ @$meta_seo->image }}">
    <!-- Open Graph data -->
    <meta property="og:title" content="{{ @$meta_seo->title }}" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image" content="{{ @$meta_seo->image }}" />
    <meta property="og:description" content="{{ @$meta_seo->description }}" />
    <meta property="og:site_name" content="{{ config('settings.site_name') }}" />
    <meta property="fb:admins" content="{{ config('settings.facebook_app_id') }}" />
    <!-- Geo data -->
    <meta name="geo.placename" content="Viet Nam" />
    <meta name="geo.position" content="x;x" />
    <meta name="geo.region" content="VN" />
    <meta name="ICBM" content="" />
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('uploads/photos/'.config('settings.favicon')) }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-social.css') }}">
    <link rel="stylesheet" href="{{ asset('css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pe-icon-7-stroke.css') }}">
    <link rel="stylesheet" href="{{ asset('css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('css/waves.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/plugins.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery.fancybox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/responsive.css') }}">
    <link rel="manifest" href="/manifest.json" />
    <script type="application/ld+json">
    {
		"@context": "http://schema.org",
		"@type": "Website",
		"url": "{{ url()->current() }}",
		"potentialAction": [{
			"@type": "SearchAction",
			"target": "{{ url()->current() }}/tim-kiem.html&keywords={searchbox_target}",
			"query-input": "required name=searchbox_target"
		}]
    }
    </script>

    @yield('custom_css')
    {!! config('settings.script_head') !!}
</head>
<body {!! $site['class'] ? 'class="'.$site['class'].'"' : '' !!} >
   	<div class="wrapper">
        <div class="header-cart">
            <!-- Cart Toggle -->
            <a class="cart-toggle" href="javascript:;">
                <i class="pe-7s-cart"></i>
                <span class="countCart"></span>
            </a>
            <!-- Mini Cart Brief -->
            <div class="mini-cart">
                <div class="cart-top">
                    <p>Hiện có <span class="countCart">0</span> sản phẩm trong giỏ hàng</p>
                </div>
                <!-- Cart Products -->
                <div class="cart-items clearfix"></div>
                <!-- Cart Total -->
                <div class="cart-total">
                    <p>Tổng tiền <span class="float-right sumOrderPrice"></span></p>
                </div>
                <!-- Cart Button -->
                <div class="cart-bottom clearfix">
                    <a href="{{ route('frontend.cart.index') }}" class="btn btn-dark btn-lg float-left">Giỏ hàng</a>
                    <a href="{{ route('frontend.cart.checkout') }}" class="btn btn-danger btn-lg float-right">Thanh toán</a>
                </div>
            </div>
        </div>

		@include('frontend.default.layouts.header')
        @include('frontend.default.layouts.navbar')

		@if(Route::currentRouteName() == 'frontend.home.index')
            @include('frontend.default.layouts.slideshow')
        @else
            @include('frontend.default.layouts.breadcrumb')
		@endif

		@yield('content')
        @include('frontend.default.layouts.footer')
		@include('frontend.default.blocks.modals')
        @include('frontend.default.blocks.popup')

	</div>
	<!-- Body main wrapper end -->

    <div class="hotline position-fixed">
        <p class="text-white d-inlineblock border-bottom text-right"> Phòng Kinh Doanh</p>
        <a target="blank" href="tel:0896618818" class="d-flex align-items-center position-relative hotline-fix">
            <p>1</p>
            <i class="fa fa-phone"></i>
            <span>Kinh Doanh<br><b>0896 618 818</b></span>
        </a>
        <a href="tel:0896619196" class="d-flex align-items-center position-relative hotline-fix mb-2">
            <p>2</p>
            <i class="fa fa-phone"></i>
            <span>Kinh Doanh<br><b>089 661 9196</b></span>
        </a>
        <?php /*
        <p class="text-white d-inlineblock border-bottom text-right">Phòng Sỉ</p>
        <a href="tel:0824100100" class="d-flex align-items-center hotline-fix">
            <i class="fa fa-phone"></i>
            <span>Liên Hệ Phòng Sỉ<br><b>0824 100 100</b></span><br>
        </a>

        <div id="fb-root"></div>
    <script async defer>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/{{ config('siteconfig.social.'.app()->getLocale()) }}/sdk.js#xfbml=1&version=v3.2&autoLogAppEvents=1';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
        */ ?>
    </div>
    <script>
        $("#mobile-menu-toggle").click(function(){
            $(this).parent().find('.nav-dropdown-default').slideToggle();
            let down = '<i class="fa fa-caret-down" aria-hidden="true"></i>';
            let up = '<i class="fa fa-caret-up" aria-hidden="true"></i>';
            let child = $(this).find('.carret-item');
            console.log(child);
            console.log($(child).attr('data-carret'));
            if($(this).attr('data-carret') == 'up') {
                $(this).attr('data-carret', 'down');
                $(child).html(down);
                return;
            } else {
                $(this).attr('data-carret', 'up');
                $(child).html(up);
                return;
            }
        });
    </script>
</body>
</html>