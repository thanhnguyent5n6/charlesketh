@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.order.index', ['type'=>$type]) }}"> {{ $pageTitle }} </a>
    <i class="fa fa-circle"></i>
</li>
<li>
    <span>Chỉnh sửa</span>
</li>
@endsection
@section('content')
<div class="row" id="qh-app">
    <div class="col-lg-9 col-xs-12">
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption"> Chi tiết </div>
            </div>
            <div class="portlet-body">
                <div class="table-responsive">
                    <qh-products></qh-products>
                </div>
            </div>
        </div>
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption"> Thông Tin Khách hàng </div>
            </div>
            <div class="portlet-body">
                <div class="form-group">
                    <label class="control-label"><i class="fa fa-phone" aria-hidden="true"></i> Điện thoại</label>
                    <div>
                        <input type="text" name="data[phone]" class="form-control validate[required,custom[phone],minSize[10],maxSize[10]]" value="{{ $item->phone }}" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label"><i class="fa fa-user" aria-hidden="true"></i> Họ và tên</label>
                    <div>
                        <input type="text" name="data[name]" class="form-control" value="{{ $item->name }}" disabled>
                    </div>
                    
                </div>
                <div class="form-group row">
                    <div class="col-sm-6">
                        <label class="control-label"><i class="fa fa-envelope" aria-hidden="true"></i> Email</label>
                    <div>
                        <input type="text" name="data[email]" class="form-control" value="{{ $item->email }}" disabled>
                    </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="control-label"><i class="fa fa-birthday-cake" aria-hidden="true"></i> Ngày sinh</label>
                        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                            <input type="text" class="form-control" readonly="" name="birthday" value="{{ $item->birthday }}" disabled>
                            <span class="input-group-btn">
                                <button class="btn btn-sm default" type="button">
                                    <i class="fa fa-calendar"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label"> <i class="fa fa-map-marker" aria-hidden="true"></i> Địa chỉ</label>
                    <div>
                        <input type="text" name="data[address]" class="form-control" value="{{ $item->address }}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-6">
                        <label class="control-label">Tỉnh / Thành phố</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control province" name="data[province_id]" data-group="order" data-live-search="true" title="-- Chọn Tỉnh / Thành phố --" disabled>
                                {!! $item->province_id ? '<option value="'.$item->province_id.'" selected ></option>' : '' !!}
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="control-label">Quận / Huyện</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control district" name="data[district_id]" data-group="order" data-live-search="true" title="-- Chọn Quận / Huyện --" disabled>
                                {!! $item->district_id ? '<option value="'.$item->district_id.'" selected ></option>' : '' !!}
                            </select>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-invoice"> Xuất hóa đơn (nếu có) </button>
                <div class="invoice" style=" @php if($item->invoice['company'] == ''){ echo 'display: none;'; } @endphp margin-top: 15px;">
                    <div class="form-group">
                        <label class="control-label">Tên công ty</label>
                        <div>
                            <input type="text" name="data[invoice][company]" class="form-control" value="{{ $item->invoice['company'] }}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Mã số thuế</label>
                        <div>
                            <input type="text" name="data[invoice][tax_code]" class="form-control" value="{{ $item->invoice['tax_code'] }}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Địa chỉ</label>
                        <div>
                            <input type="text" name="data[invoice][address]" class="form-control" value="{{ $item->invoice['address'] }}" disabled>
                        </div>
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
                    <label class="control-label">Mã đơn hàng</label>
                    <div>
                        <input type="text" class="form-control" value="{{ $item->code }}" disabled>
                    </div>
                </div>
                @if($item->coupon_code !== null)
                <div class="form-group">
                    <label class="control-label">Mã coupon</label>
                    <div>
                        <input type="text" class="form-control" value="{{ $item->coupon_code }}" disabled>
                    </div>
                </div>
                @endif

                <div class="form-group">
                    <label class="control-label">Giảm giá</label>
                    <div class="input-group">
                        <input type="text" name="coupon_amount" class="form-control" v-model.number="coupon_amount" disabled>
                        <span class="input-group-addon"> Đ </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Tăng giá</label>
                    <div class="input-group">
                        <input type="text" name="enhancement" class="form-control" v-model.number="enhancement" disabled>
                        <span class="input-group-addon"> Đ </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Phí vận chuyển</label>
                    <div class="input-group">
                        <input type="text" name="shipping" class="form-control" v-model.number="shipping" disabled>
                        <span class="input-group-addon"> Đ </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Tổng đơn hàng</label>
                    <div class="input-group">
                        <input type="text" name="order_price" class="form-control" v-model="formatPrice(total)" disabled>
                        <span class="input-group-addon"> Đ </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Ghi chú</label>
                    <div>
                        <textarea name="data[note]" class="form-control" rows="5">{{ $item->note }}</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Hình thức thanh toán</label>
                    <div>
                        <select class="selectpicker show-tick show-menu-arrow form-control" name="data[payment_id]" disabled>
                            @foreach( $siteconfig[$type]['site']['payment'] as $key => $val)
                            <option value="{{ $key }}" {{ ($item->payment_id == $key) ? 'selected' : '' }} > {{ $val }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Tình trạng</label>
                    <div>
                        <select class="selectpicker show-tick show-menu-arrow form-control" name="data[status_id]" disabled>
                            @foreach( $siteconfig[$type]['site']['status'] as $key => $val)
                            <option value="{{ $key }}" {{ ($item->status_id == $key) ? 'selected' : '' }} > {{ $val }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Thứ tự</label>
                    <div>
                        <input type="text" name="priority" value="{{ $item->priority }}" class="form-control priceFormat" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <a href="{{ url()->previous() }}" class="btn default" > Thoát </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_script')
<script type="text/x-template" id="select2-data-template">
    <table class="table table-bordered table-condensed">
        <thead>
            <tr class="text-uppercase">
                <th width="7%"> Mã SP </th>
                <th width="15%"> Tên SP </th>
                <th width="8%"> Giá bán {{ ($type == 'wholesale') ? 'sỉ' : '' }} </th>
                <th width="8%"> Giá kê </th>
                <th width="6%"> Số lượng </th>
                <th width="10%"> Thành tiền </th>
                <th width="3%"> Xóa </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(item, key) in products" >
                <td align="center">
                    <input type="hidden" :name="'products['+ key +'][id]'" v-model="item.id">
                    <input type="hidden" :name="'products['+ key +'][code]'" v-model="item.code">
                    <input type="hidden" :name="'products['+ key +'][title]'" v-model="item.title">
                    <input type="hidden" :name="'products['+ key +'][price]'" v-model="item.price">
                    <input type="hidden" :name="'products['+ key +'][price_second]'" v-model="item.price_second">
                    @{{ item.code }}
                </td>
                <td>
                    @{{ item.title }}
                </td>
                <td align="center"> @{{ formatPrice(item.price) }} </td>
                <td align="center"> @{{ formatPrice(item.price_second) }} </td>
                <td align="center"> <input type="text" :name="'products['+ key +'][qty]'" class="form-control text-center validate[required,min[1]]" v-model.number="item.qty"> </td>
                <td align="center"> <input type="text" class="form-control text-center" placeholder="Giảm giá" readonly > @{{ formatPrice(subtotal[key]) }}</td>
                <td align="center"> <button type="button" v-on:click="deleteProduct(item)" class="btn btn-sm btn-danger"><i class="fa fa-close"></i></button> </td>
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
@php
    $products = $products ? json_encode($products) : null;
@endphp
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-invoice').on('click', function(){
            $('.invoice').toggle();
        });
    });

    new Vue({
        el: '#qh-app',
        data: function () {
            var products = [];
            @if($products)
                products = {!! $products !!};
            @endif
            return {
                coupon_amount: {{ $item->coupon_amount }},
                enhancement: {{ $item->enhancement }},
                shipping: {{ $item->shipping }},
                products: products
            };
        },
        computed: {
            total() {
                return this.products.reduce((total, item) => {
                    return total + (item.qty * item.price);
                }, 0) + (this.shipping + this.enhancement) - this.coupon_amount;
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
                    var flag = false;
                    for (var j = 0; j < this.products.length; j++) {
                        if( this.products[j].id == select2data[i].id ){
                            flag = true;
                            break;
                        }
                    }
                    if(!flag){
                        this.products.push({
                            "id": select2data[i].id,
                            "code": select2data[i].code,
                            @if($type == 'wholesale')
                            "price": select2data[i].wholesale_price,
                            @else 
                            "price": select2data[i].regular_price,
                            @endif
                            "title": select2data[i].title,
                            "qty": 1,
                            @if($type == 'wholesale')
                            "price_second": select2data[i].wholesale_price,
                            @else 
                            "price_second": select2data[i].regular_price,
                            @endif
                        });
                    }
                }
            }
        },
        mounted() {
            let vm = this;
            $(this.$el).find('.select2-data-ajax').on('select2:select', function(e){
                vm.addProduct();
            })
        }
    });
</script>
@endsection