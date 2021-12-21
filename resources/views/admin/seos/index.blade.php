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
                    <a href="{{ route('admin.seo.create') }}" class="btn btn-sm btn-default"> Thêm mới </a>
                    <div class="btn-group">
                        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false"> Hành động (<span class="count-checkbox">0</span>)
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="javascript:;" class="btn-action" data-type="publish" data-ajax="act=update_status|table=seos|col=status|val=publish"> Hiển thị </a>
                            </li>
                            <li>
                                <a href="javascript:;" class="btn-action" data-type="delete" data-ajax="act=delete_record|table=seos"> Xóa dữ liệu </a>
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
								<th width="4%"> Thứ tự </th>
                                <th width="25%"> Tiêu đề </th>
                                <th width="25%"> Link seo </th>
                                <th width="10%"> Ngày tạo </th>
                                <th width="10%"> Tình trạng </th>
                                <th width="10%"> Thực thi </th>
							</tr>
						</thead>
						<tbody>
                            @forelse($items as $item)
                            <tr id="record-{{ $item->id }}">
                                <td align="center">
                                    @if($item->id > 1)
                                    <label class="mt-checkbox mt-checkbox-single">
                                        <input type="checkbox" name="id[]" class="checkable" value="{{ $item->id }}">
                                        <span></span>
                                    </label>
                                    @endif
                                </td>
                                <td align="center">
                                    @if($item->id > 1)
                                    <input type="text" name="priority" class="form-control input-mini input-priority" value="{{ $item->priority }}" data-ajax="act=update_priority|table=seos|id={{ $item->id }}|col=priority">
                                    @endif
                                </td>
                                <td align="center"><a href="{{ route('admin.seo.edit',['id'=>$item->id]) }}"> {{ $item->title }} </a></td>
                                <td align="center"><a href="{{ $item->link }}" target="_blank"> {{ $item->link }} </a></td>
                                <td align="center"> {{ $item->created_at }} </td>
                                <td align="center">
                                    @if($item->id == 1)
                                    <button class="btn btn-sm"> Mặc định </button>
                                    @else
                                    <button class="btn btn-sm btn-status btn-status-publish btn-status-publish-{{ $item->id.' '.((strpos($item->status,'publish') !== false)?'blue':'default') }}" data-loading-text="<i class='fa fa-spinner fa-pulse'></i>" data-ajax="act=update_status|table=seos|id={{ $item->id }}|col=status|val=publish"> Hiển thị </button>
                                    @endif
                                </td>
                                
                                <td align="center">
                                    <a href="{{ route('admin.seo.edit',['id'=>$item->id]) }}" class="btn btn-sm blue" title="Chỉnh sửa"> <i class="fa fa-edit"></i> </a>
                                    @if($item->id > 1)
                                    <form action="{{ route('admin.seo.delete',['id'=>$item->id]) }}" method="post">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="button" class="btn btn-sm btn-delete red" title="Xóa"> <i class="fa fa-times"></i> </button>
                                    </form>
                                    @endif
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