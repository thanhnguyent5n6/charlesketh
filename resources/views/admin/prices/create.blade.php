@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.price.index') }}"> Phiếu bảng giá </a>
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
    <form role="form" method="POST" action="{{ route('admin.price.store') }}" class="form-validation">
        {{ csrf_field() }}
        <div class="col-lg-9 col-xs-12"> 
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> <b>Chọn Sản Phẩm </b></div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <div class="input-group select2-bootstrap-append">
                            <select id="select2-button-addons-single-input-group-sm" class="form-control select2-data-ajax"  multiple="" data-label="Tên hoặc mã sản phẩm" data-url="{{ route('admin.price.ajax',['t'=>'san-pham']) }}">
                            </select>
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
                            <input type="text" name="data[phone]" class="form-control" value="{{ old('data.phone') }}" placeholder="0931.......">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-user" aria-hidden="true"></i> Họ và tên</label>
                        <div>
                            <input type="text" name="data[name]" class="form-control" value="{{ old('data.name') }}" placeholder="Nguyễn Văn A">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-map-marker" aria-hidden="true"></i> Địa chỉ</label>
                        <div>
                            <input type="text" name="data[address]" class="form-control" value="{{ old('data.address') }}" placeholder="Số 123 Trường Chinh.....">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><i class="fa fa-user" aria-hidden="true"></i> Email</label>
                        <div>
                            <input type="text" name="data[email]" class="form-control" value="{{ old('data.email') }}" placeholder="company@gmail.com">
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
                        <label class="control-label">Mã phiếu</label>
                        <div>
                            <input type="text" name="data[code]" class="form-control text-uppercase" value="" placeholder="Hệ thống tự phát sinh" readonly="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Ghi chú</label>
                        <div>
                            <textarea name="data[note]" class="form-control" rows="5">{{ old('data.note') }}</textarea>
                        </div>
                    </div>

                    <div class="form-group ">
                        <label class="control-label">Thứ tự</label>
                        <div>
                            <input type="text" name="priority" value="{{ (old('priority')) ? old('priority') : 1 }}" class="form-control priceFormat">
                        </div>
                    </div>
                    <div class="form-group finish_order">
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
                <th width="8%"> Giá bán</th>
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
                    @{{ item.code }}
                </td>
                <td>
                    @{{ item.title }}
                </td>
                <td align="center">
                    <input type="text" :name="'products['+ key +'][price]'" placeholder="Giá bán" class="validate[required] form-control text-center" v-model.formatPrice="item.price">
                </td>
                <td align="center"><input type="text" :name="'products['+ key +'][qty]'" class="form-control text-center validate[required]" v-model.number="item.qty"> </td>
                <td align="center"><div><b style="color:green;font-size: 14px">@{{ formatPrice(subtotal[key]) }} </b></div></td>
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
    new Vue({
        el: '#qh-app',
        data: function () {
            return {
                products: []
            };
        },
        computed: {
            total() {
                return this.products.reduce((total, item) => {
                    return total + (item.qty * parseInt(item.price));
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
                            return Number(item.qty * parseInt(item.price))
                        });
                    },
                    total() {
                        return this.products.reduce((total, item) => {
                            return total + (item.qty * parseInt(item.price));
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
            },
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
                            price = select2data[i].regular_price
                        }
                        this.products.push({
                            "id": select2data[i].id,
                            "code": select2data[i].code,
                            "title": select2data[i].title,
                            "qty": 1,
                            "price": price,
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