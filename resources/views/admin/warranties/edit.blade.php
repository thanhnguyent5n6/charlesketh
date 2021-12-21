@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.warranty.index', ['type'=>$type]) }}"> {{ $pageTitle }} </a>
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
    <form role="form" method="POST" action="{{ route('admin.warranty.update',['id'=>$item->id,'type'=>$type]) }}" class="form-validation">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <input type="hidden" name="redirects_to" value="{{ (old('redirects_to')) ? old('redirects_to') : url()->previous() }}" />
        <input type="hidden" name="data[order_code]" class="form-control" v-model="order.code">
        <div class="col-lg-9 col-xs-12"> 
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Thêm mới </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <select id="select2-button-addons-single-input-group-sm" class="form-control select2-data-ajax"  data-label="Tên, SĐT khách hàng hoặc mã đơn hàng" data-url="{{ route('admin.warranty.ajax',['t'=>'default']) }}"></select>
                    </div>
                    <div class="table-responsive">
                        <qh-products></qh-products>
                    </div>
                </div>
            </div>

            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Nhập Thông Tin Bảo Hành </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-barcode" aria-hidden="true"></i> Mã bảo hành</label>
                        <div style="margin-bottom: 15px;" v-for="(item, key) in warranty">
                            <input type="text" name="warranty_code[]" class="form-control" :value="item">
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
                    <div class="caption">Thông tin khách hàng </div>
                </div>
                <div class="portlet-body">

                    <div class="form-group">
                        <label class="control-label"><b>Mã đơn hàng:</b> </label>
                        @{{ order.code }}
                    </div>

                    <div class="form-group">
                        <label class="control-label"><b>Họ và tên:</b> </label>
                        @{{ order.name }}
                    </div>

                    <div class="form-group">
                        <label class="control-label"><b>Email:</b> </label>
                        @{{ order.email }}
                    </div>

                    <div class="form-group">
                        <label class="control-label"><b>Phone:</b> </label>
                        @{{ order.phone }}
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
<script type="text/x-template" id="select2-data-template">
    <table class="table table-bordered table-condensed">
        <thead>
            <tr class="text-uppercase">
                <th width="7%"> Mã SP </th>
                <th width="15%"> Tên SP </th>
                <th width="8%"> Giá bán </th>
                <th width="8%"> Giá kê </th>
                <th width="6%"> Số lượng </th>
                <th width="10%"> Thành tiền </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(item, key) in products" >
                <td align="center">@{{ item.code }}</td>
                <td>@{{ item.title }}</td>
                <td align="center">@{{ formatPrice(item.price) }} </td>
                <td align="center">@{{ formatPrice(item.price_second) }} </td>
                <td align="center">@{{ item.qty }}</td>
                <td align="center">@{{ formatPrice(subtotal[key]) }}</td>
            </tr>
            <tr>
                <td align="right" colspan="30">
                    <span class="pull-left text-uppercase">
                        Số lượng: <span class="font-red-mint font-md bold">@{{ formatPrice(totalQty) }}</span>
                    </span>
                    <span class="pull-right text-uppercase">
                        Tổng: <span class="font-red-mint font-md bold">@{{ formatPrice(total) }}</span>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</script>
<script src="{{ asset('packages/jquery.scannerdetection.js') }}" type="text/javascript"></script>
@php
    $products = $products ? json_encode($products) : null;
    $order = $order ? json_encode($order) : null;
    $warranty = $item->warranty_code ? json_encode(explode(',',$item->warranty_code)) : null;
@endphp
<script type="text/javascript">
    new Vue({
        el: '#qh-app',
        data: function () {
            var products = [];
            @if($products)
                products = {!! $products !!};
            @endif
            var order = {
                'code' : '',
                'name' : '',
                'email' : '',
                'phone' : '',
            };
            @if($order)
                order = {!! $order !!};
            @endif
            var warranty = [];
            @if($warranty)
                warranty = {!! $warranty !!};
            @endif
            return {
                products: products,
                order: order,
                warranty: warranty,
            };
        },
        computed: {
            total() {
                return this.products.reduce((total, item) => {
                    return total + (item.qty * item.price);
                }, 0);
            }
        },
        components: {
            'qh-products': {
                template: '#select2-data-template',
                data: function () {
                    return {
                        products: this.$parent.products
                    };
                },
                computed: {
                    subtotal() {
                        return this.products.map((item) => {
                            return Number( item.qty * item.price )
                        });
                    },
                    total() {
                        return this.products.reduce((total, item) => {
                            return total + (item.qty * item.price);
                        }, 0);
                    },
                    totalQty() {
                        return this.products.reduce((total, item) => {
                            return total + item.qty;
                        }, 0);
                    }
                },
                methods: {
                    deleteProduct: function (item) {
                        this.products.splice(this.products.indexOf(item) ,1);
                    },
                    formatPrice(value) {
                        let val = (value/1).toFixed(0).replace('.', ',')
                        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                    }
                    
                }
            }
        },
        methods: {
            formatPrice(value) {
                let val = (value/1).toFixed(0).replace('.', ',')
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            },
            addProduct: function () {
                var select2data = $(".select2-data-ajax").select2("data");
                for (var i = 0; i < select2data.length; i++) {
                    this.order.code = select2data[i].code;
                    this.order.name = select2data[i].name;
                    this.order.email = select2data[i].email;
                    this.order.phone = select2data[i].phone;
                    if( select2data[i]['products'].length ){
                        for (var j = 0; j < select2data[i]['products'].length; j++) {
                            this.products.push({
                                "id": select2data[i]['products'][j].id,
                                "code": select2data[i]['products'][j].code,
                                "title": select2data[i]['products'][j].title,
                                "qty": select2data[i]['products'][j].qty,
                                "price": select2data[i]['products'][j].price,
                                "price_second": select2data[i]['products'][j].price_second,
                            });
                        }
                    }
                }
            }
        },
        mounted() {
            let vm = this;
            $(this.$el).find('.select2-data-ajax').on('select2:select', function(e){
                vm.products.splice(0,vm.products.length);
                vm.addProduct();
            });
            $(this.$el).find('.scanner').scannerDetection({
                timeBeforeScanTest: 200, // wait for the next character for upto 200ms
                avgTimeByChar: 40, // it's not a barcode if a character takes longer than 100ms
                preventDefault: true,
                endChar: [5],
                onComplete: function(barcode, qty){
                    validScan = true;
                    exist = false;
                    for (var i = 0; i < vm.warranty.length; i++) {
                        if(vm.warranty[i] == barcode){
                            exist = true;
                            break;
                        }
                    }
                    if( !exist ){
                        vm.warranty.push(barcode);
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
