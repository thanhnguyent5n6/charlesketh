@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.seo.index') }}"> {{ $pageTitle }} </a>
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
    <form role="form" method="POST" action="{{ route('admin.seo.update',['id'=>$item->id]) }}" enctype="multipart/form-data" >
        {{ csrf_field() }}
        {{ method_field('put') }}
        <input type="hidden" name="redirects_to" value="{{ (old('redirects_to')) ? old('redirects_to') : url()->previous() }}" />
        <div class="col-lg-9 col-xs-12"> 
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Chỉnh sửa </div>
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
                        @php $i=0; @endphp
                        @foreach($languages as $key => $lang)
                        <div class="tab-pane" id="tab_{{ $key }}">
                            <div class="form-group">
                                <label for="name" class="control-label">Tên</label>
                                <div>
                                    <input type="text" class="form-control {!! (( $key==$default_language ) ? 'title' : '') !!}" name="dataL[{{ $key }}][title]" value="{{ isset( $item->languages[$i] ) ? $item->languages[$i]['title'] : '' }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Meta Title</label>
                                <div>
                                    <input type="text" name="dataL[{{ $key }}][meta_seo][title]" class="form-control meta-title" value="{{ isset( $item->languages[$i] ) ? $item->languages[$i]->meta_seo['title'] : '' }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Meta Keywords</label>
                                <div>
                                    <textarea name="dataL[{{ $key }}][meta_seo][keywords]" class="form-control meta-keywords" rows="6">{{ isset( $item->languages[$i] ) ? $item->languages[$i]->meta_seo['keywords'] : '' }}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">Meta Description</label>
                                <div>
                                    <textarea name="dataL[{{ $key }}][meta_seo][description]" class="form-control meta-description" rows="6">{{ isset( $item->languages[$i] ) ? $item->languages[$i]->meta_seo['description'] : '' }}</textarea>
                                </div>
                            </div>

                        </div>
                        @php $i++ @endphp
                        @endforeach
                    </div>
                </div>
            </div>
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
        </div>
        <div class="col-lg-3 col-xs-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">Thông tin chung </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group @if($item->id == 1) hidden @endif">
                        <label class="control-label">Link</label>
                        <div>
                            <input type="text" name="data[link]" value="{{ $item->link }}" class="form-control">
                        </div>
                    </div>

                    <div class="form-group @if($item->id == 1) hidden @endif">
                        <label class="control-label">Tình trạng</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="status[]" multiple>
                                <option value="publish" {{ (strpos($item->status,'publish') !== false)?'selected':'' }} > Hiển thị </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group @if($item->id == 1) hidden @endif">
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