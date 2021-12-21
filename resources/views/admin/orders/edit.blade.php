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
    @include('admin.blocks.messages')
    <!-- BEGIN FORM-->
    <form role="form" method="POST" action="{{ route('admin.order.update',['id'=>$item->id,'type'=>$type]) }}" class="form-validation">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <input type="hidden" name="redirects_to" value="{{ (old('redirects_to')) ? old('redirects_to') : url()->previous() }}" />
        <div class="col-lg-9 col-xs-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Chỉnh sửa </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <div class="input-group select2-bootstrap-append">
                            <select id="select2-button-addons-single-input-group-sm" class="form-control select2-data-ajax"  multiple="" data-label="Tên hoặc mã sản phẩm" data-url="{{ route('admin.order.ajax',['t'=>'san-pham']) }}">
                            </select>
                            <span class="input-group-btn"> <button v-on:click="addProduct" type="button" id="btn-select" class="btn btn-info"> Chọn </button> </span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <qh-products></qh-products>
                    </div>
                </div>
            </div>
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Nhập Thông Tin Khách hàng </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-phone" aria-hidden="true"></i> Điện thoại</label>
                        <div>
                            <input type="text" name="data[phone]" class="form-control validate[required,custom[phone],minSize[10],maxSize[10]]" value="{{ $item->phone }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-user" aria-hidden="true"></i> Họ và tên</label>
                        <div>
                            <input type="text" name="data[name]" class="form-control" value="{{ $item->name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-user" aria-hidden="true"></i> Giờ giao hàng</label>
                        <div class="input-group datetimepicker">
                            <input type="text" class="form-control" readonly="" name="delivery_time" value="{{ $item->delivery_time }}">
                            <span class="input-group-btn">
                                <button class="btn btn-sm default" type="button">
                                    <i class="fa fa-calendar"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="control-label"><i class="fa fa-envelope" aria-hidden="true"></i> Email</label>
                        <div>
                            <input type="text" name="data[email]" class="form-control" value="{{ $item->email }}">
                        </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label"><i class="fa fa-birthday-cake" aria-hidden="true"></i> Ngày sinh</label>
                            <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                <input type="text" class="form-control" readonly="" name="birthday" value="{{ $item->birthday }}">
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
                            <input type="text" name="data[address]" class="form-control" value="{{ $item->address }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="control-label">Tỉnh / Thành phố</label>
                            <div>
                                <select class="selectpicker show-tick show-menu-arrow form-control province" name="data[province_id]" data-group="order" data-live-search="true" title="-- Chọn Tỉnh / Thành phố --">
                                    {!! $item->province_id ? '<option value="'.$item->province_id.'" selected ></option>' : '' !!}
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label">Quận / Huyện</label>
                            <div>
                                <select class="selectpicker show-tick show-menu-arrow form-control district" name="data[district_id]" data-group="order" data-live-search="true" title="-- Chọn Quận / Huyện --">
                                    {!! $item->district_id ? '<option value="'.$item->district_id.'" selected ></option>' : '' !!}
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-invoice"> Xuất hóa đơn (nếu có) </button>
                    <div class="invoice" style=" @php if(@$item->invoice['company'] == ''){ echo 'display: none;'; } @endphp margin-top: 15px;">
                        <div class="form-group">
                            <label class="control-label">Tên công ty</label>
                            <div>
                                <input type="text" name="data[invoice][company]" class="form-control" value="{{ @$item->invoice['company'] }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Mã số thuế</label>
                            <div>
                                <input type="text" name="data[invoice][tax_code]" class="form-control" value="{{ @$item->invoice['tax_code'] }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Địa chỉ</label>
                            <div>
                                <input type="text" name="data[invoice][address]" class="form-control" value="{{ @$item->invoice['address'] }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Thông Tin Người Nhận hàng </div>
                </div>
                <div class="portlet-body">
                    <p><button type="button" class="btn btn-same"> Copy thông tin khách hàng phía trên </button></p>
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-phone" aria-hidden="true"></i> Điện thoại</label>
                            <div>
                                <input type="text" name="data[delivery][phone]" class="form-control validate[required,custom[phone],minSize[10],maxSize[10]]" value="{{ @$item->delivery['phone'] }}">
                            </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-user" aria-hidden="true"></i> Họ và tên</label>
                        <div>
                            <input type="text" name="data[delivery][name]" class="form-control" value="{{ @$item->delivery['name'] }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-map-marker" aria-hidden="true"></i> Địa chỉ</label>
                        <div>
                            <input type="text" name="data[delivery][address]" class="form-control" value="{{ @$item->delivery['address'] }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="control-label">Tỉnh / Thành phố</label>
                            <div>
                                <select class="selectpicker show-tick show-menu-arrow form-control province" name="data[delivery][province_id]" data-group="delivery" data-live-search="true" title="-- Chọn Tỉnh / Thành phố --">
                                    {!! @$item->delivery['province_id'] ? '<option value="'.$item->delivery['province_id'].'" selected ></option>' : '' !!}
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label">Quận / Huyện</label>
                            <div>
                                <select class="selectpicker show-tick show-menu-arrow form-control district" name="data[delivery][district_id]" data-group="delivery" data-live-search="true" title="-- Chọn Quận / Huyện --">
                                    {!! @$item->delivery['district_id'] ? '<option value="'.$item->delivery['district_id'].'" selected ></option>' : '' !!}
                                </select>
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
                    @if( $type == 'shop' )
                    <div class="form-group">
                        <label class="control-label">Nhân viên tư vấn</label>
                        <div>
                            <select name="saler_id" class="selectpicker show-tick show-menu-arrow form-control">
                                <option value="0">-- Chọn nhân viên --</option>
                                @forelse($sales as $saler)
                                <option value="{{ $saler->id }}" {{ ($item->saler_id == $saler->id) ? 'selected' : '' }} > {{ $saler->name }} </option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    @endif
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
                            <input type="text" name="coupon_amount" class="form-control" v-model.number="coupon_amount">
                            <span class="input-group-addon"> Đ </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Phụ Phí</label>
                        <div class="input-group">
                            <input type="text" name="enhancement" class="form-control" v-model.number="enhancement">
                            <span class="input-group-addon"> Đ </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Phí vận chuyển</label>
                        <div class="input-group">
                            <input type="text" name="shipping" class="form-control" v-model.number="shipping">
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
                        <label class="control-label">Yêu cầu thu cọc</label>
                        <div class="input-group">
                            <input type="text" name="deposit_note" class="form-control priceFormat" value="{{ $item->deposit_note }}">
                            <span class="input-group-addon"> Đ </span>
                        </div>
                    </div>
                    <div class="bg-green" style="padding: 10px;">
                        <div class="form-group">
                            <label class="control-label">Đã thu cọc</label>
                            <div class="input-group">
                                <input type="text" name="deposit_amount" class="form-control priceFormat" value="{{ $item->deposit_amount }}">
                                <span class="input-group-addon"> Đ </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label">Đã thanh toán</label>
                            <div class="input-group">
                                <input type="text" name="received_amount" class="form-control priceFormat" value="{{ $item->received_amount }}">
                                <span class="input-group-addon"> Đ </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Phí lắp đặt</label>
                            <div class="input-group">
                                <input type="text" name="installation_fees" class="form-control priceFormat" value="{{ $item->installation_fees }}">
                                <span class="input-group-addon"> Đ </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group bg-green" style="padding: 10px;">
                        <label class="mt-checkbox" style="margin-bottom: 0px;">Thi công lắp đặt
                            <input type="checkbox" name="status[]" value="construction" > <span></span> </label>
                        <input type="hidden" name="status[]" value="publish" >
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
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="data[payment_id]">
                                @foreach( $siteconfig[$type]['site']['payment'] as $key => $val)
                                <option value="{{ $key }}" {{ ($item->payment_id == $key) ? 'selected' : '' }} > {{ $val }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Tình trạng</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="data[status_id]">
                                @foreach( $siteconfig[$type]['site']['status'] as $key => $val)
                                <option value="{{ $key }}" {{ ($item->status_id == $key) ? 'selected' : '' }} > {{ $val }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Thứ tự</label>
                        <div>
                            <input type="text" name="priority" value="{{ $item->priority }}" class="form-control priceFormat">
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
                    @{{ item.code }}
                </td>
                <td>
                    @{{ item.title }}
                </td>
                <td align="center"> <div> @{{ formatPrice(item.price) }} </div> + <input type="text" :name="'products['+ key +'][price_virtual]'" class="form-control text-center" v-model.formatPrice="item.price_virtual" placeholder="Giá ảo"> </td>
                <td align="center"> <input type="text" :name="'products['+ key +'][price_second]'" class="form-control text-center validate[required,min[1]]" v-model.formatPrice="item.price_second" placeholder="Giá kê"> </td>
                <td align="center"> <input type="text" :name="'products['+ key +'][qty]'" class="form-control text-center validate[required,min[1]]" v-model.number="item.qty"> </td>
                <td align="center">@{{ formatPrice(subtotal[key]) }}<input type="text" :name="'products['+ key +'][price_discount]'" class="form-control text-center" v-model.formatPrice="item.price_discount" placeholder="Giảm giá"></td>
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
        $(function () {
            var bindDatePicker = function() {
            $(".datetimepicker").datetimepicker({
            format:'YYYY-MM-DD HH:mm:ss',
            icons: {
            time: "fa fa-clock-o",
            date: "fa fa-calendar",
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down"
            }
            }).find('input:first').on("blur",function () {
            // check if the date is correct. We can accept dd-mm-yyyy and yyyy-mm-dd.
            // update the format if it's yyyy-mm-dd
            var date = parseDate($(this).val());

            if (! isValidDate(date)) {
                //create date based on momentjs (we have that)
                date = moment().format('YYYY-MM-DD HH:mm:ss');
            }

            $(this).val(date);
            });
            }

            var isValidDate = function(value, format) {
            format = format || false;
            // lets parse the date to the best of our knowledge
            if (format) {
            value = parseDate(value);
            }

            var timestamp = Date.parse(value);

            return isNaN(timestamp) == false;
            }

            var parseDate = function(value) {
            var m = value.match(/^(\d{1,2})(\/|-)?(\d{1,2})(\/|-)?(\d{4})$/);
            if (m)
            value = m[5] + '-' + ("00" + m[3]).slice(-2) + '-' + ("00" + m[1]).slice(-2);

            return value;
            }

            bindDatePicker();
        });
        $('.btn-invoice').on('click', function(){
            $('.invoice').toggle();
        });
        $('.btn-same').on('click', function(e){
            e.preventDefault();
            $('input[name="data[delivery][phone]"]').val($('input[name="data[phone]"]').val());
            $('input[name="data[delivery][name]"]').val($('input[name="data[name]"]').val());
            $('input[name="data[delivery][address]"]').val($('input[name="data[address]"]').val());
            $('select[name="data[delivery][province_id]"]').selectpicker('val',$('select[name="data[province_id]"]').val());
            $('select[name="data[delivery][district_id]"]').selectpicker('val',$('select[name="data[district_id]"]').val());
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
                    return total + (item.qty * (item.price-item.price_discount));
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
                            return total + (item.qty * (item.price-item.price_discount));
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
                        if(select2data[i].sale_price){
                            price = select2data[i].sale_price
                        } else {
                            @if($type == 'shop')
                            price = select2data[i].regular_price
                            @else
                            price = select2data[i].wholesale_price
                            @endif
                        }
                        
                        this.products.push({
                            "id": select2data[i].id,
                            "code": select2data[i].code,
                            "title": select2data[i].title,
                            "qty": 1,
                            "price_virtual": select2data[i].price_virtual,
                            "price_discount": select2data[i].price_discount,
                            "price": price,
                            "price_second": price
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