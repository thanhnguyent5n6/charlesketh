@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.wms_transfer.index', ['type'=>$type]) }}"> {{ $pageTitle }} </a>
    <i class="fa fa-circle"></i>
</li>
<li>
    <span> Thêm mới </span>
</li>
@endsection
@section('content')
<div class="row">
    @include('admin.blocks.messages')
    <!-- BEGIN FORM-->
    <form role="form" method="POST" action="{{ route('admin.wms_transfer.store',['type'=>$type]) }}" class="form-validation">
        {{ csrf_field() }}

        <div class="col-lg-9 col-xs-12" id="qh-app">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Thêm mới </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <div class="input-group select2-bootstrap-append">
                            <select id="select2-button-addons-single-input-group-sm" class="form-control select2-data-ajax"  multiple="" data-label="Mã sản phẩm" data-url="{{ route('admin.wms_transfer.ajax',['type'=>'default']) }}">
                            </select>
                            <span class="input-group-btn"> <button v-on:click="addProduct" type="button" id="btn-select" class="btn btn-info"> Chọn </button> </span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <qh-products></qh-products>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-xs-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Thông tin chung </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label class="control-label"> Kho từ </label>
                        <div>
                            <select name="data[store_from]" class="selectpicker show-tick show-menu-arrow form-control">
                                @forelse($warehouses as $warehouse)
                                <option value="{{ $warehouse->code }}" {{ Request::query('store_from') == $warehouse->code ? 'selected' : '' }} > {{ $warehouse->name }} </option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"> Kho đến </label>
                        <div>
                            <select name="data[store_to]" class="selectpicker show-tick show-menu-arrow form-control">
                                <option value="">-- Chọn kho hàng đến --</option>
                                @forelse($warehouses as $warehouse)
                                @if( $warehouse->code == Request::query('store_from') ) continue; @endif
                                <option value="{{ $warehouse->code }}" {{ Request::query('store_to') == $warehouse->code ? 'selected' : '' }} > {{ $warehouse->name }} </option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Mã Phiếu</label>
                        <div>
                            <input type="text" name="data[code]" class="form-control" value="" placeholder="Hệ thống tự phát sinh" readonly="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Tình trạng</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="status[]">
                                @foreach($siteconfig[$type]['status'] as $key => $val)
                                <option value="{{ $key }}" {{ (old('status')) ? ( (in_array($key,old('status'))) ? 'selected' : '') : ($key=='publish')?'selected':'' }} > {{ $val }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Thứ tự</label>
                        <div>
                            <input type="text" name="priority" class="form-control priceFormat" value="{{ (old('priority')) ? old('priority') : 1 }}">
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
            <tr>
                <th width="7%"> Mã SP </th>
                <th width="15%"> Tên SP </th>
                <th width="9%"> Đơn vị tính </th>
                <th width="8%"> Đơn giá </th>
                <th width="6%"> Số lượng </th>
                <th width="10%"> Thành tiền </th>
                <th width="8%"> Tồn kho </th>
                <th width="3%"> Xóa </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(item, key) in products" >
                <td align="center">
                    <input type="hidden" :name="'products['+ key +'][id]'" v-model="item.product_id">
                    <input type="hidden" :name="'products['+ key +'][code]'" v-model="item.product_code">
                    <input type="hidden" :name="'products['+ key +'][title]'" v-model="item.product_title">
                    <input type="hidden" :name="'products['+ key +'][price]'" v-model="item.product_price">
                    <input type="hidden" :name="'products['+ key +'][unit]'" v-model="item.unit">
                    @{{ item.product_code }}
                </td>
                <td>@{{ item.product_title }}</td>
                <td align="center">
                    <span v-if="item.unit === 1">Bộ/Cái</span>
                    <span v-else-if="item.unit === 2">Dàn Nóng</span>
                    <span v-else-if="item.unit === 3">Dàn Lạnh</span>
                </td>
                <td align="center"> @{{ formatPrice(item.product_price) }} </td>
                <td align="center"> <input type="text" :name="'products['+ key +'][qty]'" :class="'form-control text-center validate[required,min[1],max[' + item.inventory + ']]'" v-model.number="item.product_qty"> </td>
                <td align="center"> <span> @{{ formatPrice(subtotal[key]) }} </span> </td>
                <td align="center"> <span> @{{ item.inventory }} </span> </td>
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
        $('select[name="data[store_from]"]').on('change', function(){
            location.href = window.Laravel.baseUrl + '/admin/wms_transfers/create?type=default&store_from=' + $(this).val();
        })
    })
    new Vue({
        el: '#qh-app',
        data: function () {
            var products = [];
            return {
                products: products
            };
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
                            return Number( item.product_qty * item.product_price )
                        });
                    },
                    total() {
                        return this.products.reduce((total, item) => {
                            return total + (item.product_qty * item.product_price);
                        }, 0);
                    },
                    totalQty() {
                        return this.products.reduce((total, item) => {
                            return total + item.product_qty;
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
            addProduct: function () {
                var select2data = $(".select2-data-ajax").select2("data");
                for (var i = 0; i < select2data.length; i++) {
                    var flag = false;
                    for (var j = 0; j < this.products.length; j++) {
                        if( this.products[j].product_id == select2data[i].product_id && this.products[j].unit == select2data[i].unit ){
                            flag = true;
                            break;
                        }
                    }
                    if(!flag){
                        this.products.push({
                            "id": select2data[i].id,
                            "product_id": select2data[i].product_id,
                            "product_code": select2data[i].product_code,
                            "product_price": select2data[i].product_price,
                            "product_title": select2data[i].title,
                            "product_qty": select2data[i].product_qty,
                            "unit": select2data[i].unit,
                            "inventory": select2data[i].inventory
                        });
                    }
                }
            }
        }
    });

</script>
@endsection