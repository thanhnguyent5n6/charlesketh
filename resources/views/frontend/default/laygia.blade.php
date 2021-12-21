@extends('frontend.default.app')
@section('content')
<!-- PAGE SECTION START -->
<section class="page-section pb-4" id="qh-app">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center"> Lấy giá sản phẩm </h2>
                <form method="post" action="{{ route('frontend.urlparser.getprice') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input type="file" class="form-control" name="code">
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<!-- PAGE SECTION END --> 
@endsection