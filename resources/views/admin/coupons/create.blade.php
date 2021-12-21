@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.coupon.index') }}"> Coupon </a>
    <i class="fa fa-circle"></i>
</li>
<li>
    <span> Thêm mới</span>
</li>
@endsection
@section('content')
<div class="row">
    @include('admin.blocks.messages')
    <!-- BEGIN FORM-->
    <form role="form" method="POST" action="{{ route('admin.coupon.store') }}" enctype="multipart/form-data" >
        {{ csrf_field() }}
        <input type="hidden" name="data[change_conditions_type]" value="discount_from_total_cart">
        <div class="col-lg-9 col-xs-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Thêm mới </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="name" class="control-label">Tên</label>
                        <div>
                            <input type="text" class="form-control" name="data[title]" value="{{ old('data.title') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="control-label">Mô tả</label>
                        <div>
                            <textarea name="data[description]" class="form-control" rows="6">{{ old('data.description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">Thông tin chung </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label class="control-label">Mã coupon</label>
                        <div class="input-group">
                            <input type="text" name="code" class="form-control generate-result" value="{{ old('code') }}">
                            <span class="input-group-btn">
                                <button class="btn" id="generate-code"> <i class="icon-refresh"></i> </button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Số tiền coupon</label>
                        <div class="input-group">
                            <input type="text" name="coupon_amount" class="form-control priceFormat" value="{{ old('coupon_amount') }}">
                            <span class="input-group-btn">
                                <button class="btn btn-info" id="change-conditions-type" value="discount_from_total_cart"> VNĐ </button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Số lần sử dụng</label>
                        <div>
                            <input type="text" name="number_of_uses" class="form-control priceFormat" value="{{ old('number_of_uses') ? old('number_of_uses') : 1 }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Điều kiện</label>
                        <i class="help-block"> Áp dụng cho đơn hàng có tổng tiền từ </i>
                        <div class="input-group input-daterange">
                            <input type="text" name="min_restriction_amount" class="form-control priceFormat" value="{{ old('min_restriction_amount') }}">
                            <span class="input-group-addon">tới</span>
                            <input type="text" name="max_restriction_amount" class="form-control priceFormat" value="{{ old('max_restriction_amount') }}">
                            
                        </div>
                        <i class="help-block"> Áp dụng từ ngày </i>
                        <div class="input-group input-daterange startDate" data-provide="datepicker" data-date-format="yyyy/mm/dd">
                            <input type="text" class="form-control" readonly="" name="data[begin_at]" value="{{ old('data.begin_at') }}">
                            <span class="input-group-addon">tới</span>
                            <input type="text" class="form-control" readonly="" name="data[end_at]" value="{{ old('data.end_at') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Tình trạng</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="status[]" multiple>
                                <option value="publish" selected> Hiển thị </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Thứ tự</label>
                        <div>
                            <input type="text" name="priority" class="form-control priceFormat" value="{{ (old('priority')) ? old('priority') : 1 }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn green"> <i class="fa fa-check"></i> Lưu</button>
                        <a href="{{ url()->previous() }}" class="btn default" > Thoát </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection