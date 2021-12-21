@component('mail::message')

@component('mail::panel')
Cám ơn bạn đã đặt hàng tại website chúng tôi
@endcomponent

<hr>
<p> Đơn hàng #{{ $order->code }} của bạn bao gồm các sản phẩm sau đây:</p>

@component('mail::table')

| MÃ SP | TÊN SẢN PHẨM | GIÁ BÁN (Đ) | SỐ LƯỢNG | TỔNG (Đ) |
|:-----:|--------------|:-------------:|:--------:|:----------:|
@forelse($products as $product)
| {{ $product['product_code'] }} | {{ $product['product_title'] }} | {{ number_format($product['product_price'],0,',','.') }} | {{ $product['product_qty'] }} | {{ number_format($product['product_price']*$product['product_qty'],0,',','.') }} |
@empty
@endforelse
<td colspan=3 align="right"> Tổng tiền | <center><b>{{ number_format($order->subtotal, 0, ',', '.') }} đ</b></center> |
@endcomponent

@if($order->coupon_code !== null)
@component('mail::subcopy')
<p><i>Bạn được giảm <b>{{ number_format($order->coupon_amount, 0, ',', '.') }} đ</b> cho đơn hàng này </i></p>
@endcomponent
@endif

@component('mail::panel')
<p style="text-align: center;">Tổng đơn hàng: <b>{{ number_format($order->order_price, 0, ',', '.') }} đ</b></p>
@endcomponent

@component('mail::button', ['url' => route('frontend.cart.tracking',['email'=>$order->email, 'code'=>$order->code]), 'color' => 'green'])
Xem đơn hàng
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@endcomponent