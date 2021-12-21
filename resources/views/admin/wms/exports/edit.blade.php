@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.wms_export.index', ['type'=>$type]) }}"> {{ $pageTitle }} </a>
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
    <form role="form" method="POST" action="{{ route('admin.wms_export.update',['id'=>$item->id,'type'=>$type]) }}" class="form-validation">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <input type="hidden" name="redirects_to" value="{{ (old('redirects_to')) ? old('redirects_to') : url()->previous() }}" />

        <div class="col-lg-9 col-xs-12" id="qh-app"> 
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Chỉnh sửa </div>
                </div>
                <div class="portlet-body">
                    {{--
                    <div class="form-group">
                        <div class="input-group select2-bootstrap-append">
                            <select id="select2-button-addons-single-input-group-sm" class="form-control select2-data-ajax"  multiple="" data-label="Mã sản phẩm" data-url="{{ route('admin.wms_import.ajax',['type'=>'default']) }}">
                            </select>
                            <span class="input-group-btn"> <button v-on:click="addProduct" type="button" id="btn-select" class="btn btn-info"> Chọn </button> </span>
                        </div>
                    </div>
                    --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th width="7%"> Mã SP </th>
                                    <th width="15%"> Tên SP </th>
                                    <th width="7%"> Đơn vị tính </th>
                                    <th width="7%"> Đơn giá </th>
                                    <th width="6%"> Số lượng </th>
                                    <th width="10%"> Thành tiền </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr>
                                    <td align="center">{{ $product->product_code }}</td>
                                    <td>{{ $product->product_title }}</td>
                                    <td align="center">{{ ($product->unit === 1 ? 'Bộ/Cái' : ($product->unit === 2 ? 'Dàn nóng' : 'Dàn lạnh')) }}</td>
                                    <td align="center">{!! get_currency_vn($product->product_price,'') !!}</td>
                                    <td align="center">{{ $product->product_qty }}</td>
                                    <td align="center">{!! get_currency_vn($product->product_price*$product->product_qty,'') !!}</td>
                                </tr>
                                @empty
                                @endforelse
                                <tr>
                                    <td align="right" colspan="30">
                                        <span class="pull-left text-uppercase">
                                            Số lượng: <span class="font-red-mint font-md bold">{!! get_currency_vn($item->export_qty,'') !!}</span>
                                        </span>
                                        <span class="pull-right text-uppercase">
                                            Tổng: <span class="font-red-mint font-md bold">{!! get_currency_vn($item->export_price,'') !!}</span>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                        <label class="control-label"> Kho hàng </label>
                        <div>
                            <select name="data[store_code]" class="selectpicker show-tick show-menu-arrow form-control" disabled="disabled">
                                @forelse($warehouses as $warehouse)
                                <option value="{{ $warehouse->code }}" {{ $warehouse->code == $item->store_code ? 'selected' : '' }} > {{ $warehouse->name }} </option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Mã Phiếu</label>
                        <div>
                            <input type="text" name="data[code]" class="form-control" value="{{ $item->code }}" readonly="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Tình trạng</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="status[]">
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
                        {{--<button type="submit" class="btn green"> <i class="fa fa-check"></i> Lưu</button>--}}
                        <a href="{{ url()->previous() }}" class="btn default" > Thoát </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection