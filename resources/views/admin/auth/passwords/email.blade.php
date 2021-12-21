<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Quản trị website') }}</title>
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=all">
    <link rel="stylesheet" href="{{ asset('packages/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/simple-line-icons/simple-line-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/bootstrap-switch/css/bootstrap-switch.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/plugins.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/login.css') }}">
    <link href="{{ asset('uploads/photos/'.config('settings.favicon')) }}" rel="shortcut icon" type="image/x-icon" />
</head>
<body class="login">
    <div class="logo">
        @if( config('settings.logo') && file_exists(public_path('/uploads/photos/'.config('settings.logo'))) )
        <a href="{{ url('/') }}"> <img src="{{ asset('uploads/photos/'.config('settings.logo')) }}" alt="main logo"> </a>
        @endif
    </div>
    <div class="content">
        <form class="forget-form" role="form" method="POST" action="{{ route('admin.password.email') }}">
            {{ csrf_field() }}
            <h3 class="form-title font-green uppercase">Quên mật khẩu</h3>
            <p> Nhập địa chỉ Email của bạn để đặt lại mật khẩu </p>
            <div class="row">@include('admin.blocks.messages')</div>
            <div class="form-group">
                <label for="email" class="control-label visible-ie8 visible-ie9">E-Mail Address</label>
                <input id="email" type="email" class="form-control form-control-solid placeholder-no-fix" name="email" value="{{ old('email') }}" autocomplete="off" placeholder="Email">
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.login') }}" class="btn grey-salsa uppercase">Quay lại</a>
                <button type="submit" class="btn green uppercase"> Gửi mật khẩu </button>
            </div>
        </form>
    </div>

    <!-- SCRIPT -->
    <script>
        @php
        $routeArray = explode('.',Route::currentRouteName());
        $routeName = $routeArray[1].'.'.( isset($_GET['type']) ? $_GET['type'] : '');
        @endphp
        window.Laravel = {!! json_encode([
            'csrfToken' =>  csrf_token(),
            'baseUrl'   =>  url('/'),
            'routeName'   =>  $routeName,
        ]) !!}
    </script>
    <!-- BEGIN CORE PLUGINS -->
    <script src="{{ asset('packages/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('packages/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('packages/bootstrap-switch/js/bootstrap-switch.min.js') }}" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->

    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('admin/js/app.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
</body>
</html>