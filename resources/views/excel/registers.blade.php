<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="{{ asset('css/excel.css') }}" rel="stylesheet" type="text/css">
	
</head>
<body>
<table>
	<tr class="row-title">
		<td class="col-title"> Tiêu đề </td>
		<td class="col-title"> Họ và tên </td>
		<td class="col-title"> Email </td>
		<td class="col-title"> Điện thoại </td>
	</tr>
	@forelse($data as $val)
	<tr class="row">
		<td class="col"> {{ $val['title'] }} </td>
		<td class="col"> {{ $val['name']}} </td>
		<td class="col"> {{ $val['email'] }} </td>
		<td class="col"> {{ $val['phone'] }} </td>
	</tr>
	@empty
	@endforelse
</table>
</body>
</html>