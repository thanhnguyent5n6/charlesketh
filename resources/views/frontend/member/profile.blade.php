@extends('frontend.member.app')
@section('content')
<section class="page-section pb-4" data-wow-duration="2s" data-wow-delay="0.2s">
    <div class="container">
        <div class="row"> @include('frontend.default.blocks.messages') </div>
        <div class="row">
            <div class="col-lg-3 col-12">@include('frontend.member.sidebar')</div>
            <div class="col-lg-9 col-12">
                <div class="card border-0">
                    <h6 class="card-header font-weight-bold text-uppercase">{{ __('account.profile') }}</h6>
                    <div class="card-body">
                        <form role="form" method="POST" action="{{ route('frontend.member.profile') }}" >
                            {{ csrf_field() }}
                            {{ method_field('put') }}

                            <div class="form-group row">
                                <label class="col-form-label text-md-right col-md-3">Email</label>
                                <div class="col-lg-8 col-md-9 col-sm-12 col-12">
                                    <input type="text" class="form-control" value="{{ $member->email }}" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label text-md-right col-md-3">Họ và tên</label>
                                <div class="col-lg-8 col-md-9 col-sm-12 col-12">
                                    <input type="text" name="data[name]" class="form-control" value="{{ $member->name }}">
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-form-label text-md-right col-md-3">Điện thoại</label>
                                <div class="col-lg-8 col-md-9 col-sm-12 col-12">
                                    <input type="text" name="data[phone]" class="form-control" value="{{ $member->phone }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label text-md-right col-md-3">Địa chỉ</label>
                                <div class="col-lg-8 col-md-9 col-sm-12 col-12">
                                    <input type="text" name="data[address]" class="form-control" value="{{ $member->address }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label text-md-right col-md-3">Tỉnh / Thành phố</label>
                                <div class="col-lg-8 col-md-9 col-sm-12 col-12">
                                    <select class="selectpicker show-tick show-menu-arrow form-control province" name="data[province_id]" data-live-search="true" title="-- Chọn Tỉnh / Thành phố" data-group="profile">
                                        <option value="{{ $member->province_id }}" selected ></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label text-md-right col-md-3">Quận / Huyện</label>
                                <div class="col-lg-8 col-md-9 col-sm-12 col-12">
                                    <select class="selectpicker show-tick show-menu-arrow form-control district" name="data[district_id]" data-live-search="true" title="-- Chọn Quận / Huyện" data-group="profile">
                                        <option value="{{ $member->district_id }}" selected ></option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label text-md-right col-md-3">Mật khẩu cũ</label>
                                <div class="col-lg-8 col-md-9 col-sm-12 col-12">
                                    <input type="password" name="oldpassword" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label text-md-right col-md-3">Mật khẩu mới</label>
                                <div class="col-lg-8 col-md-9 col-sm-12 col-12">
                                    <input type="password" name="password" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label text-md-right col-md-3">Xác nhận mật khẩu mới</label>
                                <div class="col-lg-8 col-md-9 col-sm-12 col-12">
                                    <input type="password" name="password_confirmation" class="form-control" value="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-9 col-sm-12 col-12 ml-auto">
                                    <button type="submit" class="btn btn-success"> Cập nhật </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection