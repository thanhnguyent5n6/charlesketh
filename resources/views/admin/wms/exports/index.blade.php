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
		<div class="portlet box green">
			<div class="portlet-title">
                <div class="caption">
                    <i class="icon-layers"></i>Danh sách
                </div>
                <div class="actions">
                    <a href="{{ route('admin.wms_export.create',['type'=>$type]) }}" class="btn btn-sm btn-default"> Thêm mới </a>
                    {{--
                    <div class="btn-group">
                        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false"> Hành động (<span class="count-checkbox">0</span>)
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            @foreach($siteconfig[$type]['status'] as $key => $act)
                            <li>
                                <a href="javascript:;" class="btn-action" data-type="{{ $key }}" data-ajax="act=update_status|table=wms_exports|col=status|val={{ $key }}"> {{ $act }} </a>
                            </li>
                            @endforeach
                            <li>
                                <a href="javascript:;" class="btn-action" data-type="delete" data-ajax="act=delete_record|table=wms_exports"> Xóa dữ liệu </a>
                            </li>
                        </ul>
                    </div>
                    --}}
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
                                <th width="7%"> Nhân viên </th>
                                <th width="7%"> Mã phiếu </th>
                                <th width="7%"> Kho hàng </th>
                                <th width="7%"> Tổng xuất </th>
                                <th width="7%"> Tổng tiền </th>
                                <th width="7%"> Ngày tạo </th>
								<th width="7%"> Tình trạng </th>
                                <th width="10%"> Thực thi </th>
							</tr>
						</thead>
						<tbody>
                            <tr>
                                <td colspan="30" align="center">
                                    Tổng số lượng: <span class="font-red-mint font-md bold"> {!! get_currency_vn($total->qty,'') !!} </span>
                                    -
                                    Tổng tiền: <span class="font-red-mint font-md bold"> {!! get_currency_vn($total->price,'') !!} </span>
                                </td>
                            </tr>
                            @forelse($items as $item)
                            <tr id="record-{{ $item->id }}">
                                <td align="center">
                                    <label class="mt-checkbox mt-checkbox-single">
                                        <input type="checkbox" name="id[]" class="checkable" value="{{ $item->id }}">
                                        <span></span>
                                    </label>
                                </td>
                                <td align="center"> <input type="text" name="priority" class="form-control input-mini input-priority" value="{{ $item->priority }}" data-ajax="act=update_priority|table=wms_exports|id={{ $item->id }}|col=priority"> </td>
                                <td align="center">{{ $item->username }}</td>
                                <td align="center">{{ $item->code }}</td>
                                <td align="center">{{ $item->store_code }}</td>
                                <td align="center">{{ $item->export_qty }}</td>
                                <td align="center">{!! get_currency_vn($item->export_price,'') !!}</td>
                                <td align="center"> {{ $item->created_at }} </td>
                                <td align="center">
                                    @foreach($siteconfig[$type]['status'] as $keyS => $valS)
                                        @if(strpos($item->status,$keyS) !== false)
                                        <span class="label label-sm label-{{ ($keyS == 'publish') ? 'primary' : 'danger' }}"> {{ $valS }} </span>
                                        @endif
                                    @endforeach
                                </td>
                                <td align="center">
                                    <a href="{{ route('admin.wms_export.edit',['id'=>$item->id, 'type'=>$type]) }}" class="btn btn-sm blue" title="Chỉnh sửa"> <i class="fa fa-edit"></i> </a>
                                    @if($item->status == 'publish')
                                    <a href="#" data-target="#cancel-modal" data-toggle="modal" data-url="{{ route('admin.wms_export.delete',['id'=>$item->id, 'type'=>$type]) }}" class="btn btn-sm btn-cancel red" title="Hủy phiếu"> <i class="fa fa-ban"></i> </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="30" align="center"> Không có bản dữ liệu trong bảng </td>
                            </tr>
                            @endforelse
                            <tr>
                                <td colspan="30" align="center">
                                    Tổng số lượng: <span class="font-red-mint font-md bold"> {!! get_currency_vn($total->qty,'') !!} </span>
                                    -
                                    Tổng tiền: <span class="font-red-mint font-md bold"> {!! get_currency_vn($total->price,'') !!} </span>
                                </td>
                            </tr>
						</tbody>
					</table>
				</div>
				<div class="text-center"> {{ $items->appends(['type'=>$type])->links() }} </div>
			</div>
		</div>
	</div>
</div>
<!-- Add Cancel Modal -->
<div id="cancel-modal" class="modal fade" tabindex="-1" data-focus-on="input:first">
    <form role="form" method="POST" action="#" class="form-validation">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <input type="hidden" name="data[status]" value="cancel">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 class="modal-title uppercase">Lý do hủy phiếu</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <div>
                    <textarea name="data[note_cancel]" class="form-control validate[required]" rows="5" data-prompt-position="bottomRight:-100"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn default">Thoát</button>
            <button type="submit" class="btn green" > <i class="fa fa-check"></i> Lưu</button>
        </div>
    </form>
</div>
@endsection