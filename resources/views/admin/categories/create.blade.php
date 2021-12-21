@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.category.index', ['type'=>$type]) }}"> {{$pageTitle}} </a>
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
    <form role="form" method="POST" action="{{ route('admin.category.store',['type'=>$type]) }}" enctype="multipart/form-data" >
        {{ csrf_field() }}
        <div class="col-lg-9 col-xs-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Nội dung </div>
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

                            @if($siteconfig[$type]['contents'])
                            <div class="form-group">
                                <label class="control-label">Nội dung</label>
                                <div class="ck-editor">
                                    <textarea name="dataL[{{ $key }}][contents]" id="contents_{{ $key }}" class="form-control content" rows="6">{{ old('dataL.'.$key.'.contents') }}</textarea>
                                </div>
                            </div>
                            @endif

                            @if($siteconfig[$type]['seo'])
                            <div class="form-group">
                                <label class="control-label">Meta Title</label>
                                <div>
                                    <input type="text" name="dataL[{{ $key }}][meta_seo][title]" class="form-control meta-title" value="{{ old('dataL.'.$key.'.meta_seo.title') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Meta Keywords</label>
                                <div>
                                    <textarea name="dataL[{{ $key }}][meta_seo][keywords]" class="form-control meta-keywords" rows="6">{{ old('dataL.'.$key.'.meta_seo.keywords') }}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Meta Description</label>
                                <div>
                                    <textarea name="dataL[{{ $key }}][meta_seo][description]" class="form-control meta-description" rows="6">{{ old('dataL.'.$key.'.meta_seo.description') }}</textarea>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @if($siteconfig[$type]['seo'])
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> SEO </div>
                    <div class="actions">
                        <a href="javascript:;" id="refresh-analysis" class="btn btn-sm btn-default"> Refresh! </a>
                    </div>
                </div>
                <div class="portlet-body" id="yoast-seo">
                    <div class="row">
                        <div class="col-xs-12 hidden">
                            <label for="locale">Locale</label>
                            <input type="text" id="locale" name="locale" placeholder="en_US" />
                            <label for="content">Text</label>
                            <textarea id="content" name="content" placeholder="Start writing your text!"></textarea>
                            <label for="focusKeyword">Focus keyword</label>
                            <input type="text" id="focusKeyword" name="focusKeyword" placeholder="Choose a focus keyword" />
                        </div>
                        <div class="col-xs-12">
                            <div id="snippet" class="output"> </div>
                        </div>
                        <div class="col-xs-12">
                            <div id="output-container" class="output-container">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h4>Đánh giá SEO</h4>
                                        <div id="output" class="output"></div>
                                    </div>
                                    <div class="col-xs-12">
                                        <h4>Đánh giá nội dung</h4>
                                        <div id="contentOutput" class="output"> </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="col-lg-3 col-xs-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">Thông tin chung </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label class="control-label">Danh mục cha</label>
                        <div>
                            <select name="data[parent]" class="selectpicker show-tick show-menu-arrow form-control">
                                <option value="0">Chọn danh mục</option>
                                @php
                                    Menu::resetMenu();
                                    Menu::setMenu($categories);
                                    echo Menu::getMenuSelectLimit(0,0,'',old('data.parent'));
                                @endphp
                            </select>
                        </div>
                    </div>
                    @if( @$siteconfig[$type]['suppliers'])
                    <div class="form-group">
                        <label class="control-label">Nhà cung cấp</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="suppliers[]" multiple>
                                @foreach($suppliers as $key => $val)
                                <option value="{{ $val->id }}" {{ old('suppliers') ? (in_array($val->id,old('suppliers'))) ? 'selected' : '' : '' }} > {{ $val->name }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    @if($siteconfig[$type]['filter'])
                    <div class="form-group">
                        <label class="control-label">Bộ lọc</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="filters[]" multiple>
                                @foreach($filters as $key => $val)
                                <option value="{{ $val->id }}" {{ old('filters') ? (in_array($val->id,old('filters'))) ? 'selected' : '' : '' }} > {{ $val->title }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <label class="control-label">Slug</label>
                        <div>
                            <input type="text" name="dataL[vi][slug]" class="form-control slug" value="{{ old('dataL.'.$key.'.slug') }}">
                        </div>
                    </div>

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
                    <div class="form-group">
                        <label class="control-label">Link</label>
                        <div>
                            <input type="text" name="data[link]" class="form-control" value="{{ old('data.link') }}">
                        </div>
                    </div>
                    @endif

                    @if($siteconfig[$type]['icon'])
                    <div class="form-group">
                        <label class="control-label"> <a href="https://fontawesome.com/v4.7.0/icons/" rel="nofollow" target="_blank"> Font Icon </a> </label>
                        <div>
                            <input type="text" name="data[icon]" class="form-control" value="{{ old('data.icon') }}">
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <label class="control-label">Tình trạng</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="status[]" multiple>
                                @foreach($siteconfig[$type]['status'] as $key => $val)
{{--                                <option value="{{ $key }}" {{ is_array(old('status')) ? ((in_array($key,old('status'))) ? 'selected' : '') : ($key=='publish')?'selected':'' }} > {{ $val }} </option>--}}
                                <option value="{{ $key }}" @if(is_array(old('status'))) {{ in_array($key,old('status')) ? "selected" : "" }} @else {{ ($key=='publish')?'selected':'' }} @endif > {{ $val }} </option>
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