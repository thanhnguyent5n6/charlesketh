@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.coupon.index') }}"> Coupon </a>
    <i class="fa fa-circle"></i>
</li>
<li>
    <span>Chỉnh sửa</span>
</li>
@endsection
@section('content')
<div class="row">
    @include('admin.blocks.messages')
    <!-- BEGIN FORM-->
    <form role="form" method="POST" action="{{ route('admin.coupon.update',['id'=>$item->id]) }}" enctype="multipart/form-data" >
        {{ csrf_field() }}
        {{ method_field('put') }}
        <input type="hidden" name="data[change_conditions_type]" value="{{ $item->change_conditions_type }}">
        <input type="hidden" name="redirects_to" value="{{ (old('redirects_to')) ? old('redirects_to') : url()->previous() }}" />
        <div class="col-lg-9 col-xs-12"> 
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Chỉnh sửa </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="name" class="control-label">Tên</label>
                        <div>
                            <input type="text" class="form-control" name="data[title]" value="{{ $item->title }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label">Mô tả</label>
                        <div>
                            <textarea name="data[description]" class="form-control" rows="6">{{ $item->description }}</textarea>
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
                        <div>
                            <input type="text" name="code" class="form-control" readonly="" value="{{ $item->code }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Số tiền coupon</label>
                        <div class="input-group">
                            <input type="text" name="coupon_amount" class="form-control priceFormat" value="{{ $item->coupon_amount }}">
                            <span class="input-group-btn">
                                <button class="btn btn-info" id="change-conditions-type" value="{{ $item->change_conditions_type }}"> {{ ($item->change_conditions_type == 'percentage_discount_from_total_cart') ? '%': 'VNĐ' }}  </button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Số lần sử dụng</label>
                        <div>
                            <input type="text" name="number_of_uses" class="form-control priceFormat" value="{{ $item->number_of_uses }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Điều kiện</label>
                        <i class="help-block"> Áp dụng cho đơn hàng có tổng tiền từ </i>
                        <div class="input-group input-daterange">
                            <input type="text" name="min_restriction_amount" class="form-control priceFormat" value="{{ $item->min_restriction_amount }}">
                            <span class="input-group-addon">tới</span>
                            <input type="text" name="max_restriction_amount" class="form-control priceFormat" value="{{ $item->max_restriction_amount }}">
                            
                        </div>
                        <i class="help-block"> Áp dụng từ ngày </i>
                        <div class="input-group input-daterange startDate" data-provide="datepicker" data-date-format="yyyy/mm/dd">
                            <input type="text" class="form-control" readonly="" name="data[begin_at]" value="{{ $item->begin_at }}">
                            <span class="input-group-addon">tới</span>
                            <input type="text" class="form-control" readonly="" name="data[end_at]" value="{{ $item->end_at }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Tình trạng</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="status[]" multiple>
                                <option value="publish" {{ $item->status == 'publish' ? 'selected' : '' }} > Hiển thị </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Thứ tự</label>
                        <div>
                            <input type="text" name="priority" value="{{ $item->priority }}" class="form-control priceFormat">
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