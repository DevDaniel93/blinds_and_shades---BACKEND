<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Request A Quote</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  
<style>
    body {
      font-family: Arial, Helvetica, sans-serif;
    }
    .email-template {
      width: 600px;
      margin: auto;
      align-items: center;
    }
    .email-template_logo {
      width: 290px;
      padding: 50px 0 30px;
    }
    .email-template table {
      background-color: #f7f7f7;
      width: 100%;
      border: 0;
    }
    .email-template td {
      padding: 20px 60px;
    }
    .email-template h1 {
      font-size: 28px;
      font-weight: 700;
      margin: 0;
      color: #ffa506 !important;
    }
    .email-body {
      color: #777;
      font-family: "Poppins", sans-serif;
      padding-top: 10px;
      font-size: 15px;
      line-height: 1.5;
    }
    .email-btn {
      font-size: 16px;
      background: #ff3836;
      color: #fff;
      padding: 13px 16px;
      border: 0;
      outline: 0;
      box-shadow: unset;
      cursor: pointer;
      font-weight: 700;
    }
    .theme-text {
      color: #ffa506 !important;
    }
    
    /* Mested Table */
    .product_item_table{
      margin-bottom: 20px;
    }
    .product_item_table th h3 {
      margin: 0;
    }
    .product_item_table th{
      padding: 5px;
    }
    .product_item_table td {
      padding: 5px 5px;
    }
    .product_item_table td{
      width: 50%;
    }
    .product_item_table, .product_item_table th, .product_item_table td {
      border: 1px solid #1b2e51;
      border-collapse: collapse;
    }
    td.product_color_text {
      font-weight: 700;
    }
    
    .products__title{
      color: #1b2e51;
      text-align: center;
      margin-top: 0;
    }
    
    th.product-title {
      background-color: #1b2e51 !important;
      color: #fff !important;
      padding: 10px 5px;
    }

</style>
  </head>
  <body>
    <div class="email-template">
      <table>
        <!-- Header -->
        <tr>
          <th>
            <img alt="Logo" src="https://custom3.mystagingserver.site/blinds-and-shades-backend/public/images/invoice-logo.png" class="email-template_logo" />
            <h1>Request A Quote</h1>
          </th>
        </tr>
        <!-- Body -->
        <tr>
          <td>
            <div class="products">
              <h3>Customer's Information</h3>
              <p>
                  <strong>Name:</strong> {{ $info['name'] }}<br>
                  <strong>Email:</strong> {{ $info['email'] }}<br>
                  <strong>Phone:</strong> {{ $info['phone'] }}<br>
                  <strong>Comment:</strong> {{ ($info['comment'] != null)? $info['comment'] : 'None' }}<br>
              </p>
              <h2 class="products__title">Products</h2>

              @foreach ($order_items as $item)
              @php $item = json_decode($item); @endphp
              @php $product = App\Http\Controllers\ProductController::getProductDetails($item->id); @endphp
              
              <table class="product_item_table">
                @if($product)
                <thead>
                  <tr>
                    <th class="product-title" colspan="2"><h3>{{ $product->name }}</h3></th>
                  </tr>
                </thead>
                @endif
                <tbody>
                    @php $color = App\Http\Controllers\ColorController::getColorDetails($item->color_id); @endphp
                    @if($color)
                    <tr>
                      <td class="product_color_text">Color:</td>
                      <td class="product_color_value">{{ $color->title }} </td>
                    </tr>
                    @endif
                    <tr>
                      <td class="product_color_text">Mount Type:</td>
                      <td class="product_color_value">{{ $item->mount_type }}</td>
                    </tr>
                    <tr>
                      <td class="product_color_text">Height:</td>
                      <td class="product_color_value"> {{ $item->height }} </td>
                    </tr>
                    <tr>
                      <td class="product_color_text">Width:</td>
                      <td class="product_color_value"> {{ $item->width }} </td>
                    </tr>
                    <tr>
                      <td class="product_color_text">Quantity:</td>
                      <td class="product_color_value"> {{ $item->quantity }} </td>
                    </tr>
                </tbody>
              </table>

              @endforeach
              <table class="product_item_table">
                <tbody>
                  <tr>
                    <td class="product_color_text">Total Price:</td>
                    <td class="product_color_value">${{ number_format($order_amount, 2) }}</td>
                  </tr>
                </tbody>
              </table>

              
            </div>
            <!-- <p class="email-body">
              Lorem ipsum dolor sit amet consectetur adipisicing elit.
              Provident, earum accusamus beatae soluta expedita voluptate iure?
              Doloribus porro dolor, nesciunt impedit itaque deleniti sed,
              deserunt ducimus cum nulla incidunt. Ipsum.
            </p> -->
            <!-- <button class="email-btn">Head To your Company HR</button> -->
            <p class="email-body theme-text"> &copy; {{ date('Y') }} Blinds And Shades. All rights reserved.</p>
          </td>
        </tr>
      </table>
    </div>
  </body>
</html>