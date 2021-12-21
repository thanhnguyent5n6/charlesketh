@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.photo.index', ['type'=>$type]) }}"> {{ $pageTitle }} </a>
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
    <form role="form" method="POST" action="{{ route('admin.photo.store',['type'=>$type]) }}" enctype="multipart/form-data" >
        {{ csrf_field() }}
        <div class="col-lg-9 col-xs-12"> 
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Thêm mới </div>
                    <ul class="nav nav-tabs">
                        @foreach($languages as $key => $lang)
                        <li>
                            <a href="#tab_{{ $key }}" data-toggle="tab" aria-expanded="false"> {{ $lang }} </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="portlet-body">
                    <div class="tab-content">
                        @foreach($languages as $key => $lang)
                        <div class="tab-pane" id="tab_{{ $key }}">
                            <div class="form-group">
                                <label for="name" class="control-label">Tên</label>
                                <div>
                                    <input type="text" class="form-control {!! (( $key==$default_language )?'title':'') !!}" name="dataL[{{ $key }}][title]" value="{{ old('dataL.'.$key.'.title') }}">
                                </div>
                            </div>

                            @if($siteconfig[$type]['description'])
                            <div class="form-group">
                                <label for="description" class="control-label">Mô tả</label>
                                <div>
                                    <textarea name="dataL[{{ $key }}][description]" class="form-control" rows="6">{{ old('dataL.'.$key.'.description') }}</textarea>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
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
                    @if($siteconfig[$type]['image'])
                    <div class="form-group">
                        <label class="control-label">Hình ảnh</label>
                        <div>
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail">
                                    <img src="{{ asset('noimage/'.$thumbs['_small']['width'].'x'.$thumbs['_small']['height']) }}" alt="">
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail"> </div>
                                <div>
                                    <span class="btn default btn-file">
                                    <span class="fileinput-new"> Chọn hình ảnh </span>
                                    <span class="fileinput-exists"> Thay đổi </span>
                                    <input type="file" name="image"> </span>
                                    <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Xóa </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Alt</label>
                        <div>
                            <input type="text" name="data[alt]" class="form-control" value="{{ old('data.alt') }}">
                        </div>
                    </div>
                    @endif

                    @if($siteconfig[$type]['link'])
                    <div class="form-group">
                        <label class="control-label">Link</label>
                        <div>
                            <input type="text" name="data[link]" class="form-control" value="{{ old('data.link') }}">
                        </div>
                    </div>
                    @endif

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