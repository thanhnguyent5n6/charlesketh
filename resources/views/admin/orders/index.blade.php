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
                <form role="form" method="GET" id="form-search" class="form-inline text-right" action="{{ route('admin.order.index') }}" >
                    <input type="hidden" name="type" value="{{ $type }}">
                    <div class="form-group">
                        <input type="text" class="form-control input-medium" name="keyword" value="{{ @$oldInput['keyword'] }}" placeholder="Từ khóa">
                    </div>
                    <div class="form-group">
                        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                            <input type="text" class="form-control input-medium" readonly="" name="from_at" value="{{ @$oldInput['from_at'] }}" placeholder="Từ ngày">
                            <span class="input-group-btn">
                                <button class="btn btn-sm default" type="button">
                                    <i class="fa fa-calendar"></i>
                                </button>
                            </span>
                        </div>
                        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                            <input type="text" class="form-control input-medium" readonly="" name="to_at" value="{{ @$oldInput['to_at'] }}" placeholder="Tới ngày">
                            <span class="input-group-btn">
                                <button class="btn btn-sm default" type="button">
                                    <i class="fa fa-calendar"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <select class="selectpicker show-tick show-menu-arrow form-control input-medium" name="status" title="Tình trạng">
                            @foreach($siteconfig[$type]['site']['status'] as $key => $val)
                            <option value="{{ $key }}" {{ (@$oldInput['status'] == $key) ? 'selected' : '' }} > {{ $val }} </option>
                            @endforeach
                            <option value="construction" {{ (@$oldInput['status'] == 'construction') ? 'selected' : '' }} >Thi công</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                            <input type="text" class="form-control input-medium" readonly="" name="delivery_time_from" value="{{ @$oldInput['delivery_time_from'] }}" placeholder="Giao hàng từ ngày">
                            <span class="input-group-btn">
                                <button class="btn btn-sm default" type="button">
                                    <i class="fa fa-calendar"></i>
                                </button>
                            </span>
                        </div>
                        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                            <input type="text" class="form-control input-medium" readonly="" name="delivery_time_to" value="{{ @$oldInput['delivery_time_to'] }}" placeholder="Giao hàng tới ngày">
                            <span class="input-group-btn">
                                <button class="btn btn-sm default" type="button">
                                    <i class="fa fa-calendar"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <a href="{{ route('admin.order.index',['type'=>$type]) }}" class="btn default"> <i class="fa fa-refresh"></i> Đặt lại</a>
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
                <div class="actions">
                    <a href="{{ route('admin.order.create',['type'=>$type]) }}" class="btn btn-sm btn-default"> Thêm mới </a>
                    @if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  )
                    <div class="btn-group">
                        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false"> Hành động (<span class="count-checkbox">0</span>)
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="javascript:;" class="btn-print-all" data-ajax="loai=1"> Đề xuất bán hàng </a>
                            </li>
                            <li>
                                <a href="javascript:;" class="btn-print-all" data-ajax="loai=2"> Phiếu xuất kho </a>
                            </li>
                            <li>
                                <a href="javascript:;" class="btn-print-all" data-ajax="loai=4"> Phiếu xuất kho ko giá </a>
                            </li>
                            <li>
                                <a href="javascript:;" class="btn-print-all" data-ajax="loai=3"> Phiếu thu cọc </a>
                            </li>
                            <li class="divider"></li>
                            @foreach($siteconfig[$type]['site']['status'] as $key => $act)
                            <li>
                                <a href="javascript:;" class="btn-action" data-type="{{ $key }}" data-ajax="act=update_status|table=orders|col=status|val={{ $key }}"> {{ $act }} </a>
                            </li>
                            @endforeach
                            <li class="divider"></li>
                            <li class="hidden">
                                <a href="javascript:;" class="btn-action" data-type="delete" data-ajax="act=delete_record|table=orders"> Xóa dữ liệu </a>
                            </li>
                        </ul>
                    </div>
                    @endif
                    <div class="btn-group">
                        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false"> Excel
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="{{ route('admin.order.export', array_merge(Request::query(),['extension'=>'xlsx']) ) }}"> Export </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.order.export-customer', array_merge(Request::query(),['extension'=>'xlsx']) ) }}"> Export khách hàng</a>
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
                                <th width="10%"> Nhân viên </th>
                                @endif
                                <th width="10%"> Khách hàng </th>
                                <th width="7%"> Mã đơn hàng </th>
                                <th width="7%"> Số lượng </th>
                                <th width="7%"> Tổng giá </th>
                                <th width="10%"> Ngày đặt </th>
                                <th width="10%"> Tình trạng </th>
                                <th width="10%"> Thực thi </th>
							</tr>
						</thead>
						<tbody>
                            <tr>
                                <td colspan="30" align="center">
                                    Tổng đơn hàng: <span class="font-red-mint font-md bold"> {!! get_currency_vn($total['count'],'') !!} </span>
                                    -
                                    Tổng số lượng: <span class="font-red-mint font-md bold"> {!! get_currency_vn($total['qty'],'') !!} </span>
                                    -
                                    Tổng tiền: <span class="font-red-mint font-md bold"> {!! get_currency_vn($total['price'],'') !!} </span>
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
                                <td align="center"> <input type="text" name="priority" class="form-control input-mini input-priority" value="{{ $item->priority }}" data-ajax="act=update_priority|table=orders|id={{ $item->id }}|col=priority"> </td>
                                @if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  )
                                <td align="center">@php if( $item->user_id ){ $user = get_user($item->user_id); echo $user->name; } @endphp</td>
                                @endif

                                @if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  )
                                <td align="center"><a href="{{ route('admin.order.edit',['id'=>$item->id, 'type'=>$type]) }}"> {{ $item->name.' - '.$item->phone }} </a></td>
                                @else
                                <td align="center"><a href="{{ route('admin.order.show',['id'=>$item->id, 'type'=>$type]) }}"> {{ $item->name.' - '.$item->phone }} </a></td>
                                @endif

                                <td align="center"> @php if( strpos($item->status,'printed') !== false ) echo '<button class="btn btn-sm btn-danger"> <i class="fa fa-print"></i></button>'; @endphp {{ $item->code }}</td>
                                <td align="center">{{ $item->order_qty }}</td>
                                <td align="center">{!! get_currency_vn($item->order_price,'') !!}</td>
                                <td align="center">{{ $item->created_at }}</td>
                                <td align="center">
                                    <span class="label label-sm label-{{ $siteconfig[$type]['site']['label'][$item->status_id] }}">{{ $siteconfig[$type]['site']['status'][$item->status_id] }}</span>
                                    
                                    <a href="#" style="margin-top: 5px;" data-target="#cancel-modal" data-toggle="modal" data-url="{{ route('admin.order.update',['id'=>$item->id, 'type'=>$type]) }}" class="btn btn-sm btn-cancel blue" title="Thanh toán"> Thanh toán {!! $item->received_amount ? get_currency_vn($item->received_amount,'') : '' !!}</a>

                                    @if($item->deposit_amount)
                                    <a href="#" data-target="#cancel-modal" data-toggle="modal" data-url="{{ route('admin.order.update',['id'=>$item->id, 'type'=>$type]) }}" class="btn btn-sm btn-cancel blue" title="Đã thu cọc"> Đã thanh toán: {!! get_currency_vn($item->deposit_amount,'') !!}</a>
                                    @endif

                                    @if($item->installation_fees)
                                    <a href="#" data-target="#cancel-modal" data-toggle="modal" data-url="{{ route('admin.order.update',['id'=>$item->id, 'type'=>$type]) }}" class="btn btn-sm btn-cancel blue" title="Đã thu cọc"> Phí lắp đặt {!! get_currency_vn($item->installation_fees,'') !!}</a>
                                    @endif

                                    @if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  )
                                    <button class="btn btn-sm btn-status btn-status-construction btn-status-{{ 'construction-'.$item->id.' '.((strpos($item->status,'construction') !== false)?'blue':'default') }}" data-loading-text="<i class='fa fa-spinner fa-pulse'></i>" data-ajax="act=update_status|table=orders|id={{ $item->id }}|col=status|val=construction"> Thi công </button>
                                    @endif
                                </td>
                                <td align="center">
                                    @if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  )
                                    <a href="javascript:;" class="btn btn-sm default btn-print" title="Đề xuất bán hàng" data-ajax="id={{ $item->id }}|loai=1"> Phiếu ĐX <i class="fa fa-print"></i></a>
                                    <a href="javascript:;" class="btn btn-sm blue btn-print" title="Phiếu xuất kho" data-ajax="id={{ $item->id }}|loai=2"> Phiếu XK <i class="fa fa-print"></i></a>
                                    <a href="javascript:;" class="btn btn-sm blue btn-print" title="Phiếu xuất kho ko giá" data-ajax="id={{ $item->id }}|loai=4"> Phiếu XKKG <i class="fa fa-print"></i></a>
                                    <a href="javascript:;" class="btn btn-sm green btn-print" title="Phiếu thu cọc" data-ajax="id={{ $item->id }}|loai=3"> Phiếu TC <i class="fa fa-print"></i></a>
                                    <a href="javascript:;" class="btn btn-sm blue btn-print" title="Phiếu DV" data-ajax="id={{ $item->id }}|loai=5|giamgia=0"> In Phiếu DV <i class="fa fa-print"></i></a>

                                    <br/>
                                    <a href="{{ route('admin.order.edit',['id'=>$item->id, 'type'=>$type]) }}" class="btn btn-sm blue" title="Chỉnh sửa"> <i class="fa fa-edit"></i> </a>
                                    <form action="{{ route('admin.order.delete',['id'=>$item->id, 'type'=>$type]) }}" method="post" class="hidden">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="button" class="btn btn-sm btn-delete red" title="Xóa"> <i class="fa fa-times"></i> </button>
                                    </form>
                                    @else
                                    <a href="{{ route('admin.order.show',['id'=>$item->id, 'type'=>$type]) }}" class="btn btn-sm blue" title="Chỉnh sửa"> <i class="fa fa-edit"></i> </a>
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
                                    Tổng đơn hàng: <span class="font-red-mint font-md bold"> {!! get_currency_vn($total['count'],'') !!} </span>
                                    -
                                    Tổng số lượng: <span class="font-red-mint font-md bold"> {!! get_currency_vn($total['qty'],'') !!} </span>
                                    -
                                    Tổng tiền: <span class="font-red-mint font-md bold"> {!! get_currency_vn($total['price'],'') !!} </span>
                                </td>
                            </tr>
						</tbody>
					</table>
				</div>
				<div class="text-center"> {{ $items->appends($oldInput)->links() }} </div>
			</div>
		</div>
	</div>
</div>
@if ( Auth::user()->hasRole('admin') || Auth::user()->groups()->first()->id == 5  )
<!-- Add Cancel Modal -->
<div id="cancel-modal" class="modal fade" tabindex="-1" data-focus-on="input:first">
    <form role="form" method="POST" action="#" class="form-validation">
        {{ csrf_field() }}
        {{ method_field('PUT') }}
        <input type="hidden" name="redirects_to" value="{{ url()->full() }}" />
        <input type="hidden" name="update_form_list" value="1" />
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 class="modal-title uppercase">Thu cọc và Thanh toán</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <b>Thu cọc</b>
                <div class="input-group">
                    <input type="text" name="deposit_amount" class="form-control priceFormat" />
                    <span class="input-group-addon"> Đ </span>
                </div>
            </div>
            <div class="form-group">
                <b>Thanh toán</b>
                <div class="input-group">
                    <input type="text" name="received_amount" class="form-control priceFormat" />
                    <span class="input-group-addon"> Đ </span>
                </div>
            </div>
            <div class="form-group">
                <b>Phí vật tư</b>
                <div class="input-group">
                    <input type="text" name="installation_fees" class="form-control priceFormat" />
                    <span class="input-group-addon"> Đ </span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn default">Thoát</button>
            <button type="submit" class="btn green" > <i class="fa fa-check"></i> Lưu</button>
        </div>
    </form>
</div>
@endif
@endsection

@section('custom_script')
<script type="text/javascript">
    $(document).ready(function(){
        $("a.btn-print").on('click', function(e){
            e.preventDefault();
            var btn = $(this);
            var dataAjax = btn.data('ajax').replace(/\|/g,'&') + '&type={{ $type }}';
            $.ajax({
                type: 'GET',
                url : Laravel.baseUrl+'/admin/orders/print',
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
            var dataAjax = btn.data('ajax').replace(/\|/g,'&') + '&type={{ $type }}';
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
                    url : Laravel.baseUrl+'/admin/orders/print',
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