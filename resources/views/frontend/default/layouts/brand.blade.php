@php $photos = get_photos('partners',$lang); @endphp
<section class="partners-section pb-3">
	<div class="container">
		<div class="row">
			<div class="col">
				<div class="slick-partners">
			    @forelse($photos as $photo)
			        <div>
			            <a href="{{ $photo->link }}"><img src="{{ ( $photo->image && file_exists(public_path('/uploads/photos/'.$photo->image)) ? asset( 'public/uploads/photos/'.get_thumbnail($photo->image) ) : asset('noimage/200x100') ) }}" alt="{{ $photo->alt }}" /></a></div>
			    @empty
			    @endforelse
			    </div>
			</div>
		</div>
	</div>
</section>