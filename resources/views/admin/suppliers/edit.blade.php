@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.supplier.index', ['type'=>$type]) }}"> {{ $pageTitle }} </a>
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
    <form role="form" method="POST" action="{{ route('admin.supplier.update',['id'=>$item->id,'type'=>$type]) }}"  enctype="multipart/form-data" >
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
                        <label class="control-label">Nhà cung cấp</label>
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
                        <label for="description" class="control-label">Mô tả</label>
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
                        <label class="control-label">Slug</label>
                        <div>
                            <input type="text" name="slug" class="form-control slug" value="{{ $item->slug }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Mã NCC</label>
                        <div>
                            <input type="text" name="code" class="form-control uppercase" value="{{ $item->code }}">
                        </div>
                    </div>

                    @if($siteconfig[$type]['image'])
                    <div class="form-group">
                        <label class="control-label">Hình ảnh</label>
                        <div>
                            <div class="fileinput {{ ( $item->image && file_exists(public_path( get_thumbnail($path.'/'.$item->image))) ) ? 'fileinput-exists' : 'fileinput-new' }}" data-provides="fileinput">
                                <div class="fileinput-new thumbnail">
                                    <img src="{{ asset('noimage/'.$thumbs['_small']['width'].'x'.$thumbs['_small']['height']) }}" alt="">
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail">
                                    @if( $item->image && file_exists(public_path(get_thumbnail($path.'/'.$item->image))) )
                                    <img src="{{ asset( get_thumbnail('public/'.$path.'/'.$item->image) ) }}" alt="">
                                    @endif
                                </div>
                                <div>
                                    <span class="btn default btn-file">
                                        <span class="fileinput-new"> Chọn hình ảnh </span>
                                        <span class="fileinput-exists"> Thay đổi </span>
                                        <input type="file" name="image">
                                    </span>
                                    <a href="javascript:;" class="btn btn-delete-file default fileinput-exists" data-dismiss="fileinput" data-ajax="act=delete_file|path={{ $path.'/'.$item->image }}|thumbs={{ json_encode($thumbs) }}"> Xóa </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Alt</label>
                        <div>
                            <input type="text" name="data[alt]" value="{{ $item->alt }}" class="form-control">
                        </div>
                    </div>
                    @endif

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