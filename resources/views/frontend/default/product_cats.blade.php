@extends('frontend.default.app')
@section('content')
<!-- PAGE SECTION START -->
<section class="page-section pb-4">
    <div class="container">
    	
    	<div class="row">
            <div class="col-md-6 col-ms-12 mb-3">
                <a href="/samsung-galaxy-a70-128g" title="" target="_blank" rel="nofollow"><img src="/public/images/banner-sam-sung-a70.jpg"> </a>
            </div>  
            <div class="col-md-6 col-ms-12 mb-3">
                <a href="/oppo-f11-pro-128gb" title="" target="_blank" rel="nofollow"><img src="/public/images/oppo-11-pro.jpg"> </a>
            </div>   
        </div>
        
        @if( @$category )
        <form class="frm-filter" >
            <input type="hidden" name="category_id" value="{{ $category->id }}">
            <div class="row ">
                <div class="col-12">
                    <div class="sidebar hidden">
                    <div id="show-search">
                    <div>
                        <div class="content">                          
                            <div class="row mb-3">
                                @forelse(@$suppliers as $key => $val)
                                <div class="col-2 phone_cate">
                                    <div class="custom-control custom-radio py-1">
                                        <input type="radio" class="custom-control-input" name="supplier[]" value="{{ $val->id }}" id="supplier{{ $val->id }}" {{ @$supplier && $val->id == $supplier->id ? 'checked' : '' }} @php if(!@$supplier) echo 'onclick="location.href=\''.route('frontend.home.product',['slug'=>$category->slug.'-'.$val->slug]).'\'"'; @endphp>
                                        <label class="custom-control-label custom-control-supplier" for="supplier{{ $val->id }}"><img src="{{ ( $val->image && file_exists(public_path('/uploads/photos/'.$val->image)) ? asset( 'public/uploads/photos/'.$val->image ) : asset('noimage/300x100') ) }}" alt="{{ $val->alt }}" /></label>
                                    </div>
                                </div>
                                @if( ($key+1) == 10 )
                                    <div class="collapse w-100" id="collapseSupplier">
                                    @endif

                                    @if( ($key+1) == count($suppliers) && ($key+1) >=10 )
                                    </div>
                                    <div class="col-12"><p class="text-uppercase mt-2 bg-light p-1 border"> <a href="#collapseSupplier" class="btn btn-sm btn-block font-weight-bold" data-toggle="collapse">Xem thêm các hãng khác <i class="fa fa-angle-down" aria-hidden="true"></i></a> </p></div>
                                    @endif
                                @empty
                                @endforelse
                            </div>
                            {{--
                            @forelse($suppliers as $key => $val)
                            <p class="bg-white p-0 mb-1 border text-center" data-key="{{ $key }}">
                                <a href="{{ route('frontend.home.product',['slug'=>$category->slug.'-'.$val->slug]) }}" class="d-block"><img src="{{ ( $val->image && file_exists(public_path('/uploads/photos/'.$val->image)) ? asset( 'public/uploads/photos/'.$val->image ) : asset('noimage/300x100') ) }}" alt="{{ $val->alt }}" class="img-fluid" /></a>
                            </p>
                                @if( ($key+1) == 10 )
                                <div class="collapse" id="collapseSupplier">
                                @endif

                                @if( ($key+1) == count($suppliers) && ($key+1) >=10 )
                                </div>
                                <p class="text-uppercase mt-2 bg-light p-1 border"> <a href="#collapseSupplier" class="btn btn-sm btn-block font-weight-bold" data-toggle="collapse">Xem thêm các hãng khác <i class="fa fa-angle-down" aria-hidden="true"></i></a> </p>
                                @endif

                            @empty
                            @endforelse
                            --}}
                        </div>
                    </div>

                    <div class="sidebar-widget-phone">
                        <h4 class="title">{{ __('Chọn giá từ') }}</h4>
                        <div class="content">
                            <div class="row" style="font-size: 13px">

                                <div class="col-12">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="price" value="1" id="customCheck1">
                                        <label class="custom-control-label" for="customCheck1">Dưới <b>2</b> triệu</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="price" value="2" id="customCheck2">
                                        <label class="custom-control-label" for="customCheck2">Từ <b>2 - 4</b> triệu</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="price" value="3" id="customCheck3">
                                        <label class="custom-control-label" for="customCheck3">Từ <b>4 - 7</b> triệu</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="price" value="4" id="customCheck4">
                                        <label class="custom-control-label" for="customCheck4">Từ <b>7 - 12</b> triệu</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="price" value="5" id="customCheck5">
                                        <label class="custom-control-label" for="customCheck5">Từ <b>12 - 25</b> triệu</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="price" value="6" id="customCheck6">
                                        <label class="custom-control-label" for="customCheck6">Từ <b>25 - 50</b> triệu</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="price" value="7" id="customCheck7">
                                        <label class="custom-control-label" for="customCheck7">Từ <b>50 - 75</b> triệu</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="price" value="8" id="customCheck8">
                                        <label class="custom-control-label" for="customCheck8">Trên <b>75</b> triệu</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if( count(@$filters) > 0 )
                    @forelse(@$filters[0] as $filter)
                    <div class="sidebar-widget-phone">
                        <h4 class="title">{{ $filter->title }}</h4>
                        <div class="content">
                            <div class="row">
                                @if( $filter->image )
                                <div class="col-12"> <img src="{{ asset('uploads/categories/'.$filter->image) }}" class="img-fuild"> </div>
                                @endif
                                @if( isset($filters[$filter->id]) && $filter->id != 13 )
                                @forelse($filters[$filter->id] as $key => $val)
                                <div class="col-12 ">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="filter[{{ $filter->id }}][]" value="{{ $val->id }}" id="customCheck{{ $val->id }}">
                                        <label class="custom-control-label" for="customCheck{{ $val->id }}">{{ $val->title }}</label>
                                    </div>
                                </div>
                                @empty
                                @endforelse
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    @endforelse
                    @endif
                    </div>
                    <button id="fast-search">Bộ Lọc Tìm Kiếm Nhanh</button>
                </div>
                	
                	<?php /*<input type="checkbox" label="search-bottom">
                	<label for="search-bottom">
                		<p>Bộ Lọc Sản Phẩm</p>
                		<div class=" search-cate">
                   			
                   		</div>
                    </label> */ ?>
                </div>
                <div class="col-12">
                    
                    <div class="row">
                        @if( isset($filters[13]) )
                        <div class="col-md-9 col-xs-12 mb-2 pr-2">
                            <div class="bg-primary p-1">                                                
                                <span class="badge badge-primary p-2 mr-2">Chọn Công Suất:</span>
                                    <div class="custom-control custom-control-inline custom-checkbox non-check" style="font-size:13px;font-weight: 800;margin-right: 15px ">
        <input type="checkbox" class="custom-control-input" name="filter[13][]" value="20" id="customCheck20">
        <label class="custom-control-label text-white" for="customCheck20">1HP</label>
    </div>
                                    <div class="custom-control custom-control-inline custom-checkbox non-check" style="font-size:13px;font-weight: 800;margin-right: 15px ">
        <input type="checkbox" class="custom-control-input" name="filter[13][]" value="22" id="customCheck22">
        <label class="custom-control-label text-white" for="customCheck22">1.5HP</label>
    </div>
                                    <div class="custom-control custom-control-inline custom-checkbox non-check" style="font-size:13px;font-weight: 800;margin-right: 15px ">
        <input type="checkbox" class="custom-control-input" name="filter[13][]" value="21" id="customCheck21">
        <label class="custom-control-label text-white" for="customCheck21">2HP</label>
    </div>
                                    <div class="custom-control custom-control-inline custom-checkbox non-check" style="font-size:13px;font-weight: 800;margin-right: 15px ">
        <input type="checkbox" class="custom-control-input" name="filter[13][]" value="23" id="customCheck23">
        <label class="custom-control-label text-white" for="customCheck23">2.5HP</label>
    </div>
                                    <div class="custom-control custom-control-inline custom-checkbox non-check" style="font-size:13px;font-weight: 800;margin-right: 15px ">
        <input type="checkbox" class="custom-control-input" name="filter[13][]" value="31,42" id="customCheck31">
        <label class="custom-control-label text-white" for="customCheck31">3HP</label>
    </div>
                                    
                                    <div class="custom-control custom-control-inline custom-checkbox non-check" style="font-size:13px;font-weight: 800;margin-right: 15px ">
        <input type="checkbox" class="custom-control-input" name="filter[13][]" value="32,43" id="customCheck32">
        <label class="custom-control-label text-white" for="customCheck32">4HP</label>
    </div>
                                    
                                    <div class="custom-control custom-control-inline custom-checkbox non-check" style="font-size:13px;font-weight: 800;margin-right: 15px ">
        <input type="checkbox" class="custom-control-input" name="filter[13][]" value="33" id="customCheck33">
        <label class="custom-control-label text-white" for="customCheck33">5HP</label>
    </div>
                                    <div class="custom-control custom-control-inline custom-checkbox non-check" style="font-size:13px;font-weight: 800;margin-right: 15px ">
        <input type="checkbox" class="custom-control-input" name="filter[13][]" value="35,34,39,40,41" id="customCheck35">
        <label class="custom-control-label text-white" for="customCheck35">Trên 5HP</label>
    </div>
                            </div>
                        </div>
                        @endif

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
                        <div class="col-auto pr-2 " style="width: 175px">
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
                        
                    </div>

                    @if( count($products) > 0 )
                    <div class="mt-3">{{ $products->links('frontend.default.blocks.paginate') }}</div>
                    @endif
                    
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