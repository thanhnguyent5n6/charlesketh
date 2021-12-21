<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="{{ asset('css/excel.css') }}" rel="stylesheet" type="text/css">
	
</head>
<body>
<table>
	<tr class="row-title">
		<td class="col-title"> Tên SP </td>
		<td class="col-title"> Mã SP </td>
		<td class="col-title"> Giá website </td>
		<td class="col-title"> Giá cửa hàng </td>
		<td class="col-title"> Giá km </td>
	</tr>
	@forelse($data as $val)
	<tr class="row">
		<td class="col"> {{ $val->title }} </td>
		<td class="col"> {{ $val->code }} </td>
		<td class="col"> {{ $val->wholesale_price }} </td>
		<td class="col"> {{ $val->regular_price }} </td>
		<td class="col"> {{ $val->sale_price }} </td>
	</tr>
	@empty
	@endforelse
</table>
</body>
</html>