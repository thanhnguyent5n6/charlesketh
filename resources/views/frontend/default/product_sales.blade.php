@extends('frontend.default.app')
@section('content')
<!-- PAGE SECTION START -->
<div class="container">
    <img src="/public/images/ban-6.jpg" alt="background khuyến mãi" class="mx-auto d-block">
    <img src="/public/images/ban-5.jpg" alt="khuyen mai 2010" class="mx-auto d-block">
    <!-- <img src="/public/images/ban-4.jpg" alt="khuyen mai 2010" class="mx-auto d-block">
    <img src="/public/images/ban-3.jpg" alt="khuyen mai 2010" class="mx-auto d-block">
    <img src="/public/images/ban-2.jpg" alt="khuyen mai 2010" class="mx-auto d-block">
    <img src="/public/images/ban-1.jpg" alt="khuyen mai 2010" class="mx-auto d-block"> -->
</div>
<section class="page-section-km pb-4">
    <div class="container">
        <h2 class="py-3 text-center bg-warning font-weight-bold">CÁC SẢN PHẨM ĐANG KHUYẾN MÃI</h2>
            
        @if( @$category )
        <form class="frm-filter">
            <input type="hidden" name="category_id" value="{{ $category->id }}">
            <div class="row ">
                <div class="col-xl-9 col-12">
                    <div class="row">
                        @if(@$subCategory)
                        <div class="col-auto pr-2 pl-0" style="width: 200px">
                            <select class="selectpicker show-tick show-menu-arrow form-control" onchange="location.href=this.value">
                                <option value="{{ route('frontend.home.product',['slug'=>$category->slug]) }}">{{ $category->title }}</option>
                                @php
                                    Menu::resetMenu();
                                    Menu::setMenu($categories);
                                    echo Menu::getMenuSelectRedirect(0,$category->id,'--',0);
                                @endphp
                                
                            </select>
                        </div>
                        @endif
                        
                    </div>
                    <div class="row m-0 filter-result">
                        @forelse($products as $product)
                            {!! get_template_product($product,$type,6,'p-0') !!}
                        @empty
                        <div class="col text-center text-danger font-weight-bold py-4">Không tìm thấy sản phẩm</div>
                        @endforelse
                        @if( count($products) > 0 )
                        <div class="mt-3">{{ $products->links('frontend.default.blocks.paginate') }}</div>
                        @endif
                    </div>                    
                </div>
            </div>
        </form>
        @else
            <div class="row m-0 bg-km">
                @forelse($products as $product)
                    {!! get_template_product_sale($product,$type,6,'p-0') !!}
                @empty
                <div class="col text-center text-danger font-weight-bold py-4">Không tìm thấy sản phẩm</div>
                @endforelse
                
            </div>
            @if( count($products) > 0 )
            <div class="mt-3">{{ $products->links('frontend.default.blocks.paginate') }}</div>
            @endif
        @endif
    </div>
</section>
<!-- PAGE SECTION END -->
@endsection