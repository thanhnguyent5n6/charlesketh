@extends('frontend.default.app')
@section('content')
<!-- PAGE SECTION START -->
<section class="page-section pb-4" id="qh-app">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center"> So sánh sản phẩm </h2>
                <div class="form-group">
                    <input type="text" class="form-control" v-model="code">
                </div>
                <div class="form-group text-center">
                    <button type="button" class="btn btn-primary" @click="search()">Tìm kiếm</button>
                </div>

                <table class="table" v-if="searchItems.length > 0">
                    <thead>
                        <tr>
                            <th style="width: 20%">Sản phẩm</th>
                            <th style="width: 60%">Link</th>
                            <th style="width: 20%">Giá bán</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in searchItems">
                            <td>@{{ item.name }}</td>
                            <td><a href="@{{ item.link }}" target="_bland">@{{ item.link }}</a></td>
                            <td>@{{ item.price }}</td>
                        </tr>
                    </tbody>
                </table>
                <p v-if="message != ''" style="text-align:center"> @{{ message }} </p>
            </div>
        </div>
    </div>
</section>
<!-- PAGE SECTION END --> 
@endsection

@section('custom_script')
<script src="{{ asset('js/vue.min.js') }}"></script>
<script>
    new Vue({
        el: '#qh-app',
        data(){
            return {
                code: '',
                searchItems : '',
                message: ''
            }
        },
        methods: {
            search() {
                const vm = this;
                vm.message = '';
                if(vm.code){
                    var dataAjax = 'code=' + vm.code+'&_token='+Laravel.csrfToken;
                    
                    $.ajax({
                        type: 'POST',
                        url: Laravel.baseUrl+'/so-sanh-san-pham',
                        data: dataAjax,
                        dataType: 'json',
                        beforeSend: function(){
                            vm.message = 'Đang tìm kiếm sản phẩm...';
                        }
                    }).done(function(obj){
                        if( obj.items.length > 0 ){
                            vm.searchItems = obj.items;
                            vm.message = '';
                        } else {
                            vm.message = 'Không tìm thấy sản phẩm';
                        }
                    });
                } else {
                    vm.message = 'Chưa nhập mã sản phẩm';
                }
            }
        }
    });
</script>
@endsection