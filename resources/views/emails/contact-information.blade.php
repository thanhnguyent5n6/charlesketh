@component('mail::message')

@component('mail::panel')
Hello! Bạn nhận được email liên hệ từ {{ config('app.name') }}
@endcomponent
<hr>
<p> Thông tin liên hệ bao gồm nội dung sau đây:</p>
<div class="table">
	<table>
		<tr>
			<td style="width:150px; text-align: left;">Họ và Tên</td>
			<td>{{ $contact['name'] }}</td>
		</tr>
		<tr>
			<td style="width:150px; text-align: left;">Email</td>
			<td>{{ $contact['email'] }}</td>
		</tr>
		<tr>
			<td style="width:150px; text-align: left;">Chủ đề</td>
			<td>{{ $contact['title'] }}</td>
		</tr>
		<tr>
			<td style="width:150px; text-align: left;">Tin nhắn</td>
			<td>{{ $contact['description'] }}</td>
		</tr>
	</table>
</div>
Thanks,<br>
{{ config('app.name') }}

@endcomponent