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
		<td class="col-title"> Số ĐT </td>
		<td class="col-title"> Địa chỉ </td>
		<td class="col-title"> Số lượng </td>
	</tr>
	@forelse($data as $k => $val)
	<tr class="row">
		<td class="col"> {{ ($k+1) }} </td>
		<td class="col"> {{ $val['name'] }} </td>
		<td class="col"> {{ $val['phone'] }} </td>
		<td class="col"> {{ $val['address'] }} </td>
		<td class="col"> {{ $val['order_count'] }} </td>
	</tr>
	@empty
	@endforelse
</table>
</body>
</html>