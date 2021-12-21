@extends('frontend.default.app')
@section('content')
<!-- PAGE SECTION START -->
<section class="page-section pb-4">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center text-danger mb-5">
				<img src="./public/images/coupon.png" alt="coupon">
	       </div>
	     </div>
	    <div class="row">
				
			<div class="col-md-4 col-xs-12 bg-light p-3 border">        
	            <div class="offer-icon">
	                <img src="https://dienmaygiasi.vn/public/uploads/products/2019-02/s09a-m_1550654631.jpg" alt="">
	            
			        <div>
			            <h2 class="offer-title">MÁY LẠNH ASANZO S09A | 1HP</h2>
			            <div class="text-danger">Giảm thêm 100k</div>
			            <div class="text-success"><i class="pe-7s-timer"></i> 5/6/2019</div>
			        </div>
		        </div>
				 <div class="offer-info">
					<div class="input-group mb-3">
						<input type="text" id="input-offer-code" class="form-control form-control-lg" value="S09A">
						<div class="input-group-append">
							<button type="button" class="btn btn-site" id="btn-copy-offer-code">Sao chép <span class="d-none d-sm-inline-block">mã giảm giá</span></button>
						</div>
					</div>
			            <a href="https://dienmaygiasi.vn/may-lanh-asanzo-s09a-1hp" class="btn btn-lg btn-block btn-danger text-white text-uppercase pt-3 pb-3" target="_blank"> Đi đến chi tiết sản phẩm </a>
			    </div>
			</div>	
		</div>
    </div>
</section>
<!-- PAGE SECTION END -->
@endsection