@extends('admin.app')
@section('breadcrumb')
<li>
    <span> Phiếu báo giá </span>
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
                    <a href="{{ route('admin.price.create') }}" class="btn btn-sm btn-default"> Thêm mới </a>
                    <div class="btn-group">
                        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false"> Hành động (<span class="count-checkbox">0</span>)
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="javascript:;" class="btn-action" data-type="delete" data-ajax="act=delete_record|table=warranties"> Xóa dữ liệu </a>
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
                                @if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  )
                                <th width="5%"> Nhân viên </th>
                                @endif
                                <th width="10%">Mã phiếu</th>
                                <th width="10%"> Ngày tạo </th>
                                <th width="15%"> Thực thi </th>
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
                                <td align="center"> <input type="text" name="priority" class="form-control input-mini input-priority" value="{{ $item->priority }}" data-ajax="act=update_priority|table=prices|id={{ $item->id }}|col=priority"> </td>
                                @if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  )
                                <td align="center">{{ $item->user->name }}</td>
                                @endif
                                
                                <td align="center"> @php if( strpos($item->status,'printed') !== false ) echo '<button class="btn btn-sm btn-danger"> <i class="fa fa-print"></i></button>'; @endphp {{ $item->code }}</td>
                                <td align="center">{{ $item->created_at }}</td>
                                <td align="center">
                                    <a href="javascript:;" class="btn btn-sm blue btn-print" title="In phiếu" data-ajax="id={{$item->id}}"> In Phiếu <i class="fa fa-print"></i></a>
                                    <a href="{{ route('admin.price.edit',['id'=>$item->id]) }}" class="btn btn-sm blue" title="Chỉnh sửa"> <i class="fa fa-edit"></i> Xem & Sửa</a>
                                    @if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  )
                                    <form action="{{ route('admin.price.delete',['id'=>$item->id]) }}" method="post">
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

@section('custom_script')
<script type="text/javascript">
    $(document).ready(function(){

        $("a.btn-print").on('click', function(e){
            e.preventDefault();
            var btn = $(this);
            var dataAjax = btn.data('ajax').replace(/\|/g,'&');
            $.ajax({
                type: 'GET',
                url : Laravel.baseUrl+'/admin/prices/print',
                data: dataAjax,
                dataType: 'json'
            }).done(function(obj){
                var contents = obj.data;
                var frame1 = $('<iframe />');
                frame1[0].name = "frame1";
                frame1.css({ "position": "absolute", "top": "-1000000px" });
                $("body").append(frame1);
                var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
                frameDoc.document.open();
                //Create a new HTML document.
                frameDoc.document.write('<html><head><title>{{ config('settings.site_name') }}</title>');
                frameDoc.document.write('</head><body>');
                //Append the external CSS file.
                frameDoc.document.write('<link href="{{ asset('packages/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />');
                frameDoc.document.write('<link href="{{ asset('admin/css/print.css') }}" rel="stylesheet" type="text/css" />');
                //Append the DIV contents.
                frameDoc.document.write(contents);
                frameDoc.document.write('</body></html>');
                frameDoc.document.close();
                setTimeout(function () {
                    window.frames["frame1"].focus();
                    window.frames["frame1"].print();
                    frame1.remove();
                }, 500);
            });
            
        });

        $('.btn-print-all').on('click', function(e) {
            e.preventDefault();
            var btn = $(this);
            if (typeof btn.data('ajax') === 'undefined') return;
            var dataAjax = btn.data('ajax').replace(/\|/g,'&');
            var listID = '';

            $('input.checkable:checked').each(function(){
                listID = listID+","+$(this).val();
            });

            listID=listID.substr(1);
            if (listID == '') {
                App.alert({
                    container: '#alert-container', // alerts parent container(by default placed after the page breadcrumbs)
                    place: 'append', // "append" or "prepend" in container 
                    type: 'danger', // alert's type
                    message: 'Không có bản ghi nào được chọn', // alert's message
                    close: true, // make alert closable
                    reset: true, // close all previouse alerts first
                    focus: true, // auto scroll to the alert after shown
                    closeInSeconds: 5, // auto close after defined seconds
                    icon: 'warning' // put icon before the message
                });
                return false;
            } else {
                $.ajax({
                    type: 'GET',
                    url : Laravel.baseUrl+'/admin/prices/print',
                    data: dataAjax+'&id='+listID,
                    dataType: 'json'
                }).done(function(obj){
                    var contents = obj.data;
                    var frame1 = $('<iframe />');
                    frame1[0].name = "frame1";
                    frame1.css({ "position": "absolute", "top": "-1000000px" });
                    $("body").append(frame1);
                    var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
                    frameDoc.document.open();
                    //Create a new HTML document.
                    frameDoc.document.write('<html><head><title>{{ config('settings.site_name') }}</title>');
                    frameDoc.document.write('</head><body>');
                    //Append the external CSS file.
                    frameDoc.document.write('<link href="{{ asset('packages/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />');
                    frameDoc.document.write('<link href="{{ asset('admin/css/print.css') }}" rel="stylesheet" type="text/css" />');
                    //Append the DIV contents.
                    frameDoc.document.write(contents);
                    frameDoc.document.write('</body></html>');
                    frameDoc.document.close();
                    setTimeout(function () {
                        window.frames["frame1"].focus();
                        window.frames["frame1"].print();
                        frame1.remove();
                    }, 500);
                });
            }
        });
    });
</script>
@endsection