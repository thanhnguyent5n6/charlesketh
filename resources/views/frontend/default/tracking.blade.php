@extends('frontend.default.app')
@section('content')
<!-- PAGE SECTION START -->
<section class="page-section pb-4">
    <div class="container">
        <div class="row">
            @include('frontend.default.blocks.messages')
            @if ( $item !== null )
            <div class="col-lg-9 col-md-8 col-12 pull-right">
                <div class="pb-20">
                    <p class="text-right font-sm">Đơn hàng #{{ $item->code }} được tạo ngày {{ date('d/m/Y', strtotime($item->created_at) ) }} </p>
                    <p class="text-right"><button class="btn btn-lg btn-{{config('siteconfig.order_site_labels.'.$item->status_id)}}"> {{ config('siteconfig.order.online.site.status.'.$item->status_id) }} </button></p>
                </div>
                <div class="alert alert-info mb-40">
                    <h4 class="text-center uppercase bold pt-10">Thông tin đơn hàng</h4>
                </div>
                <div>
                    <p> <b> Kính gửi (Ông/Bà):</b> {{ $item->name }}</p>
                    <p> Chúng tôi xin chân thành cảm ơn Quý khách đã tín nhiệm sử dụng sản phẩm của {{ config('app.name') }}. Dưới đây là thông tin các sản phẩm Quý khách đã mua.</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="active">
                                <th width="15%"> Mã đơn hàng </th>
                                <th width="20%"> Ngày tạo </th>
                                <th width="20%"> Ngày thanh toán </th>
                                <th width="25%"> Hình thức thanh toán </th>
                                <th width="20%"> Tổng tiền </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center"> {{ $item->code }} </td>
                                <td align="center"> {{ date('d/m/Y', strtotime($item->created_at) ) }} </td>
                                <td align="center"> {{ date('d/m/Y', strtotime($item->created_at) ) }} </td>
                                <td align="center"> {{ config('siteconfig.order.online.site.payment.'.$item->payment_id) }} </td>
                                <td align="center"> {!! get_currency_vn($item->order_price) !!} </td>
                            </tr>
                        </tbody>
                    </table>
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
            <div class="col-lg-3 col-md-4 col-12">
                <div class="sidebar">
                    <div class="sidebar-widget mb-20">
                        <h4 class="title">Đơn hàng của bạn</h4>
                        <ul class="category">
                            <li> <a href="#" class="active"> {{ $item->code }} </a> </li>
                        </ul>
                    </div>
                </div>
            </div>
            @else
            <div class="col-12">
                <h2 class="text-center"> Theo dõi đơn hàng </h2>
                <form method="GET" action="{{ route('frontend.cart.tracking') }}">
                    <div class="form-group">
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="Mã đơn hàng">
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Email">
                    </div>
                    <button type="submit" class="btn btn-primary"> Kiểm tra </button>
                </form>
            </div>

            @endif
        </div>
    </div>
</section>
<!-- PAGE SECTION END --> 
@endsection