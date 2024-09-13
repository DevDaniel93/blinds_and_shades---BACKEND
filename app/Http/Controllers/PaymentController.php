<?php
namespace App\Http\Controllers;

use Stripe\Stripe;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
    //   dd($request->all());
         $validator = Validator::make($request->all(), [
           'token' => 'required',
           'email' => 'required',
           'name' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }
    
        Stripe::setApiKey(env('STRIPE_SECRET'));
    
        // Create a customer
        $customer = \Stripe\Customer::create(array(
                'source' => $request->token,
                'email' => $request->email,
                'name' => $request->name,
            ));
    
        // Attach the payment method to the customer
        $paymentMethod = $this->createPaymentMethod($request->token);
        $paymentMethod->attach(['customer' => $customer->id]);
    
        // Create a PaymentIntent with the customer and payment method
        $paymentIntent = PaymentIntent::create([
            'amount' => 1000, // Amount in cents
            'currency' => 'usd',
            'customer' => $customer->id,
            'payment_method' => $paymentMethod->id,
            'confirmation_method' => 'manual',
            'confirm' => true,
        ]);
        
        // Retrieve the client secret
        $clientSecret = $paymentIntent->client_secret;
        // if($paymentIntent->status == 'successed'){
        //     $customer->id;    
        //     $paymentIntent->status;
        // }
        
    
        return response()->json([
            'status' => true,
            'client_secret' => $clientSecret,
            ],200);
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


// without token start

// class PaymentController extends Controller
// {
    /** Pay order via stripe */
    // public function payByStripe(){
    //     Stripe::setApiKey(env('STRIPE_SECRET'));
    //     try {
    //         // retrieve JSON from POST body
    //         // $jsonStr = file_get_contents('php://input');
    //         // $jsonObj = json_decode($jsonStr);

    //         // Create a PaymentIntent with amount and currency
    //         $paymentIntent = \Stripe\PaymentIntent::create([
    //             // 'amount' => $this->calculateOrderAmount($jsonObj->items),
    //             'amount' => 500,
    //             'currency' => 'usd',
    //             'description' => 'Blinds & Shades',
    //             'setup_future_usage' => 'on_session'
    //         ]);
            
    //         $output = [
    //             'clientSecret' => $paymentIntent->client_secret,
    //         ];
    //         return response()->json($output);
            
    //     } catch (ErrorException $e) {
    //         return response()->json(['error' => $e->getMessage()]);
    //     }
    // }

    /** Calculate order total for stripe */
    // public function calculateOrderAmount(array $items): int {
    //     // Replace this constant with a calculation of the order's amount
    //     // Calculate the order total on the server to prevent
    //     // people from directly manipulating the amount on the client
    //     foreach($items as $item){
    //         return $item->amount * 100;
    //     }
    // }

// without token end
    
    
}

                                                            
                                                        