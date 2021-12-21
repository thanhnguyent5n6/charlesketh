@extends('frontend.default.app')
@section('content')
<!-- PAGE SECTION START -->
<section class="page-section pb-4">
    <div class="container">
        @if( @$category )
        <form class="frm-filter">
            <input type="hidden" name="category_id" value="{{ $category->id }}">
            <div class="row">
                <div class="col-xl-3 col-12 pr-xl-0 mb-3 mb-xl-0">
                	
                	
                	<?php /*
                    @include('frontend.default.layouts.sidebar')

                    <input type="checkbox" label="search-bottom">
                	<label for="search-bottom">
                		<p>Bộ Lọc Sản Phẩm</p>
                		<div class=" search-cate">
                   			
                   		</div>
                    </label> */ ?>
                </div>
                <div class="col-xl-12 col-12">
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
                <div class="col-auto pr-2" style="width: 175px">
                    <select name="order_by" class="selectpicker show-tick show-menu-arrow form-control">
                        <option value="">Sắp xếp theo</option>
                        <option value="1">Tên: A-Z</option>
                        <option value="2">Tên: Z-A</option>
                        <option value="3">Giá: Thấp - Cao</option>
                        <option value="4">Giá: Cao - Thấp</option>
                        <option value="5">Xem nhiều nhất</option>
                        <option value="6">Mới nhất</option>
                    </select>
                </div>
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
            <div class="row m-0">
                @forelse($products as $product)
                    {!! get_template_product($product,$type,6,'p-0') !!}
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