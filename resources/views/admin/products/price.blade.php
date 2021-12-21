@extends('admin.app')
@section('breadcrumb')
<li>
    <span> Giá {{ $pageTitle }} </span>
</li>
@endsection
@section('content')

<div class="row">
	@include('admin.blocks.messages')
	<div class="col-md-12">
        <div class="portlet">
            <div class="portlet-body">
                <form role="form" method="GET" id="form-search" class="form-inline text-right" action="{{ route('admin.product.price') }}" >
                    <input type="hidden" name="type" value="{{ $type }}">
                    <div class="form-group">
                        <select name="category_id" class="selectpicker show-tick show-menu-arrow form-control input-medium" title="Danh mục">
                            @php
                                Menu::setMenu($categories);
                                echo Menu::getMenuSelect(0,0,'',@$oldInput['category_id']);
                            @endphp
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" name="title" class="form-control input-medium" value="{{ @$oldInput['title'] }}" placeholder="Tên hoặc Mã sản phẩm">
                    </div>
                    <div class="form-group">
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

            <div class="portlet-body">
				<div class="table-responsive">
					<table class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th width="3%"> Thứ tự </th>
                                <th width="10%"> Mã SP </th>
                                <th width="25%"> Tiêu đề </th>
                                <th width="10%"> Giá gốc </th>
                                <th width="10%"> Giá Website </th>
                                <th width="10%"> Giá Cửa Hàng </th>
                                <th width="10%"> Giá VTP </th>
                                <th width="10%"> Giá khuyến mãi </th>
							</tr>
						</thead>
						<tbody>
                            @forelse($items as $item)
                            <tr id="record-{{ $item->id }}">
                                <td align="center"> <input type="text" name="priority" class="form-control input-mini input-priority" value="{{ $item->priority }}" data-ajax="act=update_priority|table=products|id={{ $item->id }}|col=priority"> </td>
                                <td align="center"> {{ $item->code }} </td>
                                <td align="center"><a href="{{ route('admin.product.edit',['id'=>$item->id, 'type'=>$type]) }}"> {{ $item->title }} </a></td>
                                <td align="center"> <input type="text" name="priority" class="form-control input-priority" value="{{ $item->original_price }}" data-ajax="act=update_priority|table=products|id={{ $item->id }}|col=original_price"> </td>
                                <td align="center"> <input type="text" name="priority" class="form-control input-priority" value="{{ $item->wholesale_price }}" data-ajax="act=update_priority|table=products|id={{ $item->id }}|col=wholesale_price"> </td>
                                <td align="center"> <input type="text" name="priority" class="form-control input-priority" value="{{ $item->regular_price }}" data-ajax="act=update_priority|table=products|id={{ $item->id }}|col=regular_price"> </td>
                                <td align="center"> <input type="text" name="priority" class="form-control input-priority" value="{{ $item->vtp_price }}" data-ajax="act=update_priority|table=products|id={{ $item->id }}|col=vtp_price"> </td>
                                <td align="center"> <input type="text" name="priority" class="form-control input-priority" value="{{ $item->sale_price }}" data-ajax="act=update_priority|table=products|id={{ $item->id }}|col=sale_price"> </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="30" align="center"> Không có bản dữ liệu trong bảng </td>
                            </tr>
                            @endforelse
						</tbody>
					</table>
				</div>
				<div class="text-center"> {{ $items->appends($oldInput)->links() }} </div>
			</div>
		</div>
	</div>
</div>
@endsection