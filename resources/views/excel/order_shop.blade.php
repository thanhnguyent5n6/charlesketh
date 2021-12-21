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
		<td class="col-title"> Tên KH </td>
		<td class="col-title"> Số ĐH </td>
		<td class="col-title"> Mã KH </td>
		<td class="col-title"> Địa Chỉ GH </td>
		<td class="col-title"> Số HĐ </td>
		<td class="col-title"> Mã Hàng </td>
		<td class="col-title"> ĐVT </td>
		<td class="col-title"> SL </td>
		<td class="col-title"> Đơn Giá </td>
		<td class="col-title"> T.Tiền </td>
		<td class="col-title" colspan="5"> Đã T.Toán </td>
		<td class="col-title"> Giá Kê </td>
		<td class="col-title"> Còn phải thu </td>
	</tr>
	<tr class="row-title">
		<td class="col-title"></td>
		<td class="col-title"></td>
		<td class="col-title"></td>
		<td class="col-title"></td>
		<td class="col-title"></td>
		<td class="col-title"></td>
		<td class="col-title"></td>
		<td class="col-title"></td>
		<td class="col-title"></td>
		<td class="col-title"></td>
		<td class="col-title"></td>
		<td class="col-title">TM</td>
		<td class="col-title">CK</td>
		<td class="col-title">Tổng</td>
		<td class="col-title">Đã cọc (Cá Nhân)</td>
		<td class="col-title">Đã thanh toán</td>
		<td class="col-title"></td>
		<td class="col-title"></td>
	</tr>
	@forelse($data as $k => $val)
	<tr class="row">
		<td class="col">{{ ($k+1) }}</td>
		<td class="col">{{ $val['name'] }}</td>
		<td class="col">{{ $val['code'] }}</td>
		<td class="col">{{ $val['phone'] }}</td>
		<td class="col">{{ $val['address'] }}</td>
		<td class="col">  </td>
		<td class="col">
			@forelse($val['details'] as $pro)
				{{ $pro['product_code'] }}<br/>
			@empty
			@endforelse
		</td>
		<td class="col"></td>
		<td class="col">{{ $val['order_qty'] }}</td>
		<td class="col">
			@forelse($val['details'] as $pro)
				{{ $pro['product_price'] }}<br/>
			@empty
			@endforelse
		</td>
		<td class="col">{{ $val['order_price'] }}</td>

		<td class="col">{{ $val['payment_id'] == 1 ? 'X' : '' }}</td>
		<td class="col">{{ $val['payment_id'] != 1 ? 'X' : '' }}</td>

		<td class="col">{{ $val['order_price'] }}</td>
		<td class="col">{{ $val['deposit_amount'] }}</td>
		<td class="col">{{ $val['received_amount'] }}</td>

		<td class="col">
			@forelse($val['details'] as $pro)
				{{ $pro['product_price_second'] }}<br/>
			@empty
			@endforelse
		</td>
		<td class="col"></td>
	</tr>
	@empty
	@endforelse
</table>
</body>
</html>