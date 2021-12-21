@extends('admin.app')
@section('breadcrumb')
<li>
    <a href="{{ route('admin.wms_import.index', ['type'=>$type]) }}"> {{ $pageTitle }} </a>
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
    <form role="form" method="POST" action="{{ route('admin.wms_import.store',['type'=>$type]) }}" class="form-validation">
        {{ csrf_field() }}

        <div class="col-lg-9 col-xs-12" id="qh-app">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption"> Thêm mới </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group">
                        <div class="input-group select2-bootstrap-append">
                            <select id="select2-button-addons-single-input-group-sm" class="form-control select2-data-ajax"  multiple="" data-label="Tên hoặc mã sản phẩm" data-url="{{ route('admin.product.ajax',['t'=>'san-pham']) }}">
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
                        <label class="control-label"> Kho hàng </label>
                        <div>
                            <select name="data[store_code]" class="selectpicker show-tick show-menu-arrow form-control validate[required]">
                                <option value=""> -- Chọn kho hàng -- </option>
                                @forelse($warehouses as $warehouse)
                                <option value="{{ $warehouse->code }}" {{ (old('store_code')) ? (($warehouse->code == old('store_code')) ? 'selected' : '') : '' }} > {{ $warehouse->name }} </option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"> Nhà cung cấp </label>
                        <div>
                            <select name="data[supplier_id]" class="selectpicker show-tick show-menu-arrow form-control" data-live-search="true">
                                <option value=""> -- Chọn nhà cung cấp -- </option>
                                @forelse($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ (old('supplier_id')) ? (($supplier->id == old('supplier_id')) ? 'selected' : '') : '' }} > {{ $supplier->name }} </option>
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
                        <label class="control-label">Hình thức thanh toán</label>
                        <div>
                            <select class="selectpicker show-tick show-menu-arrow form-control" name="data[payment_id]">
                                @foreach( $siteconfig[$type]['payment'] as $key => $val)
                                <option value="{{ $key }}" {{ (old('data.payment_id')) ? ( (in_array($key,old('data.payment_id'))) ? 'selected' : '') : ($key==1)?'selected':'' }} > {{ $val }} </option>
                                @endforeach
                            </select>
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
            <tr class="text-uppercase">
                <th width="6%"> Mã SP </th>
                <th width="15%"> Tên SP </th>
                <th width="9%"> Đơn vị tính </th>
                <th width="8%"> Đơn giá </th>
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
                    <input type="hidden" :name="'products['+ key +'][unit]'" v-model="item.unit">
                    @{{ item.code }}
                </td>
                <td>
                    @{{ item.title }}
                </td>
                <td>
                	<span v-if="item.unit === 1">Bộ/Cái</span>
                	<span v-else-if="item.unit === 2">Dàn Nóng</span>
                	<span v-else-if="item.unit === 3">Dàn Lạnh</span>
                </td>
                <td align="center"> <input type="text" :name="'products['+ key +'][price]'" class="form-control text-center validate[required,min[1]]" v-model.number="item.price"> </td>
                <td align="center"> <input type="text" :name="'products['+ key +'][qty]'" class="form-control text-center validate[required,min[1]]" v-model.number="item.qty"> </td>
                <td align="center">@{{ formatPrice(subtotal[key]) }}</td>
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
            addProduct: function () {
                var select2data = $(".select2-data-ajax").select2("data");
                for (var i = 0; i < select2data.length; i++) {
                	if(select2data[i].category_id === 12){
                		this.products.push({
	                        "id": select2data[i].id,
	                        "code": select2data[i].code,
	                        "price": select2data[i].original_price,
	                        "title": select2data[i].title,
	                        "qty": 1,
	                        "unit": 2
	                    });
	                    this.products.push({
	                        "id": select2data[i].id,
	                        "code": select2data[i].code,
	                        "price": select2data[i].original_price,
	                        "title": select2data[i].title,
	                        "qty": 1,
	                        "unit": 3
	                    });
                	} else {
                		this.products.push({
	                        "id": select2data[i].id,
	                        "code": select2data[i].code,
	                        "price": select2data[i].original_price,
	                        "title": select2data[i].title,
	                        "qty": 1,
	                        "unit": 1
	                    });
                	}
                    
                }
            }
        }
    });
</script>
@endsection