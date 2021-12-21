@extends('frontend.default.app')
@section('content')
<!-- PAGE SECTION START -->
<section class="page-section pb-4">
    @if( count($offers) > 0 )
    <div class="container pb-4">
        <h2 class="section-title">Coupon</h2>
        <div class="row">
            <div class="col-12">
                <div class="row">
                    @forelse($offers as $val)
                        {!! get_template_offer($val,'offer',4,'pb-4') !!}
                    @empty
                    <div class="col-12"><p> Nội dung chưa cập nhật </p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

    @if( count($tours) > 0 )
    <div class="container pb-4">
        <h2 class="section-title">Tour du lịch</h2>
        <div class="row">
            <div class="col-12">
                <div class="row">
                    @forelse($tours as $val)
                        {!! get_template_tour($val,'tour',4,'pb-4') !!}
                    @empty
                    <div class="col-12"><p> Nội dung chưa cập nhật </p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

    @if( count($rentals) > 0 )
    <div class="container">
        <h2 class="section-title">Cho thuê xe</h2>
        <div class="row">
            <div class="col-12">
                <div class="row">
                    @forelse($rentals as $val)
                        {!! get_template_rental($val,'rental',3,'pb-4') !!}
                    @empty
                    <div class="col-12"><p> Nội dung chưa cập nhật </p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

</section>
<!-- PAGE SECTION END -->
@endsection