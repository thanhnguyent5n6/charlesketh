<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="{{ asset('css/excel.css') }}" rel="stylesheet" type="text/css">
	
</head>
<body>
<table>
	<tr class="row-title">
		<td class="col-title"> Stt </td>
		<td class="col-title"> Ngày </td>
		<td class="col-title"> Mã Đơn Hàng </td>
		<td class="col-title"> Mã Hàng </td>
		<td class="col-title"> Số Lượng </td>
		<td class="col-title"> Tên KH </td>
		<td class="col-title"> Số ĐT </td>
		<td class="col-title"> NV Lắp Đặt </td>
	</tr>
	@forelse($data as $k => $val)
	<tr class="row">
		<td class="col"> {{ ($k+1) }} </td>
		<td class="col"> {{ $val['created_at'] }} </td>
		<td class="col"> {{ $val['code'] }} </td>
		<td class="col">
			@forelse($val['details'] as $pro)
				{{ $pro['product_code'] }},
			@empty
			@endforelse
		</td>
		<td class="col"> {{ $val['order_qty'] }} </td>
		<td class="col"> {{ $val['name'] }} </td>
		<td class="col"> {{ $val['phone'] }} </td>
		<td class="col"> {{ $val['address'] }} </td>
	</tr>
	@empty
	@endforelse
</table>
</body>
</html>