@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.delivery.index', ['type'=>$type]) }}"> {{ $pageTitle }} </a>
    <i class="fa fa-circle"></i>
</li>
<li>
    <span>Thêm mới</span>
</li>
@endsection
@section('content')
<div class="row" id="qh-app">
    @include('admin.blocks.messages')
    <!-- BEGIN FORM-->
    <form role="form" method="POST" action="{{ route('admin.delivery.update',['id'=>$item->id,'type'=>$type]) }}" class="form-validation">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <input type="hidden" name="redirects_to" value="{{ (old('redirects_to')) ? old('redirects_to') : url()->previous() }}" />
        <div class="col-lg-9 col-xs-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Thêm mới </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-barcode" aria-hidden="true"></i> Mã đơn hàng</label>
                        <div style="margin-bottom: 15px;" v-for="(item, key) in orders">
                            <input type="text" name="order_code[]" class="form-control validate[required]" :value="item">
                        </div>
                    </div>
                    <div class="form-group">
                        <div>
                            <input type="text" class="form-control scanner">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-book" aria-hidden="true"></i> Ghi chú</label>
                        <div>
                            <textarea name="data[note]" class="form-control" rows="5">{{ $item->note }}</textarea>
                        </div>
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
                        <label class="control-label">Tài xế</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="member_id">
                                @foreach( $members as $key => $member)
                                <option value="{{ $member->id }}" {{ $item->member_id == $member->id ? 'selected' : '' }} > {{ $member->name }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Tình trạng</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="status[]">
                                @foreach( $siteconfig[$type]['status'] as $key => $val)
                                <option value="{{ $key }}" {{ (old('status')) ? ( (in_array($key,old('status'))) ? 'selected' : '') : ($key==1)?'selected':'' }} > {{ $val }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Thứ tự</label>
                        <div>
                            <input type="text" name="priority" value="{{ (old('priority')) ? old('priority') : 1 }}" class="form-control priceFormat">
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn green"> <i class="fa fa-check"></i> Lưu</button>
                        <a href="{{ url()->previous() }}" class="btn default" > Thoát </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('custom_script')
<script src="{{ asset('packages/jquery.scannerdetection.js') }}" type="text/javascript"></script>
@php
    $orders = $item->order_code ? json_encode(explode(',',$item->order_code)) : null;
@endphp
<script type="text/javascript">
    new Vue({
        el: '#qh-app',
        data: function () {
            var orders = [];
            @if($orders)
                orders = {!! $orders !!};
            @endif
            return {
                orders: orders,
            };
        },
        mounted() {
            let vm = this;
            $(this.$el).find('.scanner').scannerDetection({
                timeBeforeScanTest: 200, // wait for the next character for upto 200ms
                avgTimeByChar: 40, // it's not a barcode if a character takes longer than 100ms
                preventDefault: true,
                endChar: [5],
                onComplete: function(barcode, qty){
                    validScan = true;
                    exist = false;
                    for (var i = 0; i < vm.orders.length; i++) {
                        if(vm.orders[i] == barcode){
                            exist = true;
                            break;
                        }
                    }
                    if( !exist ){
                        vm.orders.push(barcode);
                    }
                },
                onError: function(string, qty) {
                    // alert(string);
                }
            });
        }
    });
</script>
@endsection
