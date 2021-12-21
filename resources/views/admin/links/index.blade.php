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
                    <a href="{{ route('admin.link.create',['type'=>$type]) }}" class="btn btn-sm btn-default"> Thêm mới </a>
                    <div class="btn-group">
                        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false"> Hành động (<span class="count-checkbox">0</span>)
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            @foreach($siteconfig[$type]['status'] as $key => $act)
                            <li>
                                <a href="javascript:;" class="btn-action" data-type="{{ $key }}" data-ajax="act=update_status|table=links|col=status|val={{ $key }}"> {{ $act }} </a>
                            </li>
                            @endforeach
                            <li>
                                <a href="javascript:;" class="btn-action" data-type="delete" data-ajax="act=delete_record|table=links|config=link|type={{ $type }}"> Xóa dữ liệu </a>
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
                                <th width="15%"> Tiêu đề </th>

                                @if($siteconfig[$type]['support'])
                                <th width="10%"> Email </th>
                                <th width="10%"> Điện thoại </th>
                                <th width="10%"> Facebook </th>
                                <th width="10%"> Skype </th>
                                @endif

                                @if($siteconfig[$type]['youtube'])
                                <th width="15%"> Youtube </th>
                                @endif

								@if($siteconfig[$type]['image'])
                                <th width="15%"> Hình ảnh </th>
                                @endif
                                
                                @if($siteconfig[$type]['link'])
                                <th width="15%"> Link </th>
                                @endif
                                <th width="10%"> Ngày tạo </th>
								<th width="10%"> Tình trạng </th>
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
                                <td align="center"> <input type="text" name="priority" class="form-control input-mini input-priority" value="{{ $item->priority }}" data-ajax="act=update_priority|table=links|id={{ $item->id }}|col=priority"> </td>
                                <td align="center"><a href="{{ route('admin.link.edit',['id'=>$item->id, 'type'=>$type]) }}"> {{ $item->title }} </a></td>

                                @if($siteconfig[$type]['support'])
                                <td align="center">{{$item->email}}</td>
                                <td align="center">{{$item->phone}}</td>
                                <td align="center">{{$item->facebook}}</td>
                                <td align="center">{{$item->skype}}</td>
                                @endif

                                @if($siteconfig[$type]['youtube'])
                                <td align="center">{{$item->youtube}}</td>
                                @endif

                                @if($siteconfig[$type]['image'])
                                <td align="center">{!! ( ($item->image && file_exists(public_path(get_thumbnail($path.'/'.$item->image))) ) ? '<img src="'.asset( get_thumbnail('public/'.$path.'/'.$item->image) ).'" height="50" />' : '') !!}</td>
                                @endif

                                @if($siteconfig[$type]['link'])
                                <td align="center">{{$item->link}}</td>
                                @endif
                                <td align="center"> {{ $item->created_at }} </td>
                                <td align="center">
                                    @foreach($siteconfig[$type]['status'] as $keyS => $valS)
                                        <button class="btn btn-sm btn-status btn-status-{{ $keyS}} btn-status-{{ $keyS.'-'.$item->id.' '.((strpos($item->status,$keyS) !== false)?'blue':'default') }}" data-loading-text="<i class='fa fa-spinner fa-pulse'></i>" data-ajax="act=update_status|table=links|id={{ $item->id }}|col=status|val={{ $keyS }}"> {{ $valS }} </button>
                                    @endforeach
                                </td>
                                <td align="center">
                                    <a href="{{ route('admin.link.edit',['id'=>$item->id, 'type'=>$type]) }}" class="btn btn-sm blue" title="Chỉnh sửa"> <i class="fa fa-edit"></i> </a>
                                    <form action="{{ route('admin.link.delete',['id'=>$item->id, 'type'=>$type]) }}" method="post">
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
				<div class="text-center"> {{ $items->appends(['type'=>$type])->links() }} </div>
			</div>
		</div>
	</div>
</div>
@endsection