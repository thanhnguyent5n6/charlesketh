@extends('frontend.default.app')
@section('content')
<!-- PAGE SECTION START -->
<section class="page-section ptb-60">
    <div class="container">
        <div class="row">
            @include('frontend.default.blocks.messages')
            <div class="checkout-form col-12">
                <form method="post" action="{{ url('/thanh-toan') }}">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-lg-8 col-md-7 col-12 mb-3">
                            <h3> {{ __('cart.billing_details') }} </h3>
                            <div class="row">
                            	<div class="col-12">
                            		<div class="alert alert-success rounded-0">
		                            	<div class="form-check">
									    <input type="radio" class="form-check-input" name="exampleCheck1" checked>
									    <label class="form-check-label" for="exampleCheck1" >Thanh Toán Khi Nhận Hàng (mặc định)</label>
									  </div>
									</div>
                            	</div>
                            	<div class="col-12">
		                            <div class="alert alert-danger rounded-0">
		                            	<div class="form-check">
										    <input type="radio" class="form-check-input" name="exampleCheck1" id="form-payment">
										    <label class="form-check-label" for="exampleCheck1" >Thanh Toán Chuyển Khoản</label>
										   	<div class="hidden-out">
				                            	<b>Tất cả các đơn hàng Online được thanh toán theo hình thức COD (giao hàng nhận tiền) - Riêng đối với những đơn hàng có yêu cầu chuyển khoản thanh toán. Quý khách vui lòng chuyển khoản qua tài khoản CTY bên dưới:</b><br>
				                            	<b>THÔNG TIN THANH TOÁN</b>
				                            	<p>- Ngân hàng Thương mại cổ phần Kỹ Thương Việt Nam (TECHCOMBANK)</p>
				                            	<p>- NGUYỄN VĂN A</p>
				                            	<p>- Chi nhánh: HÀ NỘI</p>
				                            	<p>- STK: 0999999999</p>
		                            		</div>
									 	 </div>								 	 
									</div>
								</div>
							</div>
							
                            <div class="form-group">
                                <label for="name">{{ __('cart.name') }} (<span class="required">*</span>)</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}"/>
                            </div>
                            {{--
                            <div class="form-group">
                                <label for="address">{{ __('cart.address') }} (<span class="required">*</span>)</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address') }}" />
                            </div>
                            --}}
                            <div class="form-group">
                                <label for="email">Email (<span class="required">*</span>)</label>                                      
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" />
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">{{ __('cart.phone') }} (<span class="required">*</span>)</label>                                     
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" />
                            </div>
                            {{--
                            <div class="form-group row">
                                <div class="col">
                                    <label for="province">{{ __('cart.province') }} (<span class="required">*</span>)</label>
                                    <select class="selectpicker show-tick show-menu-arrow province form-control" name="province_id" data-group="order" data-live-search="true" title="-- Chọn Tỉnh / Thành phố --">
                                        <option value="{{ old('province_id') }}" selected ></option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="district">{{ __('cart.district') }} (<span class="required">*</span>)</label>
                                    <select class="selectpicker show-tick show-menu-arrow district form-control" name="district_id" data-group="order" data-live-search="true" title="-- Chọn Quận / Huyện --">
                                        <option value="{{ old('district_id') }}" selected ></option>
                                    </select>
                                </div>
                            </div>
                            --}}
                            <div class="form-group order-notes">
                                <label for="order_note">{{ __('cart.notes') }}</label>
                                <textarea name="order_note" class="form-control" rows="5">{{ old('order_note') }}</textarea>
                            </div>
                                                                 
                        </div>
                        <div class="col-lg-4 col-md-5 col-12 mb-3">
                            <div class="coupon-form mb-4">
                                <div class="cart-coupon mb-3">
                                    <h3> Coupon </h3>
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
                            <div class="order-wrapper">
                                <h3> {{ __('cart.your_order') }} </h3>
                                <div class="order-table table-responsive">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="product-name">{{ __('cart.product_name') }}</th>
                                                <th class="product-total">{{ __('cart.total') }} (Đ)</th>
                                            </tr>                           
                                        </thead>
                                        <tbody>
                                            @forelse($cart as $key => $val)
                                                <tr class="pro-key-{{ $key }}">
                                                    <td class="product-name">
                                                        {{ $val['title'] }} <strong class="product-qty"> × {{ $val['qty'] }} </strong>
                                                    </td>
                                                    <td class="product-total">
                                                        <span class="amount">{!! get_currency_vn($val['price']*$val['qty'],'') !!}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                            <tr> <td colspan="10"> {{ __('cart.no_item') }} </td> </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>{{ __('cart.cart_total') }}</th>
                                                <td><span class="sumCartPrice"></span></td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('cart.order_total') }}</th>
                                                <td><strong class="sumOrderPrice"></strong>
                                                </td>
                                            </tr>                               
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="payment-method">
                                    <div class="panel-group" id="accordion">
                                        @forelse($payments as $key => $payment)
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <label data-toggle="collapse" data-parent="#accordion" data-target="#collapse-{{ $key }}"><input type="radio" name="payment" value="{{ $payment->title }}" {{ $key==0 ? 'checked' : '' }} > {{ $payment->title }}</label>
                                                </h4>
                                            </div>
                                            <div id="collapse-{{ $key }}" class="panel-collapse collapse {{ $key==0 ? 'in' : '' }}">
                                                <div class="panel-body">
                                                    <p> {{ $payment->description }} </p>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="order-button text-center">
                                <button type="submit" class="btn btn-xl btn-danger text-uppercase">{{ __('cart.place_order') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<!-- PAGE SECTION END --> 
@endsection