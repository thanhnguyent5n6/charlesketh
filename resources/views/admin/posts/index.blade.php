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
        {{--
        <div class="portlet">
            <div class="portlet-body">
                <form role="form" method="GET" id="form-search" class="form-inline text-right" action="{{ route('admin.post.index') }}" >
                    <input type="hidden" name="type" value="{{ $type }}">
                    @if($siteconfig[$type]['category'])
                    <div class="form-group">
                        <select name="category_id" class="selectpicker show-tick show-menu-arrow form-control input-medium" title="Danh mục">
                            @php
                                Menu::setMenu($categories);
                                echo Menu::getMenuSelect(0,0,'',@$oldInput['category_id']);
                            @endphp
                        </select>
                    </div>
                    @endif
                    <div class="form-group">
                        <input type="text" name="title" class="form-control input-medium" value="{{ @$oldInput['title'] }}" placeholder="Tiêu đề">
                    </div>
                    <div class="form-group">
                        <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                            <input type="text" class="form-control input-medium" readonly="" name="created_at" value="{{ @$oldInput['created_at'] }}" placeholder="Ngày tạo">
                            <span class="input-group-btn">
                                <button class="btn btn-sm default" type="button">
                                    <i class="fa fa-calendar"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <select class="selectpicker show-tick show-menu-arrow form-control input-medium" name="status" title="Tình trạng">
                            @foreach($siteconfig[$type]['status'] as $key => $val)
                            <option value="{{ $key }}" {{ (@$oldInput['status'] == $key) ? 'selected' : '' }} > {{ $val }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn green"> <i class="fa fa-search"></i> Tìm kiếm</button>
                        <a href="{{ route('admin.post.index',['type'=>$type]) }}" class="btn default"> <i class="fa fa-refresh"></i> Đặt lại</a>
                    </div>
                </form>
            </div>
        </div>
        --}}
		<div class="portlet box green">
			<div class="portlet-title">
                <div class="caption">
                    <i class="icon-layers"></i>Danh sách
                </div>
                <div class="actions">
                    <a href="{{ route('admin.post.create',['type'=>$type]) }}" class="btn btn-sm btn-default"> Thêm mới </a>
                    <div class="btn-group">
                        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false"> Hành động (<span class="count-checkbox">0</span>)
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            @foreach($siteconfig[$type]['status'] as $key => $act)
                            <li>
                                <a href="javascript:;" class="btn-action" data-type="{{ $key }}" data-ajax="act=update_status|table=posts|col=status|val={{ $key }}"> {{ $act }} </a>
                            </li>
                            @endforeach
                            <li>
                                <a href="javascript:;" class="btn-action" data-type="delete" data-ajax="act=delete_record|table=posts|config=post|type={{ $type }}"> Xóa dữ liệu </a>
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
                                @if($siteconfig[$type]['category'])
                                <th width="10%"> Danh mục </th>
                                @endif
                                <th width="25%"> Tiêu đề </th>
								@if($siteconfig[$type]['image'])
                                <th width="15%"> Hình ảnh </th>
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
                                <td align="center"> <input type="text" name="priority" class="form-control input-mini input-priority" value="{{ $item->priority }}" data-ajax="act=update_priority|table=posts|id={{ $item->id }}|col=priority"> </td>
                                @if($siteconfig[$type]['category'])
                                <td align="center"> {{ $item->category }} </td>
                                @endif
                                <td align="center"><a href="{{ route('admin.post.edit',['id'=>$item->id, 'type'=>$type]) }}"> {{ $item->title }} </a></td>
                                @if($siteconfig[$type]['image'])
                                <td align="center">{!! ( ($item->image && file_exists(public_path(get_thumbnail($path.'/'.$item->image))) ) ? '<img src="'.asset( get_thumbnail('public/'.$path.'/'.$item->image) ).'" height="50" />' : '') !!}</td>
                                @endif
                                <td align="center"> {{ $item->created_at }} </td>
                                <td align="center">
                                    @foreach($siteconfig[$type]['status'] as $keyS => $valS)
                                        <button class="btn btn-sm btn-status btn-status-{{ $keyS}} btn-status-{{ $keyS.'-'.$item->id.' '.((strpos($item->status,$keyS) !== false)?'blue':'default') }}" data-loading-text="<i class='fa fa-spinner fa-pulse'></i>" data-ajax="act=update_status|table=posts|id={{ $item->id }}|col=status|val={{ $keyS }}"> {{ $valS }} </button>
                                    @endforeach
                                </td>
                                <td align="center">
                                    <a href="{{ route('admin.post.edit',['id'=>$item->id, 'type'=>$type]) }}" class="btn btn-sm blue" title="Chỉnh sửa"> <i class="fa fa-edit"></i> </a>
                                    <form action="{{ route('admin.post.delete',['id'=>$item->id, 'type'=>$type]) }}" method="post">
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
				<div class="text-center"> {{ $items->appends($oldInput)->links() }} </div>
			</div>
		</div>
	</div>
</div>
@endsection