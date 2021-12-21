<!-- NEWSLETTER BEGIN -->
<section class="newsletter-section">
	<div class="container">
		<div class="row">
			<div class="col">
				
				<form action="#" method="post">
					<h5 class="mb-3">{{ __('Bạn có muốn là người đầu tiên nhận được những khuyến mãi hấp dẫn từ các cửa hàng yếu thích của bạn?') }}</h5>
				    <div class="form-group row">
				    	<div class="input-group col-12 col-sm-7">
				    		<input type="email" value="" name="email" class="form-control" placeholder="Email Address" required>
				    		<div class="input-group-append">
				    			<button type="submit" class="btn btn-danger btn-lg btn-ajax uppercase" data-ajax="act=newsletter|type=newsletter"> {{ __('Xác nhận') }} </button>
				    		</div>
				    	</div>
				    </div>
				    <div class="form-group">
					    <div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="customCheck1">
							<label class="custom-control-label" for="customCheck1">Check this custom checkbox</label>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<!-- NEWSLETTER END -->
