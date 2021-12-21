@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.register.index', ['type'=>$type]) }}"> {{ $pageTitle }} </a>
    <i class="fa fa-circle"></i>
</li>
<li>
    <span> Thêm mới </span>
</li>
@endsection
@section('content')
<div class="row">
    @include('admin.blocks.messages')
    <!-- BEGIN FORM-->
    <form role="form" method="POST" action="{{ route('admin.register.store',['type'=>$type]) }}">
        {{ csrf_field() }}

        <div class="col-lg-9 col-xs-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Thêm mới </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="name" class="control-label">Tiêu đề</label>
                        <div>
                            <input type="text" class="form-control" name="data[title]" value="{{ old('data.title') }}">
                        </div>
                    </div>
                    @if($siteconfig[$type]['description'])
                    <div class="form-group">
                        <label for="description" class="control-label">Mô tả</label>
                        <div>
                            <textarea name="data[description]" class="form-control" rows="6">{{ old('data.description') }}</textarea>
                        </div>
                    </div>
                    @endif

                    @if($siteconfig[$type]['contents'])
                    <div class="form-group">
                        <label class="control-label">Nội dung</label>
                        <div class="ck-editor">
                            <textarea name="data[contents]" id="contents" class="form-control" rows="6">{{ old('data.contents') }}</textarea>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        <div class="col-lg-3 col-xs-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Thông tin chung </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label class="control-label">Họ và tên</label>
                        <div>
                            <input type="text" name="data[name]" class="form-control" value="{{ old('data.name') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Email</label>
                        <div>
                            <input type="text" name="data[email]" class="form-control" value="{{ old('data.email') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Điện thoại</label>
                        <div>
                            <input type="text" name="data[phone]" class="form-control" value="{{ old('data.phone') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Địa chỉ</label>
                        <div>
                            <input type="text" name="data[address]" class="form-control" value="{{ old('data.address') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="mt-radio-inline">
                            <label class="mt-radio">
                                <input type="radio" name="data[gender]" value="1" checked> Nam<span></span> </label>
                            <label class="mt-radio">
                                <input type="radio" name="data[gender]" value="2"> Nữ<span></span> </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Tình trạng</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="status[]" multiple>
                                @foreach($siteconfig[$type]['status'] as $key => $val)
                                <option value="{{ $key }}" {{ (old('status')) ? ( (in_array($key,old('status'))) ? 'selected' : '') : ($key=='publish')?'selected':'' }} > {{ $val }} </option>
                                @endforeach
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