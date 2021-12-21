<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="{{ asset('css/excel.css') }}" rel="stylesheet" type="text/css">
	
</head>
<body>
<table>
	<tr class="row-title">
		<td class="col-title"> Mã SP </td>
		<td class="col-title"> Tên SP (dienmayxanh.com) </td>
		<td class="col-title"> Giá bán (dienmayxanh.com) </td>
		<td class="col-title"> Tên SP (dienmaycholon.vn) </td>
		<td class="col-title"> Giá bán (dienmaycholon.vn) </td>
	</tr>
	@forelse($data as $key => $val)
	<tr class="row">
		<td class="col"> {{ $key }} </td>
		<td class="col"> {{ $val['name'] }} </td>
		<td class="col"> {{ $val['price'] }} </td>
		<td class="col"> {{ $val['name_cholon'] }} </td>
		<td class="col"> {{ $val['price_cholon'] }} </td>
	</tr>
	@empty
	@endforelse
</table>
</body>
</html>