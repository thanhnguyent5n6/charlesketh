
@if(@$bannerTop && count(@$bannerTop) > 0)
<div class="text-center d-none d-md-block">
    <a href="{{ $bannerTop[0]->link }}"><img src="{{ asset('uploads/photos/'.$bannerTop[0]->image) }}"></a>
</div>
@endif
<header class="header-section sticker" data-offset-top="50" data-limit=".footer-section">
    <div class="header-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-3 col pr-0">
                    <div class="header-logo">
                        @if(Route::currentRouteName() == 'frontend.home.index')
                        <h1>
                            <a href="{{ url('/') }}">
{{--                                <img src="{{ asset('uploads/photos/'.config('settings.logo')) }}" alt="logo">--}}
                                <img src="https://vangchat.com.vn/wp-content/uploads/2019/06/logo-vangchat.png" alt="logo">
                            </a>

                        </h1>
                        @else
                        <h2><a href="{{ url('/') }}">
{{--                                <img src="{{ asset('uploads/photos/'.config('settings.logo')) }}" alt="logo">--}}
                                <img src="https://vangchat.com.vn/wp-content/uploads/2019/06/logo-vangchat.png" alt="logo">
                            </a>
                        </h2>
                        @endif
                    </div>
                </div>
                <div class="col-xl-5 col-auto order-xl-last pl-0">
                    <div class="header-info">
                        <div>
                            <span>Sản phẩm <a href="{{ route('frontend.home.viewed') }}">Bạn đã xem</a></span>
                        </div>
                        <div>
                            <i class="pe-7s-note2"></i>
                            <span>Theo dõi<a href="{{ route('frontend.cart.tracking') }}">Đơn hàng</a></span>
                        </div>
                        <div>
                            <i class="pe-7s-phone"></i>
                            <span>
                                <a href="tel:{{ config('settings.site_hotline') }}">Hotline</a>
                                <a href="tel:0974258196">0123 456 789</a>
                            </span>
                        </div>
                        
                    </div>
                </div>
                <div class="col-xl-4 col-12 text-center">
                    <div class="search-form">
                        <div class="header-search">
                            {{--
                            <select name="category" class="selectpicker" title="Danh mục">
                                @php
                                    Menu::resetMenu();
                                    Menu::setMenu($categories);
                                    echo Menu::getMenuSelect(0,0,'',Request::get('category'));
                                @endphp
                            </select>--}}
                            <input type="text" name="keyword" class="form-control typeahead" value="{{ Request::get('keyword') }}" placeholder="Tên hoặc mã sản phẩm bạn cần tìm" autocomplete="off">
                            <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                        </div>
                        <div class="search-result"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>