@extends('frontend.default.app')
@section('content')
<!-- PAGE SECTION START -->
<section class="page-section ptb-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <!-- Single Detail -->
                @if($page)
                <div class="post-detail">
                    <h1 class="title">{{ $page->title }}</h1>
                    {!! $page->contents !!}
                </div>
                @else
                {!! __('site.update_content') !!}
                @endif
            </div>
        </div>
    </div>
</section>
<!-- PAGE SECTION END -->
@endsection