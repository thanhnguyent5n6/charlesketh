@extends('frontend.member.app')
@section('content')
<section class="page-section pb-4" data-wow-duration="2s" data-wow-delay="0.2s">
    <div class="container">
        <div class="row"> @include('frontend.default.blocks.messages') </div>
        <div class="row">
            <div class="col-lg-3 col-12">@include('frontend.member.sidebar')</div>
            <div class="col-lg-9 col-12">
                <div class="card border-0">
                    <div class="card-body">
                        <div class="cart-table table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th width="7%"> Mã đơn hàng </th>
                                        <th width="10%"> Ngày đặt </th>
                                        <th width="7%"> Số lượng </th>
                                        <th width="7%"> Tổng giá </th>
                                        <th width="10%"> Tình trạng </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($items as $item)
                                    <tr id="record-{{ $item->id }}">
                                        <td align="center"><a href="{{ route('frontend.member.order_detail',['id'=>$item->id]) }}"><b>{{ $item->code }}</b></a></td>
                                        <td align="center"> {{ $item->created_at }} </td>
                                        <td align="center">{{ $item->order_qty }}</td>
                                        <td align="center">{!! get_currency_vn($item->order_price,'') !!}</td>
                                        <td align="center">{{ config('siteconfig.order.online.site.status.'.$item->status_id) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="30" align="center"> Không có bản dữ liệu trong bảng </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center"> {{ $items->links('frontend.default.blocks.paginate') }} </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection