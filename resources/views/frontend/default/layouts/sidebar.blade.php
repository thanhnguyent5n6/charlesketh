<div class="sidebar">
    <div id="show-search">
    <div class="sidebar-widget">
        <h4 class="title">{{ __('Hãng sản xuất') }}</h4>
        <div class="content">
            
            <div class="row">
                @forelse(@$suppliers as $key => $val)
                <div class="col-12">
                    <div class="custom-control custom-radio">
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
        </div>
    </div>

    <div class="sidebar-widget">
        <h4 class="title">{{ __('Chọn giá từ') }}</h4>
        <div class="content">
            <div class="row" style="font-size: 13px">
                <div class="col-6 pr-1">
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="price" value="1" id="customCheck1">
                        <label class="custom-control-label" for="customCheck1">Dưới <b>2</b> triệu</label>
                    </div>
                </div>

                <div class="col-6 pr-1">
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="price" value="2" id="customCheck2">
                        <label class="custom-control-label" for="customCheck2">Từ <b>2 - 4</b> triệu</label>
                    </div>
                </div>

                <div class="col-6 pr-1">
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="price" value="3" id="customCheck3">
                        <label class="custom-control-label" for="customCheck3">Từ <b>4 - 7</b> triệu</label>
                    </div>
                </div>
                <div class="col-6 pr-1">
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="price" value="4" id="customCheck4">
                        <label class="custom-control-label" for="customCheck4">Từ <b>7 - 12</b> triệu</label>
                    </div>
                </div>
                <div class="col-6 pr-1">
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="price" value="5" id="customCheck5">
                        <label class="custom-control-label" for="customCheck5">Từ <b>12 - 25</b> triệu</label>
                    </div>
                </div>
                <div class="col-6 pl-1">
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="price" value="6" id="customCheck6">
                        <label class="custom-control-label" for="customCheck6">Từ <b>25 - 50</b> triệu</label>
                    </div>
                </div>
                <div class="col-6 pl-1">
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="price" value="7" id="customCheck7">
                        <label class="custom-control-label" for="customCheck7">Từ <b>50 - 75</b> triệu</label>
                    </div>
                </div>
                <div class="col-6 pl-1">
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" name="price" value="8" id="customCheck8">
                        <label class="custom-control-label" for="customCheck8">Trên <b>75</b> triệu</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if( @$filters )
    @forelse(@$filters[0] as $filter)
    <div class="sidebar-widget">
        <h4 class="title">{{ $filter->title }}</h4>
        <div class="content">
            <div class="row">
                @if( $filter->image )
                <div class="col-12"> <img src="{{ asset('uploads/categories/'.$filter->image) }}" class="img-fuild"> </div>
                @endif
                @if( isset($filters[$filter->id]) && $filter->id != 13 )
                @forelse($filters[$filter->id] as $key => $val)
                <div class="col-6 {{ ($key+1)%2 == 0 ? 'pl-1' : 'pr-1' }}">
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