<div class="sidebar">
    <div class="sidebar-widget mb-4">
        <h4 class="title">
            <span class="member-name-box">
            <span class="member-name-box__letter">
                @php
                    $arr_name = explode( ' ', auth()->guard('member')->user()->name );
                    echo $letter = substr($arr_name[count($arr_name)-2], 0 , 1);
                    echo $letter = substr($arr_name[count($arr_name)-1], 0 , 1);
                @endphp
            </span>
            <span class="member-name-box__of">Tài khoản của</span>
            <span class="member-name-box__name">{{ auth()->guard('member')->user()->name }}</span>
            </span>
        </h4>
        <ul class="category">
            <li> <a href="{{ route('frontend.member.profile') }}" {!! (Route::currentRouteName() == 'frontend.member.profile') ? 'class="active"' : '' !!} > {{ __('account.profile') }} </a> </li>
            <li> <a href=""> {{ __('account.notification') }} </a> </li>
            <li> <a href="{{ route('frontend.member.order') }}" {!! (Route::currentRouteName() == 'frontend.member.order') ? 'class="active"' : '' !!} > {{ __('account.order_management') }} </a> </li>
            <li> <a href=""> {{ __('account.delivery_address') }} </a> </li>
            <li> <a href="{{ route('frontend.home.viewed') }}"> {{ __('account.viewed') }} </a> </li>
            <li> <a href="{{ route('frontend.wishlist.index') }}"> {{ __('account.wishlist') }} </a> </li>
        </ul>
    </div>
</div>