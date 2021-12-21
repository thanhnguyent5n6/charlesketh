@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.pos.index', ['type'=>$type]) }}"> {{ $pageTitle }} </a>
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
    <form role="form" method="POST" action="{{ route('admin.pos.store',['type'=>$type]) }}" class="form-validation">
        {{ csrf_field() }}
        <div class="col-lg-9 col-xs-12"> 
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">CHỌN SẢN PHẨM </div>
                </div>
                <div class="portlet-body" style="background-color: #feffc3;">
                    <div class="form-group">
                        <div class="input-group select2-bootstrap-append">
                            <select id="select2-button-addons-single-input-group-sm" class="form-control select2-data-ajax"  multiple="" data-label="Tên hoặc mã sản phẩm" data-url="{{ route('admin.pos.ajax',['t'=>'san-pham']) }}">
                            </select>
                            <span class="input-group-btn"> <button v-on:click="addProduct" type="button" id="btn-select" class="btn btn-info btn-lg" style="height: 52px"> Chọn </button> </span>
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
                <div class="portlet-body" style="background: rgb(255, 242, 141);">
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-phone" aria-hidden="true"></i> Điện thoại</label>
                            <div>
                                <input type="text" name="data[phone]" class="form-control validate[required,custom[phone],minSize[10],maxSize[10]]" value="{{ old('data.phone') }}">
                            </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-user" aria-hidden="true"></i> Họ và tên</label>
                        <div>
                            <input type="text" name="data[name]" class="form-control validate[required]" value="{{ old('data.name') }}">
                        </div>
                    </div>
                                       
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-map-marker" aria-hidden="true"></i> Địa chỉ</label>
                        <div>
                            <input type="text" name="data[address]" class="form-control" value="{{ old('data.address') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="control-label">Tỉnh / Thành phố</label>
                            <div>
                                <select class="selectpicker show-tick show-menu-arrow form-control province" name="data[province_id]" data-group="order" data-live-search="true" title="-- Chọn Tỉnh / Thành phố --">
                                    {!! old('data.province_id') ? '<option value="'.old('data.province_id').'" selected ></option>' : '' !!}
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label">Quận / Huyện</label>
                            <div>
                                <select class="selectpicker show-tick show-menu-arrow form-control district" name="data[district_id]" data-group="order" data-live-search="true" title="-- Chọn Quận / Huyện --">
                                    {!! old('data.district_id') ? '<option value="'.old('data.district_id').'" selected ></option>' : '' !!}
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-clock-o" aria-hidden="true"></i> Giờ giao hàng</label>
                        <div class="input-group datetimepicker">
                            <input type="text" class="form-control validate[required]" readonly="" name="delivery_time" value="{{ old('delivery_time') }}">
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
                            <input type="text" name="data[email]" class="form-control" value="{{ old('data.email') }}">
                        </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label"><i class="fa fa-birthday-cake" aria-hidden="true"></i> Ngày sinh</label>
                            <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                <input type="text" class="form-control" readonly="" name="birthday" value="{{ old('birthday') }}">
                                <span class="input-group-btn">
                                    <button class="btn btn-sm default" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                        
                    <button type="button" class="btn btn-invoice green"> Xuất hóa đơn (nếu có) </button>
                    <div class="invoice" style="display: none; margin-top: 15px;">
                        <div class="form-group">
                            <label class="control-label">Tên công ty</label>
                            <div>
                                <input type="text" name="data[invoice][company]" class="form-control" value="{{ old('data.invoice.company') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Mã số thuế</label>
                            <div>
                                <input type="text" name="data[invoice][tax_code]" class="form-control" value="{{ old('data.invoice.tax_code') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Địa chỉ</label>
                            <div>
                                <input type="text" name="data[invoice][address]" class="form-control" value="{{ old('data.invoice.address') }}">
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
                    <p><button type="button" class="btn btn-same green"> Copy thông tin phía trên </button></p>
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-phone" aria-hidden="true"></i> Điện thoại</label>
                            <div>
                                <input type="text" name="data[delivery][phone]" class="form-control validate[required,custom[phone],minSize[10],maxSize[10]]" value="{{ old('data.phone') }}">
                            </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-user" aria-hidden="true"></i> Họ và tên</label>
                        <div>
                            <input type="text" name="data[delivery][name]" class="form-control validate[required]" value="{{ old('data.delivery.name') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-map-marker" aria-hidden="true"></i> Địa chỉ</label>
                        <div>
                            <input type="text" name="data[delivery][address]" class="form-control" value="{{ old('data.delivery.address') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="control-label">Tỉnh / Thành phố</label>
                            <div>
                                <select class="selectpicker show-tick show-menu-arrow form-control province" name="data[delivery][province_id]" data-group="delivery" data-live-search="true" title="-- Chọn Tỉnh / Thành phố --">
                                    {!! old('data.delivery.province_id') ? '<option value="'.old('data.delivery.province_id').'" selected ></option>' : '' !!}
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label">Quận / Huyện</label>
                            <div>
                                <select class="selectpicker show-tick show-menu-arrow form-control district" name="data[delivery][district_id]" data-group="delivery" data-live-search="true" title="-- Chọn Quận / Huyện --">
                                    {!! old('data.delivery.district_id') ? '<option value="'.old('data.delivery.district_id').'" selected ></option>' : '' !!}
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
                    <?php /*
                    <div class="form-group">
                        <label class="control-label">Nhân viên tư vấn</label>
                        <div>
                            <select name="saler_id" class="selectpicker show-tick show-menu-arrow form-control">
                                <option value="0">-- Chọn nhân viên --</option>
                                @forelse($sales as $saler)
                                <option value="{{ $saler->id }}" {{ (old('sales')) ? ((in_array($saler->id,old('sales'))) ? 'selected' : '') : '' }} > {{ $saler->name }} </option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    */ ?>
                    <div class="form-group">
                        <label class="control-label">Mã đơn hàng</label>
                        <div>
                            <input type="text" name="data[code]" class="form-control text-uppercase" value="" placeholder="Hệ thống tự phát sinh" readonly="">
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="control-label">Phí vận chuyển</label>
                        <div class="input-group">
                            <input type="text" name="shipping" class="form-control" value="{{ old('shipping') }}" v-model.number="shipping">
                            <span class="input-group-addon"> Đ </span>
                        </div>
                    </div>

                     <div class="form-group">
                        <label class="control-label">Giảm giá</label>
                        <div class="input-group">
                            <input type="text" name="coupon_amount" class="form-control" value="{{ old('coupon_amount') }}" v-model.number="coupon_amount">
                            <span class="input-group-addon"> Đ </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Tăng giá</label>
                        <div class="input-group">
                            <input type="text" name="enhancement" class="form-control" value="{{ old('enhancement') }}" v-model.number="enhancement" disabled>
                            <span class="input-group-addon"> Đ </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Tình trạng</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="data[status_id]">
                                @foreach( $siteconfig[$type]['site']['status'] as $key => $val)
                                <option value="{{ $key }}" {{ old('data.status_id') ? ( in_array($key,old('data.status_id')) ? 'selected' : '') : ($key==1 ? 'selected':'') }} > {{ $val }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="bg-green" style="padding: 10px;">
                        <div class="form-group">
                            <label class="control-label">Đã thanh toán</label>
                            <div class="input-group">
                                <input type="text" name="deposit_amount" class="form-control priceFormat" value="">
                                <span class="input-group-addon"> Đ </span>
                            </div>
                        </div>
                        <?php /*
                        <div class="form-group">
                            <label class="control-label">Đã thanh toán</label>
                            <div class="input-group">
                                <input type="text" name="received_amount" class="form-control priceFormat" value="">
                                <span class="input-group-addon"> Đ </span>
                            </div>
                        </div>
                        */?>

                        <div class="form-group hidden">
                            <label class="control-label">Phí lắp đặt</label>
                            <div class="input-group">
                                <input type="text" name="installation_fees" class="form-control priceFormat" value="">
                                <span class="input-group-addon"> Đ </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="mt-checkbox" style="margin-bottom: 0px;">Check (nếu có thi công lắp đặt)
                                <input type="checkbox" name="status[]" value="construction" > <span></span> </label>
                            <input type="hidden" name="status[]" value="publish" >
                        </div>
                    </div>

                    <div class="bg-info" style="padding: 10px; margin-bottom: 10px">
                        <div class="form-group">
                            <label class="control-label" style="color:red">Tổng giá trị đơn hàng</label>
                            <div class="input-group">
                                <input type="text" name="order_price" class="form-control" v-model="formatPrice(total)" disabled>
                                <span class="input-group-addon"> Đ </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Khách đưa</label>
                            <div class="input-group">
                                <input type="text" class="form-control" v-model.number="give_money">
                                <span class="input-group-addon"> Đ </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Tiền thối: </label>
                            <b style="color: #f00; font-size: 20px">@{{ formatPrice(total-give_money) }}</b>
                        </div>
                    </div>                

                    <?php /*
                    <div class="form-group">
                        <label class="control-label">Yêu cầu thu cọc</label>
                        <div class="input-group">
                            <input type="text" name="deposit_note" class="form-control priceFormat" value="">
                            <span class="input-group-addon"> Đ </span>
                        </div>
                    </div>
                    */ ?>
                    
                    <div class="form-group">
                        <label class="control-label">Ghi chú</label>
                        <div>
                            <textarea name="data[note]" class="form-control" rows="5">{{ old('data.note') }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Hình thức thanh toán</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="data[payment_id]">
                                @foreach( $siteconfig[$type]['site']['payment'] as $key => $val)
                                <option value="{{ $key }}" {{ old('data.payment_id') ? ( in_array($key,old('data.payment_id')) ? 'selected' : '') : ($key==1 ? 'selected':'') }} > {{ $val }} </option>
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
                        <button type="submit" class="btn green submit-fix-mobile"> <i class="fa fa-check"></i> Hoàn Tất</button>
                        <a href="{{ url()->previous() }}" class="btn default" > Thoát </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('custom_css')
<style>
@media (max-width: 767.98px) {
    .submit-fix-mobile{
        position: fixed;
        display: block;
        bottom: 0px;
        left: 0px;
    }
}
</style>
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
                <td align="center"> <div> @{{ formatPrice(item.price) }} </div> + <input type="text" :name="'products['+ key +'][price_virtual]'" class="form-control text-center" v-model.formatPrice="item.price_virtual"></td>
                <td align="center"> <input type="text" :name="'products['+ key +'][price_second]'" class="form-control text-center validate[required,min[1]]" v-model.formatPrice="item.price_second"> </td>
                <td align="center"> <input type="text" :name="'products['+ key +'][qty]'" class="form-control text-center validate[required,min[1]]" v-model.number="item.qty"> </td>
                <td align="center"> @{{ formatPrice(subtotal[key]) }}</td>
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
        $('.btn-same').on('click', function(e){
            e.preventDefault();
            $('input[name="data[delivery][phone]"]').val($('input[name="data[phone]"]').val());
            $('input[name="data[delivery][name]"]').val($('input[name="data[name]"]').val());
            $('input[name="data[delivery][address]"]').val($('input[name="data[address]"]').val());
            $('select[name="data[delivery][province_id]"]').selectpicker('val',$('select[name="data[province_id]"]').val());
            $('select[name="data[delivery][district_id]"]').selectpicker('val',$('select[name="data[district_id]"]').val());
        });

        $('.btn-invoice').on('click', function(e){
            e.preventDefault();
            $('.invoice').toggle();
        });
        $('input[name="data[phone]"]').on('blur', function(e){
            e.preventDefault();
            var btn = $(this);
            var dataAjax = 'phone=' + btn.val() + '&type={{ $type }}';
            $.ajax({
                type: 'GET',
                url : Laravel.baseUrl+'/admin/orders/customer',
                data: dataAjax,
                dataType: 'json'
            }).done(function(obj){
                var item = obj.data;
                $('input[name="data[name]"]').val(item.name);
                $('input[name="data[email]"]').val(item.email);
                $('input[name="data[address]"]').val(item.address);
                $('input[name="birthday"]').val(item.birthday);
                $('select[name="data[province_id]"]').selectpicker('val',item.province_id);
                $('select[name="data[district_id]"]').selectpicker('val',item.district_id);
            });
        });
    });
    new Vue({
        el: '#qh-app',
        data: function () {
            var products = [];
            return {
                coupon_amount: 0,
                enhancement: 0,
                shipping: 0,
                give_money: 0,
                products: products
            };
        },
        computed: {
            total() {
                return this.products.reduce((total, item) => {
                    return total + (item.qty * item.price);
                }, 0) + (this.shipping + this.enhancement) - (this.coupon_amount);
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
                    var price = 0;
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
                            "price_virtual": 0,
                            "price": price,
                            "price_second": price,
                            "price_discount": 0
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