<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333333;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        h2 {
            color: #444444;
            font-size: 20px;
            margin-top: 20px;
            border-bottom: 2px solid #eeeeee;
            padding-bottom: 10px;
        }

        p {
            line-height: 1.6;
            margin: 0 0 15px 0;
        }

        strong {
            color: #555555;
        }

        ul {
            padding-left: 20px;
        }

        li {
            margin-bottom: 10px;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eeeeee;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Confirmation</h1>
        <p>Dear {{ $order->shipping_first_name }} {{ $order->shipping_last_name }},</p>
        <p>Thank you for your order! Here are your order details:</p>

        <h2>Order Information</h2>
        <p><strong>Order ID:</strong> {{ $order->id }}</p>
        <p><strong>Order Amount:</strong> ${{ number_format($order_amount, 2) }}</p>

        <h2>Shipping Information</h2>
        <p>
            <strong>Name:</strong> {{ $shipping_info['first_name'] }} {{ $shipping_info['last_name'] }}<br>
            <strong>Address:</strong> {{ $shipping_info['address'] }} {{ $shipping_info['apt'] }}, {{ $shipping_info['city'] }}, {{ $shipping_info['state'] }} {{ $shipping_info['zipcode'] }}
        </p>

        @if ($billing_info)
            <h2>Billing Information</h2>
            <p>
                <strong>Name:</strong> {{ $billing_info['first_name'] }} {{ $billing_info['last_name'] }}<br>
                <strong>Address:</strong> {{ $billing_info['address'] }} {{ $billing_info['apt'] }}, {{ $billing_info['city'] }}, {{ $billing_info['state'] }} {{ $billing_info['zipcode'] }}
            </p>
        @endif

        <h2>Order Items</h2>
        <ul>
            @foreach ($order_items as $item)
             @php $item = json_decode($item); @endphp
                <li>
                    @php $product = App\Http\Controllers\ProductController::getProductDetails($item->id); @endphp
                    @if($product)
                        {{--<img src="{{ asset($product->image) }}" height="70px" width="70px"/>--}}
                        <strong> {{ $product->name }}  </strong> <br>
                    @endif
                    @php $color = App\Http\Controllers\ColorController::getColorDetails($item->color_id); @endphp
                    @if($color)
                        {{--<img src="{{ asset($color->primary_image) }}" height="70px" width="70px"/>--}}
                        <strong> {{ $color->title }}  </strong> <br>
                    @endif
                    <strong>Mount Type:</strong> {{ $item->mount_type }}<br>
                    <strong>Height:</strong> {{ $item->height }}<br>
                    <strong>Width:</strong> {{ $item->width }}<br>
                    <strong>Quantity:</strong> {{ $item->quantity }}<br>
                </li>
            @endforeach
        </ul>

        <p>If you have any questions about your order, please contact us.</p>

        <p>Best regards,<br>Blinds And Shades</p>

        <div class="footer">
            &copy; {{ date('Y') }} Blinds And Shades. All rights reserved.<br>
            <!--<a href="#" style="color: #333333; text-decoration: none;">Unsubscribe</a>-->
        </div>
    </div>
</body>
</html>
