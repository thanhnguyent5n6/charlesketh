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
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/default/css/responsive.css') }}">
    @yield('custom_css')
    {!! config('settings.script_head') !!}
    <link rel="manifest" href="/manifest.json" />
        <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
        <script>
          var OneSignal = window.OneSignal || [];
          OneSignal.push(function() {
            OneSignal.init({
              appId: "bffe339c-dacd-4027-8221-9e1faa0def6e",
            });
          });
        </script>

</head>
<body {!! $site['class'] ? 'class="'.$site['class'].'"' : '' !!} >
    <div id="fb-root"></div>
    <script async defer>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/{{ config('siteconfig.social.'.app()->getLocale()) }}/sdk.js#xfbml=1&version=v2.12&autoLogAppEvents=1';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

    <div class="wrapper">
        @include('frontend.default.layouts.header')

        @include('frontend.default.layouts.search')
        
        @if(Route::currentRouteName() != 'frontend.home.index')
            @include('frontend.default.layouts.breadcrumb')
        @endif
        
        @yield('content')
        
        @include('frontend.default.layouts.newsletter')
        @include('frontend.default.layouts.footer')
        @include('frontend.default.blocks.modals')
    </div>
    <!-- Body main wrapper end -->
    
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' =>  csrf_token(),
            'baseUrl'   =>  url('/'),
        ]) !!}
    </script>
    <script src="{{ asset('jsons/province.js') }}"></script>
    <script src="{{ asset('jsons/district.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery-migrate.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    <script src="{{ asset('js/waves.min.js') }}"></script>
    <script src="{{ asset('themes/default/js/plugins.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('themes/default/js/main.js') }}"></script>

    @yield('custom_script')

    {!! config('settings.script_body') !!}

    @if( @$SchemaOrg )
    {!! $SchemaOrg->toScript() !!}
    @endif
</body>

</html>