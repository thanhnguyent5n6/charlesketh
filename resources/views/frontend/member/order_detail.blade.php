@extends('frontend.member.app')
@section('content')
<section class="page-section pb-4" data-wow-duration="2s" data-wow-delay="0.2s">
    <div class="container">
        <div class="row"> @include('frontend.default.blocks.messages') </div>
        <div class="row">
            @if ( $item !== null )
            <div class="col-12">
                <div class="pb-20">
                    <p class="text-right font-sm">Đơn hàng #{{ $item->code }} được tạo ngày {{ $item->created_at }} </p>
                    <p class="text-right"><button class="btn btn-lg btn-{{config('siteconfig.order_site_labels.'.$item->status_id)}}"> {{ config('siteconfig.order_site_status.'.$item->status_id) }} </button></p>
                </div>
                <div class="alert alert-info mb-40">
                    <h4 class="text-center uppercase bold pt-10">Thông tin hợp đồng</h4>
                </div>
                <div>
                    <p> <b> Kính gửi (Ông/Bà):</b> {{ $item->name }}</p>
                    <p> Chúng tôi xin chân thành cảm ơn Quý khách đã tín nhiệm sử dụng dịch vụ của {{ config('app.name') }}. Dưới đây là thông tin các dịch vụ Quý khách đã đăng ký.</p>
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
                                <td align="center"> {{ $item->created_at }} </td>
                                <td align="center"> </td>
                                <td align="center"> {{ config('siteconfig.order.online.payment.'.$item->payment_id) }} </td>
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
                                <th width="5%"> Stt </th>
                                <th> {{ __('cart.product_name') }} </th>
                                <th width="20%"> {{ __('cart.quantity') }} </th>
                                <th width="20%"> {{ __('cart.total') }} </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $key => $product)
                            <tr>
                                <td align="center"> {{ $key+1 }} </td>
                                <td> {{ $product->product_title }} </td>
                                <td align="center"> {{ $product->product_qty }} </td>
                                <td align="center"> {!! get_currency_vn($product->product_price*$product->product_qty,'') !!} </td>
                            </tr>
                            @empty
                            @endforelse
                            <tr class="active">
                                <td align="right" colspan="3"> Tổng tiền </td>
                                <td align="center"> <b class="bold font-red font-lg">{!! get_currency_vn($item->order_price,'') !!}</b> </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                    <p> {{ __('site.no_data') }} </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection