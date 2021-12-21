@extends('frontend.default.app')
@section('content')
<!-- PAGE SECTION START -->
<section class="page-section pb-4">
    <div class="container">
        <div style="padding: 15px; border-radius: 4px; background-color: #f9f9f9">
        <h2 class="section-title">{{ @$category ? $category->title : $site['title'] }}</h2>
        <div class="row">
            <div class="col-12">
                <div class="row">
                    @forelse($posts as $post)
                    {!! get_template_post($post,$type,4,'pt-3 pb-3') !!}
                @empty
                @endforelse
                    
                </div>
                
            </div>
            <div class="col-6"></div>
        </div>
        <div class="page-pagination text-center">
            {{ $posts->links('frontend.default.blocks.paginate') }}
        </div>
        </div>
    </div>
</section>
<!-- PAGE SECTION END -->
@endsection