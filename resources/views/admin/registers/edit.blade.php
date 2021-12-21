@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.register.index', ['type'=>$type]) }}"> {{ $pageTitle }} </a>
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
    <form role="form" method="POST" action="{{ route('admin.register.update',['id'=>$item->id,'type'=>$type]) }}" >
        {{ csrf_field() }}
        {{ method_field('put') }}
        <input type="hidden" name="redirects_to" value="{{ (old('redirects_to')) ? old('redirects_to') : url()->previous() }}" />
        <div class="col-lg-9 col-xs-12"> 
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Chỉnh sửa </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="name" class="control-label">Tiêu đề</label>
                        <div>
                            <input type="text" class="form-control" name="data[title]" value="{{ $item->title }}">
                        </div>
                    </div>
                    @if($siteconfig[$type]['description'])
                    <div class="form-group">
                        <label for="description" class="control-label">Mô tả</label>
                        <div>
                            <textarea name="data[description]" class="form-control" rows="6">{{ $item->description }}</textarea>
                        </div>
                    </div>
                    @endif

                    @if($siteconfig[$type]['contents'])
                    <div class="form-group">
                        <label class="control-label">Nội dung</label>
                        <div class="ck-editor">
                            <textarea name="data[contents]" id="contents" class="form-control" rows="6">{{ $item->contents }}</textarea>
                        </div>
                    </div>
                    @endif
                    
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
                        <label class="control-label">Họ và tên</label>
                        <div>
                            <input type="text" name="data[name]" class="form-control" value="{{ $item->name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Email</label>
                        <div>
                            <input type="text" name="data[email]" class="form-control" value="{{ $item->email }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Điện thoại</label>
                        <div>
                            <input type="text" name="data[phone]" class="form-control" value="{{ $item->phone }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Địa chỉ</label>
                        <div>
                            <input type="text" name="data[address]" class="form-control" value="{{ $item->address }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="mt-radio-inline">
                            <label class="mt-radio">
                                <input type="radio" name="data[gender]" value="1" {{ ( ($item->gender == 1) ? 'checked' : '' ) }} > Nam<span></span> </label>
                            <label class="mt-radio">
                                <input type="radio" name="data[gender]" value="2" {{ ( ($item->gender == 2) ? 'checked' : '' ) }} > Nữ<span></span> </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Tình trạng</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="status[]" multiple>
                                <optgroup >
                                    @foreach($siteconfig[$type]['status'] as $key => $val)
                                    <option value="{{ $key }}" {{ (strpos($item->status,$key) !== false)?'selected':'' }} > {{ $val }} </option>
                                    @endforeach
                                </optgroup>
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