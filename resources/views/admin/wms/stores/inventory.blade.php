@extends('admin.app')
@section('breadcrumb')
<li>
    <span> {{ $pageTitle }} </span>
</li>
@endsection
@section('content')
<div class="row">
	@include('admin.blocks.messages')
	<div class="col-md-12">
        <div class="portlet">
            <div class="portlet-body">
                <form role="form" method="GET" id="form-search" class="form-inline text-right" action="{{ route('admin.wms_store.inventory') }}" >
                    <div class="form-group">
                        <input type="text" class="form-control input-medium" name="keyword" value="{{ Request::query('keyword') }}" placeholder="Mã sản phẩm">
                    </div>
                    {{--
                    <div class="form-group">
                        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                            <input type="text" class="form-control input-medium" readonly="" name="from_at" value="{{ Request::query('from_at') }}" placeholder="Từ ngày">
                            <span class="input-group-btn">
                                <button class="btn btn-sm default" type="button">
                                    <i class="fa fa-calendar"></i>
                                </button>
                            </span>
                        </div>
                        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                            <input type="text" class="form-control input-medium" readonly="" name="to_at" value="{{ Request::query('to_at') }}" placeholder="Tới ngày">
                            <span class="input-group-btn">
                                <button class="btn btn-sm default" type="button">
                                    <i class="fa fa-calendar"></i>
                                </button>
                            </span>
                        </div>
                    </div>--}}
                    <div class="form-group">
                        <select name="store" class="selectpicker show-tick show-menu-arrow form-control input-medium">
                            @forelse($stores as $store)
                            <option value="{{ $store->code }}" {{ Request::query('store') == $store->code ? 'selected' : '' }} > {{ $store->name }} </option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group">
                        <a href="{{ route('admin.wms_store.inventory') }}" class="btn default"> <i class="fa fa-refresh"></i> Đặt lại</a>
                        <button type="submit" class="btn green"> <i class="fa fa-search"></i> Tìm kiếm</button>
                    </div>
                </form>
            </div>
        </div>
		<div class="portlet box green">
			<div class="portlet-title">
                <div class="caption">
                    <i class="icon-layers"></i>Danh sách
                </div>
            </div>
            @if( @$items )
            <div class="portlet-body">
				<div class="table-responsive">
					<table class="table table-bordered table-condensed">
						<thead>
							<tr>
                                <th width="3%"> # </th>
                                <th width="20%"> Tên sản phẩm </th>
                                <th width="7%"> Đơn vị </th>
                                <th width="7%"> Tổng nhập </th>
                                <th width="7%"> Tổng xuất </th>
                                <th width="7%"> Tổng tồn </th>
                                <th width="7%"> Tình trạng </th>
                                <th width="10%"> Thực thi </th>
							</tr>
						</thead>
						<tbody>
                            @forelse( $items as $key => $item)
                            <tr id="record-{{ $item['id'] }}">
                                <td align="center">{{ $item['id'] }}</td>
                                <td align="center">{{ $item['product_title'] }}</td>
                                <td align="center">{{ ($item['unit'] === 1 ? 'Bộ/Cái' : ($item['unit'] === 2 ? 'Dàn nóng' : 'Dàn lạnh')) }}</td>
                                <td align="center">{{ $item['import'] }}</td>
                                <td align="center">{{ $item['export'] }}</td>
                                <td align="center">{{ $item['inventory'] }}</td>
                                <td align="center"></td>
                                <td align="center"></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="30" align="center"> Không có bản dữ liệu trong bảng </td>
                            </tr>
                            @endforelse
						</tbody>
					</table>
				</div>
                <div class="text-center"> {{ $items->appends(['type'=>$type])->links() }} </div>
			</div>
            @endif
		</div>
	</div>
</div>
@endsection