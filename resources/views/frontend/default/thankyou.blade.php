@extends('frontend.default.app')
@section('content')
<!-- PAGE SECTION START -->
<section class="page-section py-3">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="sub-title">Cảm ơn bạn đã đặt hàng tại Website</h3>
                <div class="alert alert-success">
                    Mã đơn hàng #{{ session('orderCode') }} đã được đặt hàng thành công.
                </div>

                <div class="pt-60 pb-20">
                    <h4 class="uppercase font-red bold">Thông tin khách hàng</h4>
                </div>
                <div>
                    <p> <span>{{ __('cart.name') }}: </span> <b> {{ $item->name }} </b> </p>
                    <p> <span>Email: </span> <b> {{ $item->email }} </b> </p>
                    <p> <span>{{ __('cart.phone') }}: </span> <b> {{ $item->phone }} </b> </p>
                    <p> <span>Hình thức thanh toán:</span> <b>Tiền Mặt</b></p>
                    <p> <span>Ngày nhận hàng dự kiến: <b>Giao hàng không quá 5h từ lúc đặt hàng</b></span></p>
                    <p> <span>Phí giao hàng:</span> <b>Miễn Phí</b></p>
                    <p> <span>Lưu ý:</span> <b>Bạn vui lòng kiểm tra thông tin đơn hàng và tình trạng kiện hàng trước khi nhận. Quý khách chỉ có thể mở hộp và kiểm tra sản phẩm sau khi đã thanh toán và xác nhận nhận hàng với nhân viên vận chuyển.</b></p>
                </div>

                <div class="pt-60 pb-20">
                    <h4 class="uppercase font-red bold">Chi tiết đơn hàng</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="active">
                                <th> {{ __('cart.photo') }} </th>
                                <th> {{ __('cart.product_name') }} </th>
                                <th> {{ __('cart.price') }} (Đ)</th>
                                <th> {{ __('cart.quantity') }} </th>
                                <th> {{ __('cart.total') }} (Đ)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $val)
                            <tr>
                                <td align="center"><img src="" alt="" /></td>
                                <td align="center">{{ $val['product_title'] }}
                                    {{ ($val['color_title'] ? $val['color_title'].' - ' : '').($val['size_title'] ? $val['size_title'] : '') }}
                                </td>
                                <td align="center">{!! get_currency_vn($val['product_price'],'') !!}</td>
                                <td align="center">{{ $val['product_qty'] }}</td>
                                <td align="center">{!! get_currency_vn($val['product_price']*$val['product_qty'],'') !!}</td>
                            </tr>
                            @empty
                            <tr> <td colspan="30"> {{ __('cart.no_item') }} </td> </tr>
                            @endforelse
                            <tr class="active">
                                <td align="right" colspan="4"> Tổng tiền </td>
                                <td align="center"> <b class="bold font-red font-lg">{!! get_currency_vn($item->order_price) !!}</b> </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- PAGE SECTION END -->
@endsection