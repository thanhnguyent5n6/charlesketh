@component('mail::message')

<p> Đơn hàng #{{ $order->code }}</p>


Thanks,<br>
{{ config('app.name') }}

@endcomponent