<section class="search-section pt-4 pb-4" data-wow-duration="2s" data-wow-delay="0.2s">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 col-lg-10">
				<div class="search-form">
		        	<h4 class="text-uppercase mb-3 text-white">{{ __('Bạn sắp đi đâu?') }}</h4>
		        	<div class="form-group">
			        	<div class="input-group">
							<input type="text" name="keyword" class="form-control typeahead" value="{{ Request::get('keyword') }}" placeholder="{{ __('site.search') }}" autocomplete="off">
							<div class="input-group-append">
								<button type="submit" class="btn"><i class="pe-7s-search"></i></button>
							</div>
						</div>
					</div>
					<div class="search-result"></div>
			    </div>
			</div>
		</div>
	</div>
</section>