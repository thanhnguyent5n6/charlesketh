@extends('frontend.default.app')
@section('content')
<!-- PAGE SECTION START -->
<div class="container">
	<div class="row text-right mb-4">
		<div class="col-md-6 col-xs-12 server-ml">
			<h2>DỊCH VỤ VỆ SINH</h2>
			<h3>MÁY LẠNH</h3>
			<p>MIỄN PHÍ 100%</p>
			<hr>
			<span class="d-block mb-4">GỌI ĐIỆN TƯ VẤN MIỄN PHÍ: 0828 100 100 (8H - 22H) </span>
			<h4>VỆ SINH MÁY LẠNH GIÚP</h4>
			<ul class="list-unstyled">
				<li>TĂNG TUỔI THỌ CHO MÁY LẠNH</li>
				<li>TIẾT KIỆM ĐIỆN NĂNG</li>
			</ul>
			<h4>DÀNH CHO KHÁCH HÀNG MUA TẠI ĐIỆN MÁY GIÁ SỈ & ĐƠN VỊ KHÁC</h4>
				<span>*NHÂN VIÊN CÓ CHUYÊN MÔN TỐT, TẬN TÂM, TƯ VẤN MIỄN PHÍ</span>
		</div>
		<div class="col-md-6">
			<img src="/public/images/banner-clean.png" alt="vệ sinh máy lạnh">
		</div>
	</div>

	<div class="row form-ml p-5">
		<div class="col-md-6 col-xs-12 text-center">
			<h3 class="text-warning">TỪ NGÀY 01/02 ĐẾN 28/02</h3>
			<p class="text-white" style="font-size: 2rem;">MIỄN PHÍ 100%</p>
			<p class="text-white" style="font-size: 1.4rem;">CHO DỊCH VỤ VỆ SINH MÁY LẠNH</p>
			<span class="text-white">tại</span>
			<p class="text-warning mt-2" style="font-size: 2.1rem;">ĐIỆN MÁY GIÁ SỈ</p>
		</div>
		<div class="col-md-6 col-xs-12">
			<h3 class="text-white">THÔNG TIN ĐĂNG KÝ</h3>
			<form action="">
				<div class="row">
					<div class="col-md-12 col-xs-12">
						<input class="w-100 p-1 mb-2" type="text" name="name" required placeholder="Họ và tên ( bắt buộc )">
					</div>
					<div class="col-md-12 col-xs-12">
						<input class="w-100 p-1 mb-2" type="number" name="phone" required placeholder="Số điện thoại ( bắt buộc )">
					</div>
					<div class="col-md-12 col-xs-12">
						<input class="w-100 p-1 mb-2" type="text" name="address[]" placeholder="Địa chỉ">
					</div>
					<div class="col-md-12 col-xs-12">
						<input class="w-100 p-1 mb-2" type="text" name="address[]" placeholder="Quận huyện, thành phố">
					</div>
				</div>
				<button type="submit" class="btn btn-warning btn-ajax px-3" data-ajax="act=cleaning-service|type=cleaning-service">ĐĂNG KÝ</button>
			</form>
				
		</div>
	</div>
	<div class="mt-3">
		<p><b>Vệ Sinh Miễn Phí Máy Lạnh/ ĐHKK cho khách hàng</b></p>
		<ul>
			<li class="mb-1">Miễn Phí Máy ĐHKK cho khách hàng mua hàng tại Điện Máy Giá Sỉ, không hạn chế số lượng. Miễn Phí 100% kể cả xạc Gas</li>
			<li>NẾU KHÁCH HÀNG MUA MÁY LẠNH / ĐHKK TẠI ĐƠN VỊ KHÁC, Mỗi Khách Hàng 01 máy | Trường hợp khách cần vệ sinh thêm máy thì phí phát sinh thêm trên 1 máy Mono: 100K / Máy Inverter : 150K ( Bao gồm cả sạc Gas )</li>
		</ul>
	</div>
</div>
<!-- PAGE SECTION END -->
@endsection