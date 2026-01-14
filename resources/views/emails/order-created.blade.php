<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Menunggu Pembayaran</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f0f0f0; -webkit-font-smoothing: antialiased;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f0f0f0;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #484A56 0%, #7A5657 50%, #B58687 100%); padding: 50px 40px; text-align: center;">
                            <div style="width: 70px; height: 70px; background-color: rgba(255,255,255,0.15); border-radius: 50%; margin: 0 auto 20px; display: inline-block; line-height: 70px;">
                                <span style="font-size: 28px; color: #ffffff;">🛒</span>
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase;">Beautylatory</h1>
                            <p style="margin: 12px 0 0; color: rgba(255,255,255,0.85); font-size: 14px; font-weight: 400;">Pesanan Menunggu Pembayaran</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 45px 40px 35px;">
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Halo <strong>{{ $order->user->name }}</strong>,
                            </p>
                            <p style="margin: 0 0 25px; font-size: 15px; color: #666666; line-height: 1.7;">
                                Terima kasih telah berbelanja di Beautylatory! Pesanan kamu sudah kami terima dan menunggu pembayaran.
                            </p>

                            <!-- Order Info Card -->
                            <table role="presentation" style="width: 100%; margin-bottom: 25px; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 20px; background: linear-gradient(135deg, #fdf2f4 0%, #fef7f8 100%); border-radius: 16px; border: 1px solid #fce4e8;">
                                        <p style="margin: 0 0 5px; font-size: 11px; color: #9C6C6D; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;">Nomor Pesanan</p>
                                        <p style="margin: 0; font-size: 22px; color: #1a1a1a; font-weight: 700;">{{ $order->order_number }}</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Order Items -->
                            <p style="margin: 0 0 15px; font-size: 11px; color: #9C6C6D; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;">Detail Pesanan</p>
                            <table role="presentation" style="width: 100%; margin-bottom: 20px; border-collapse: collapse;">
                                @foreach($order->items as $item)
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                                        <p style="margin: 0; font-size: 14px; color: #333333; font-weight: 500;">{{ $item->product_name }}</p>
                                        <p style="margin: 4px 0 0; font-size: 13px; color: #888888;">{{ $item->quantity }}x @ Rp {{ number_format($item->product_price, 0, ',', '.') }}</p>
                                    </td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f0f0f0; text-align: right;">
                                        <p style="margin: 0; font-size: 14px; color: #333333; font-weight: 600;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                    </td>
                                </tr>
                                @endforeach
                            </table>

                            <!-- Totals -->
                            <table role="presentation" style="width: 100%; margin-bottom: 25px; background-color: #fafafa; border-radius: 12px;">
                                <tr>
                                    <td style="padding: 15px 20px;">
                                        <table role="presentation" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 5px 0; font-size: 14px; color: #666666;">Subtotal</td>
                                                <td style="padding: 5px 0; font-size: 14px; color: #333333; text-align: right;">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 5px 0; font-size: 14px; color: #666666;">Ongkos Kirim ({{ $order->shipping_courier ?? 'Standard' }})</td>
                                                <td style="padding: 5px 0; font-size: 14px; color: #333333; text-align: right;">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                            </tr>
                                            @if($order->voucher_discount > 0)
                                            <tr>
                                                <td style="padding: 5px 0; font-size: 14px; color: #22c55e;">Diskon Voucher</td>
                                                <td style="padding: 5px 0; font-size: 14px; color: #22c55e; text-align: right;">-Rp {{ number_format($order->voucher_discount, 0, ',', '.') }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 10px 0 5px; font-size: 16px; color: #1a1a1a; font-weight: 700; border-top: 1px solid #e5e5e5;">Total</td>
                                                <td style="padding: 10px 0 5px; font-size: 18px; color: #9C6C6D; font-weight: 700; text-align: right; border-top: 1px solid #e5e5e5;">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Shipping Address -->
                            <p style="margin: 0 0 10px; font-size: 11px; color: #9C6C6D; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;">Alamat Pengiriman</p>
                            <div style="padding: 15px; background-color: #f8f8f8; border-radius: 12px; margin-bottom: 30px;">
                                <p style="margin: 0; font-size: 14px; color: #333333; line-height: 1.6;">
                                    {{ $order->shipping_address }}<br>
                                    {{ $order->shipping_village ? $order->shipping_village.', ' : '' }}{{ $order->shipping_district ? $order->shipping_district.', ' : '' }}<br>
                                    {{ $order->shipping_city }}, {{ $order->shipping_province }} {{ $order->shipping_postal_code }}<br>
                                    <strong>Telp:</strong> {{ $order->phone }}
                                </p>
                            </div>

                            <!-- Payment Deadline -->
                            @if($order->payment_expired_at)
                            <div style="padding: 20px; background: linear-gradient(135deg, #fef3c7 0%, #fef9c3 100%); border-radius: 12px; margin-bottom: 30px; border-left: 4px solid #f59e0b;">
                                <p style="margin: 0; font-size: 14px; color: #92400e; font-weight: 600;">
                                    ⏰ Selesaikan pembayaran sebelum: {{ $order->payment_expired_at->format('d M Y, H:i') }} WIB
                                </p>
                            </div>
                            @endif

                            <!-- CTA Button -->
                            @if($order->payment_url)
                            <table role="presentation" style="width: 100%; margin-bottom: 30px;">
                                <tr>
                                    <td style="text-align: center;">
                                        <a href="{{ $order->payment_url }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #9C6C6D 0%, #B58687 100%); color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 600; border-radius: 50px; letter-spacing: 0.5px; box-shadow: 0 8px 20px rgba(156, 108, 109, 0.3);">
                                            Bayar Sekarang
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @endif
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: #484A56; text-align: center;">
                            <p style="margin: 0 0 8px; font-size: 13px; color: rgba(255,255,255,0.9); font-weight: 500;">Beautylatory</p>
                            <p style="margin: 0; font-size: 12px; color: rgba(255,255,255,0.6); line-height: 1.6;">
                                Email ini dikirim otomatis. Jangan balas email ini.<br>
                                Butuh bantuan? Hubungi kami di support@beautylatory.com
                            </p>
                        </td>
                    </tr>
                </table>

                <table role="presentation" style="max-width: 600px; margin: 25px auto 0;">
                    <tr>
                        <td style="text-align: center;">
                            <p style="margin: 0; font-size: 11px; color: #999999;">© {{ date('Y') }} Beautylatory. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
