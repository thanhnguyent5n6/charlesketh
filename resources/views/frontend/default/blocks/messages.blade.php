<div class="col-12">
	@if (count($errors) > 0)
	<div class="alert alert-danger" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
	    <ul>
	        @foreach ($errors->all() as $error)
	            <li>{!! $error !!}</li>
	        @endforeach
	    </ul>
	</div>
	@endif
	
	@if(session('danger'))
	<div class="note note-danger">
	    <p> THÔNG BÁO: {!! session('danger') !!}. </p>
	</div>
	@endif

	@if(session('success')) 
	<div class="note note-success">
	    <p> THÔNG BÁO: {!! session('success') !!}. </p>
	</div>
	@endif

	@if (session('status'))
    <div class="alert alert-success">
        {!! session('status') !!}
    </div>
    @endif
</div>