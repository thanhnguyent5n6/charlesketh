@extends('admin.app')
@section('breadcrumb')
<li>
    <span> Bảng điều khiển </span>
</li>
@endsection
@section('content')
@if ( Auth::user()->hasRole('admin') )
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 ">
            <div class="display">
                <div class="number">
                    <h3 class="font-green-sharp">
                        <span data-counter="counterup" data-value="{{ $order_count }}">0</span>
                    </h3>
                    <small>Tổng Đơn Hàng</small>
                </div>
                <div class="icon">
                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                    <span style="width: 76%;" class="progress-bar progress-bar-success green-sharp">
                        <span class="sr-only">76% progress</span>
                    </span>
                </div>
                <div class="status">
                    <div class="status-title"> Hoàn thành </div>
                    <div class="status-number"> 76% </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 ">
            <div class="display">
                <div class="number">
                    <h3 class="font-red-haze">
                        <span data-counter="counterup" data-value="{{ $product_count }}">0</span>
                    </h3>
                    <small>Tổng Sản Phẩm</small>
                </div>
                <div class="icon">
                    <i class="fa fa-cubes" aria-hidden="true"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                    <span style="width: 85%;" class="progress-bar progress-bar-success red-haze">
                        <span class="sr-only">85% change</span>
                    </span>
                </div>
                <div class="status">
                    <div class="status-title"> Hoàn thành </div>
                    <div class="status-number"> 85% </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 ">
            <div class="display">
                <div class="number">
                    <h3 class="font-blue-sharp">
                        <span>{!! get_currency_vn($order_sum,'') !!}</span>
                    </h3>
                    <small>Tổng Doanh Thu</small>
                </div>
                <div class="icon">
                    <i class="fa fa-usd" aria-hidden="true"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                    <span style="width: 45%;" class="progress-bar progress-bar-success blue-sharp">
                        <span class="sr-only">45% grow</span>
                    </span>
                </div>
                <div class="status">
                    <div class="status-title"> Hoàn thành </div>
                    <div class="status-number"> 45% </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 ">
            <div class="display">
                <div class="number">
                    <h3 class="font-purple-soft">
                        <span data-counter="counterup" data-value="{{ $order_count }}"></span>
                    </h3>
                    <small>Khách hàng</small>
                </div>
                <div class="icon">
                    <i class="icon-user"></i>
                </div>
            </div>
            <div class="progress-info">
                <div class="progress">
                    <span style="width: 7%;" class="progress-bar progress-bar-success purple-soft">
                        <span class="sr-only">56% change</span>
                    </span>
                </div>
                <div class="status">
                    <div class="status-title"> KH mới </div>
                    <div class="status-number"> 7% </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('custom_script')
<script src="{{ asset('admin/js/dashboard.js') }}" type="text/javascript"></script>
@endsection