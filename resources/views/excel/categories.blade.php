<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="{{ asset('css/excel.css') }}" rel="stylesheet" type="text/css">
	
</head>
<body>
<table>
	<tr class="row-title">
		<td class="col-title"> Parent </td>
		<td class="col-title"> Name </td>
	</tr>
	@forelse($data as $val)
	<tr class="row">
		<td class="col"> {{ $val->parent }} </td>
		<td class="col"> {{ $val->title }} </td>
	</tr>
	@empty
	@endforelse
</table>
</body>
</html>