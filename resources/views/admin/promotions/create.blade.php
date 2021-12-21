@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.promotion.index') }}"> Khuyến mãi </a>
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
    <form role="form" method="POST" action="{{ route('admin.promotion.store') }}" enctype="multipart/form-data" >
        {{ csrf_field() }}
        <input type="hidden" name="data[change_conditions_type]" value="discount_from_total_cart">
        <div class="col-lg-9 col-xs-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Thêm mới </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label class="control-label">Danh mục</label>
                        <div>
                            <select name="category_id[]" class="selectpicker show-tick show-menu-arrow form-control" data-live-search="true" multiple="" title="-- Chọn danh mục --">
                                @php
                                    Menu::resetMenu();
                                    Menu::setMenu($categories);
                                    echo Menu::getMenuSelect(0,0,'',0);
                                @endphp
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Sản phẩm</label>
                        <div>
                            <select name="product_id[]" class="form-control select2-data-ajax"  multiple="" data-label="Tên hoặc mã sản phẩm" data-url="{{ route('admin.product.ajax',['t'=>'san-pham']) }}">
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Sản phẩm loại trừ</label>
                        <div>
                            <select name="product_limit[]" class="form-control select2-data-ajax"  multiple="" data-label="Tên hoặc mã sản phẩm" data-url="{{ route('admin.product.ajax',['t'=>'san-pham']) }}">
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label">Tên</label>
                        <div>
                            <input type="text" class="form-control title" name="data[title]" value="{{ old('data.title') }}">
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
                        <label class="control-label">Số tiền khuyến mãi</label>
                        <div class="input-group">
                            <input type="text" name="coupon_amount" class="form-control priceFormat" value="{{ old('coupon_amount') }}">
                            <span class="input-group-btn">
                                <button class="btn btn-info" id="change-conditions-type" value="discount_from_total_cart"> VNĐ </button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Áp dụng từ ngày</label>
                        <div class="input-group input-daterange startDate" data-provide="datepicker" data-date-format="yyyy/mm/dd">
                            <input type="text" class="form-control" readonly="" name="data[begin_at]" value="{{ old('data.begin_at') }}">
                            <span class="input-group-addon">tới</span>
                            <input type="text" class="form-control" readonly="" name="data[end_at]" value="{{ old('data.end_at') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Slug</label>
                        <div>
                            <input type="text" name="data[slug]" class="form-control slug" value="{{ old('data.slug') }}">
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