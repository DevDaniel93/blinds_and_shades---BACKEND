<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use ErrorException;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with('orderItem')->get();
            $response =
            [
                'status' => true,
                'data' => $orders,
                'message' => 'All orders'
            ];
            return response()->json($response, 200);
    }
     
    public function indexForUserside(Request $request)
    {
        $orders = Order::with('orderItem')->where('email',$request->email)->get();
            $response =
            [
                'status' => true,
                'data' => $orders,
                'message' => 'All orders'
            ];
            return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_first_name' => 'required|string',
            'shipping_last_name' => 'required|string',
            'shipping_address' => 'required|string',
            'shipping_apt' => 'nullable|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'required|string|max:10',
            'shipping_zipcode' => 'required|string',
            'is_billing_address_same' => 'required|integer|in:0,1',
            'billing_first_name' => 'nullable|required_if:is_billing_address_same,0|string',
            'billing_last_name' => 'nullable|required_if:is_billing_address_same,0|string',
            'billing_address' => 'nullable|required_if:is_billing_address_same,0|string',
            'billing_apt' => 'nullable|required_if:is_billing_address_same,0|string',
            'billing_city' => 'nullable|required_if:is_billing_address_same,0|string',
            'billing_state' => 'nullable|required_if:is_billing_address_same,0|string|max:10',
            'billing_zipcode' => 'nullable|required_if:is_billing_address_same,0|string',
            'shipping_method' => 'required|string',
            'create_account' => 'sometimes|integer',
            'password' => 'nullable|required_if:create_account,1|string|confirmed|min:6',
            'password_confirmation' => 'nullable|required_if:create_account,1|string',
            'payment_method_type' => 'required|string|in:credit_card,other', // Adjust as necessary
            'token' => 'required_if:payment_method_type,credit_card',
            'email' => 'required|email',
            'phone' => 'nullable|string',  //regex:/^[0-9\-\+\(\)]+$/
            'total_price' => 'required|numeric',
            'order_items' => 'required|array',
            // 'order_items.*.id' => 'required|integer|exists:products,id',
            // 'order_items.*.color_id' => 'required|integer|exists:colors,id',
            // 'order_items.*.mount_type' => 'required|string',
            // 'order_items.*.height' => 'required|numeric',
            // 'order_items.*.width' => 'required|numeric',
            // 'order_items.*.guarantee_fit' => 'required|integer',
            // 'order_items.*.room_name_wall' => 'nullable|array',
            // 'order_items.*.room_name_wall.name' => 'nullable|string',
            // 'order_items.*.room_name_wall.wall' => 'nullable|string',
            // 'order_items.*.customizations_selected' => 'required|array',
            // 'order_items.*.customizations_selected.*.id' => 'required|integer',
            // 'order_items.*.customizations_selected.*.addon_id' => 'required|integer',
            // 'order_items.*.customizations_selected.*.title' => 'required|string',
            // 'order_items.*.customizations_selected.*.description' => 'nullable|string',
            // 'order_items.*.customizations_selected.*.video_link' => 'nullable|url',
            // 'order_items.*.customizations_selected.*.status' => 'required|string',
            // 'order_items.*.customizations_selected.*.amount' => 'required|numeric',
            // 'order_items.*.customizations_selected.*.image' => 'nullable|string',
            // 'order_items.*.customizations_selected.*.type' => 'required|string',
            // 'order_items.*.customizations_selected.*.variables' => 'nullable|array',
            // 'order_items.*.customizations_selected.*.variables.*.title' => 'nullable|string',
            // 'order_items.*.customizations_selected.*.variables.*.description' => 'nullable|string',
            // 'order_items.*.customizations_selected.*.variables.*.var_itmes' => 'nullable|array',
            // 'order_items.*.customizations_selected.*.variables.*.var_itmes.*.title' => 'nullable|string',
            // 'order_items.*.customizations_selected.*.variables.*.var_itmes.*.price' => 'nullable|numeric',
            // 'order_items.*.warranty_option' => 'required|array',
            // 'order_items.*.warranty_option.title' => 'required|string',
            // 'order_items.*.warranty_option.price' => 'required|numeric',
            // 'order_items.*.quantity' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }
        
        // calculate order amount
        $orderAmount = $request->order_amount;
    
        $input = $request->all();
        // Create new order entry
         if($request->is_billing_address_same != 0){
           $input['billing_first_name'] =  $request->shipping_first_name;
           $input['billing_last_name'] =  $request->shipping_last_name;
           $input['billing_street_address'] =  $request->shipping_address;
           $input['billing_apt'] =  $request->shipping_apt;
           $input['billing_city'] =  $request->shipping_city;
           $input['billing_state'] =  $request->shipping_state;
           $input['billing_zip_code'] =  $request->shipping_zipcode;
         }
        $input['order_status'] =  'Pending'; 
        $order = Order::create($input);
        if($order == '' || $order == null){
             return response()->json([
            'status' => false,
            'message' => 'Failed to submit order.',
            ], 401);
        }
        
        if(count($request->order_items) > 0){
            foreach ($request->order_items as $item) {
                $item = json_decode($item);
              
                $entry = new OrderItem();
                $entry->order_id = $order->id;
                $entry->product_id = $item->id;
                $entry->color_id = $item->color_id;
                $entry->mount_type = $item->mount_type;
                $entry->height = $item->height;
                $entry->width = $item->width;
                $entry->guarantee_fit = $item->guarantee_fit??0;  //not coming from frontend cart
                $entry->room_name = $item->room_name;
                $entry->room_wall = $item->room_wall;
                $entry->customizations_selected = $item->customizations_selected;
                $entry->warranty_options = $item->warranty_options;
                $entry->quantity = $item->quantity;
                $entry->save();
            }

        }
        
         if($request->payment_method_type == 'credit_card'){
                
             Stripe::setApiKey(env('STRIPE_SECRET'));
            // Create a customer
            $customer = \Stripe\Customer::create(array(
                    'source' => $request->token,
                    'email' => $request->email,
                    'name' => $request->shipping_first_name.' '.$request->shipping_last_name,
                ));
        
            // Attach the payment method to the customer
            $paymentMethod = $this->createPaymentMethod($request->token);
            $paymentMethod->attach(['customer' => $customer->id]);
        
            // Create a PaymentIntent with the customer and payment method
            $paymentIntent = PaymentIntent::create([
                'amount' => number_format((float)$orderAmount, 2, '.', ''),
                'currency' => 'usd',
                'customer' => $customer->id,
                'payment_method' => $paymentMethod->id,
                'confirmation_method' => 'manual',
                'confirm' => true,
            ]);
            
            // Retrieve the client secret
            $clientSecret = $paymentIntent->client_secret;
            if($paymentIntent->status == 'successed'){
                    $order->payment_client_id =  $customer->id;
                    $order->payment_transection_id = $paymentIntent->id;
                    $order->save();
            }
        }
             if ($request->has('create_account')) {
                 
                if ($request->create_account != 0) {
                    if (User::where('email', $request->email)->exists()) {
                        return response()->json([
                            'status' => true,
                            'order_id' => $order,
                            'message' => 'Order submitted successfully. The provided email is already associated with an existing account. Please log in with this email or use a different one to create a new account.',
                        ], 200);
                    }
                    
                    $user = User::create([
                        'email' => $request->email,
                        'password' => $request->password,
                        ]);
                        
                    if($user !== null && $user !== ''){
                        // attach user id with current order
                        $order->user_id = $user->id;
                        $order->save();
                        // add shipping & billing info to user account used in order 
                        $user->shipping_first_name =  $request->shipping_first_name;
                        $user->shipping_last_name =  $request->shipping_last_name;
                        $user->shipping_street_address =  $request->shipping_address;
                        $user->shipping_apt =  $request->shipping_apt;
                        $user->shipping_city =  $request->shipping_city;
                        $user->shipping_state =  $request->shipping_state;
                        $user->shipping_zip_code =  $request->shipping_zipcode;
                         if($request->is_billing_address_same == 0){
                            $user->billing_first_name =  $request->billing_first_name;
                            $user->billing_last_name =  $request->billing_last_name;
                            $user->billing_street_address =  $request->billing_address;
                            $user->billing_apt =  $request->billing_apt;
                            $user->billing_city =  $request->billing_city;
                            $user->billing_state =  $request->billing_state;
                            $user->billing_zip_code =  $request->billing_zipcode;
                        }else{
                            $user->billing_first_name =  $request->shipping_first_name;
                            $user->billing_last_name =  $request->shipping_last_name;
                            $user->billing_street_address =  $request->shipping_address;
                            $user->billing_apt =  $request->shipping_apt;
                            $user->billing_city =  $request->shipping_city;
                            $user->billing_state =  $request->shipping_state;
                            $user->billing_zip_code =  $request->shipping_zipcode;
                        }
                        $user->save();
                    }
                     return response()->json([
                        'status' => true,
                        'order_id' => $order,
                        'message' => 'Order submitted successfully. Your account has been created. You can now log in using your credentials.',
                    ], 200);
                }
            }
                    
       $data = [
            'order' => $order,
            'order_items' => $request->order_items,
            'shipping_info' => [
                'first_name' => $request->shipping_first_name,
                'last_name' => $request->shipping_last_name,
                'address' => $request->shipping_address,
                'apt' => $request->shipping_apt,
                'city' => $request->shipping_city,
                'state' => $request->shipping_state,
                'zipcode' => $request->shipping_zipcode,
            ],
            'billing_info' => $request->is_billing_address_same == 0 ? [
                'first_name' => $request->billing_first_name,
                'last_name' => $request->billing_last_name,
                'address' => $request->billing_address,
                'apt' => $request->billing_apt,
                'city' => $request->billing_city,
                'state' => $request->billing_state,
                'zipcode' => $request->billing_zipcode,
            ] : null,
            'order_amount' => $orderAmount,
        ];
        
        $datamail = Mail::send('mail.sendOrderEmail', $data, function ($message) use ($request) {
            $message->to($request->email)->subject('Order Confirmation');
        });
        
        if (!$datamail) {
             return response()->json([
            'status' => false,
            'message' => 'Failed to send email.',
            ], 401);
        }
        
        return response()->json([
            'status' => true,
            'order_id' => $order,
            'message' => 'Order submitted successfully.',
        ], 200);
    }
    public function requestQuote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string|regex:/^[0-9\-\+\(\)]+$/',
            'comment' => 'sometimes|string',
            'total_price' => 'required|numeric',
            'order_items' => 'required|array',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }
        
        // calculate order amount
        $orderAmount = $request->total_price;
       $data = [
            'order_items' => $request->order_items,
            'info' => [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'comment' => $request->comment?? null,
            ],
            'order_amount' => $orderAmount,
        ];
        
        $datamail = Mail::send('mail.requestQuote', $data, function ($message) use ($request) {
            $message->to('jckretail@gmail.com')->subject('Request A Quote');
        });
        
        if (!$datamail) {
             return response()->json([
            'status' => false,
            'message' => 'Failed to send email.',
            ], 401);
        }
        
        return response()->json([
            'status' => true,
            // 'order_id' => $order,
            'message' => 'Request sent successfully.',
        ], 200);
    }   

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::find($id);
        if($order !== null){
            $response =
            [
                'status' => true,
                'data' => $order,
                'message' => 'order found'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'order not found!!'
            ];
            return response()->json($response, 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $order = Order::with('orderItem')->find($id);
        if($order !== null){
            $response =
            [
                'status' => true,
                'data' => $order,
                'message' => 'order found'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'order not found!!'
            ];
            return response()->json($response, 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'order_status' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }
    
        $order = Order::find($id);
        if($order == null || $order == ''){
             return response()->json([
                'status' => false,
                'message' => 'Order not found',
            ], 404);
        }
        $order->order_status = $request->order_status;
        $order->save();
        return response()->json([
            'status' => true,
            'data' => $order,
            'message' => 'order status updated successfully',
        ], 200);

    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::find($id);
        if($order !== null){
            $order->delete();
            $response =
            [
                'status' => true,
                'message' => 'order Removed Successfully'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'order not found!!'
            ];
            return response()->json($response, 400);
        }
    }
    
    
    private function createPaymentMethod($token)
    {
        return \Stripe\PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'token' => $token,
            ],
        ]);
    }

    
    
    
    
    
}