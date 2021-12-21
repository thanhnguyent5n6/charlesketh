@extends('admin.app')
@section('breadcrumb')
<li>
    <span> Coupon </span>
</li>
@endsection
@section('content')
<div class="row">
	@include('admin.blocks.messages')
	<div class="col-md-12">
		<div class="portlet box green">
			<div class="portlet-title">
                <div class="caption">
                    <i class="icon-layers"></i>Danh sách
                </div>
                <div class="actions">
                    <a href="{{ route('admin.coupon.create') }}" class="btn btn-sm btn-default"> Thêm mới </a>
                    <div class="btn-group">
                        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false"> Hành động (<span class="count-checkbox">0</span>)
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="javascript:;" class="btn-action" data-type="publish" data-ajax="act=update_status|table=coupons|col=status|val=publish"> Hiển thị </a>
                            </li>
                            <li>
                                <a href="javascript:;" class="btn-action" data-type="delete" data-ajax="act=delete_record|table=coupons"> Xóa dữ liệu </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="portlet-body">
				<div class="table-responsive">
					<table class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th width="1%">
                                    <label class="mt-checkbox mt-checkbox-single">
                                        <input type="checkbox" name="select" class="group-checkable">
                                        <span></span>
                                    </label>
                                </th>
								<th width="3%"> Thứ tự </th>
                                <th width="8%"> Mã coupon </th>
                                <th width="10%"> Tiêu đề </th>
                                <th width="8%"> Giá tiền </th>
                                <th width="8%"> Số lần </th>
                                <th width="12%"> Đơn hàng </th>
                                <th width="10%"> Bắt đầu </th>
                                <th width="10%"> Kết thúc </th>
                                <th width="7%"> Đã sử dụng </th>
								<th width="8%"> Tình trạng </th>
                                <th width="10%"> Thực thi </th>
							</tr>
						</thead>
						<tbody>
							@forelse($items as $item)
                            <tr id="record-{{ $item->id }}">
                                <td align="center">
                                    <label class="mt-checkbox mt-checkbox-single">
                                        <input type="checkbox" name="id[]" class="checkable" value="{{ $item->id }}">
                                        <span></span>
                                    </label>
                                </td>
                                <td align="center"> <input type="text" name="priority" class="form-control input-mini input-priority" value="{{ $item->priority }}" data-ajax="act=update_priority|table=coupons|id={{ $item->id }}|col=priority"> </td>
                                <td align="center"><a href="{{ route('admin.coupon.edit',['id'=>$item->id]) }}"> {{ $item->code }} </a></td>
                                <td align="center"><a href="{{ route('admin.coupon.edit',['id'=>$item->id]) }}"> {{ $item->title }} </a></td>
                                <td align="center"> {!! get_currency_vn($item->coupon_amount,'') . ($item->change_conditions_type == 'percentage_discount_from_total_cart' ? '%': '') !!} </td>
                                <td align="center"> {{ $item->number_of_uses }} </td>
                                <td align="center"> {!! get_currency_vn($item->min_restriction_amount,'').' - '.get_currency_vn($item->max_restriction_amount,'') !!} </td>
                                <td align="center"> {{ $item->begin_at }} </td>
                                <td align="center"> {{ $item->end_at }} </td>
                                <td align="center"> {{ $item->used }} </td>
                                <td align="center">
                                    <button class="btn btn-sm btn-status btn-status-publish btn-status-publish-{{ $item->id.' '.( ($item->status == 'publish') ? 'blue' : 'default' ) }}" data-loading-text="<i class='fa fa-spinner fa-pulse'></i>" data-ajax="act=update_status|table=coupons|id={{ $item->id }}|col=status|val=publish"> Hiển thị </button>
                                </td>
                                <td align="center">
                                    <a href="{{ route('admin.coupon.edit',['id'=>$item->id]) }}" class="btn btn-sm blue" title="Chỉnh sửa"> <i class="fa fa-edit"></i> </a>
                                    <form action="{{ route('admin.coupon.delete',['id'=>$item->id]) }}" method="post">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="button" class="btn btn-sm btn-delete red" title="Xóa"> <i class="fa fa-times"></i> </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="30" align="center"> Không có bản dữ liệu trong bảng </td>
                            </tr>
                            @endforelse
						</tbody>
					</table>
				</div>
				<div class="text-center"> {{ $items->links() }} </div>
			</div>
		</div>
	</div>
</div>
@endsection