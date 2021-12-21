<section @if( @$bg_breadcrumb->image && file_exists(public_path('/uploads/photos/'.@$bg_breadcrumb->image)) ) class="breadcrumb-section" style="background-image: url('{{ asset( 'public/uploads/photos/'.$bg_breadcrumb->image ) }}')" @else class="breadcrumb-section no-background" @endif >
    <div class="page-breadcrumb">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul class="breadcrumb">
                        {!! $breadcrumb !!}
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- END SLIDER SECTION -->