@extends('frontend.default.app')
@section('content')
<!-- PAGE SECTION START -->
<section class="page-section pb-4">
    <form action="#">
        <div class="container">
            <h3 class="section-title-line"> {{ __('cart.billing_details') }} </h3>
            <div class="row">
                <div class="col-12">
                    <div class="cart-table table-responsive mb-4">
                        <table>
                            <thead>
                                <tr>
                                    <th class="pro-thumbnail"> {{ __('cart.photo') }} </th>
                                    <th class="pro-title"> {{ __('cart.product_name') }} </th>
                                    <th class="pro-price"> {{ __('cart.price') }} (Đ)</th>
                                    <th class="pro-quantity"> {{ __('cart.quantity') }} </th>
                                    <th class="pro-subtotal"> {{ __('cart.total') }} (Đ)</th>
                                    <th class="pro-remove"> {{ __('cart.delete') }} </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cart as $key => $val)
                                <tr class="pro-key-{{ $key }}">
                                    <td class="pro-thumbnail"><a href="#"><img src="{{ $val['image'] }}" alt="" /></a></td>
                                    <td class="pro-title"><a href="#">{{ $val['title'] }}</a></td>
                                    <td class="pro-price"><span class="amount">{!! get_currency_vn($val['price'],'') !!}</span></td>
                                    <td class="pro-quantity"><div class="product-quantity"><input type="text" value="{{ $val['qty'] }}" class="update-cart" data-ajax="key={{ $key }}" /></div></td>
                                    <td class="pro-subtotal sumProPrice">{!! get_currency_vn($val['price']*$val['qty'],'') !!}</td>
                                    <td class="pro-remove"><a href="#" class="delete-cart" data-ajax="key={{ $key }}" >×</a></td>
                                </tr>
                                @empty
                                <tr> <td colspan="30"> {{ __('cart.no_item') }} </td> </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-8 col-sm-7 col-12 mb-3">
                    <div class="cart-buttons mb-3">
                        <a href="{{ url('/dien-thoai') }}" class="btn btn-dark btn-lg text-uppercase text-white"> {{ __('cart.continue_shopping') }} </a>
                    </div>
                    <div class="cart-coupon mb-3">
                        <h4>Coupon</h4>
                        <p> {{ __('cart.enter_coupon') }} </p>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="{{ __('cart.coupon_code') }}" value="{{ @$coupon['code'] }}" />
                            <div class="input-group-append">
                                <button type="button" class="btn btn-dark">{{ __('cart.use') }}</button>
                            </div>
                        </div>
                    </div>
                    <div id="result-coupon">
                        @if( $coupon )
                        <div class="custom-alerts alert alert-{{ $coupon['effective']['type'] }} fade in">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            <i class="fa-lg fa fa-{{ $coupon['effective']['icon'] }}"></i> {!! $coupon['effective']['message'] !!}
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 col-sm-5 col-12 mb-3">
                    <div class="cart-total">
                        <h3> {{ __('cart.cart_total') }} </h3>
                        <table>
                            <tbody>
                                <tr class="cart-subtotal">
                                    <th> {{ __('cart.total') }} </th>
                                    <td><span class="amount sumCartPrice"></span></td>
                                </tr>
                                <tr class="order-total">
                                    <th> {{ __('cart.order_total') }} </th>
                                    <td>
                                        <strong><span class="amount sumOrderPrice"></span></strong>
                                    </td>
                                </tr>                                           
                            </tbody>
                        </table>
                        <div class="proceed-to-checkout">
                            <a href="{{ route('frontend.cart.checkout') }}" class="btn btn-danger btn-lg text-uppercase text-white"> {{ __('cart.checkout') }} </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form> 
</section>
<!-- PAGE SECTION END --> 
@endsection