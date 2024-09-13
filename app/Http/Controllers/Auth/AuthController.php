<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\ForgetOtp;
use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\SimpleEmail;
use App\Enums\TokenAbility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    use ApiResponser;
    public function register(Request $request)
    {
        $msg = "";
        $validate = validator::make(
            $request->all(),
            [
                'email' => 'required|unique:users,email',
                'password' => 'required|confirmed'
            ]
        );
        if ($validate->fails()) {
            $response =
                [
                    'success' => false,
                    'message' => $validate->errors()
                ];
            return response()->json($response, 401);
        }
 
 
        // Create a new user
        $input = $request->all();
        $input['password'] = $input['password'];
        $input['user_role'] = 2;
        // $input['mana_currency'] = 0;
        // $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        // $customer = $stripe->customers->create(array(
 
        //     "email" => $request->email,
 
        //     "name" => $request->name,
 
        // ));
 
        // dd($customer->id);
        // $input['stripe_id'] = $customer->id;
        $user = User::create($input);
 
        // $success['accessToken'] = $user->createToken('Blinds & Shades')->plainTextToken;
        $accessToken = $user->createToken('accessToken', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
        $refreshToken = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.rt_expiration')));
        
        $success['accessToken'] = $accessToken->plainTextToken;
        $success['refreshToken'] = $refreshToken->plainTextToken;
        // $success['name'] = $user->name;
 
        $msg = "User Registered Successfully";
 
 
 
        $response = [
            'success' => true,
            'data' => $success,
            'message' => $msg,
        ];
 
        return response()->json($response, 200);
    }
 
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if($user == null){
           $response = [
                'success' => false,
                'message' => 'User Not Found!!'
            ];
            return response()->json($response, 401); 
        }
        $msg = "";  
        if ($user->user_role == 1) {
            $msg = "Admin Login SuccessFully";
        } else if ($user->user_role == 2) {
            $msg = "User Login SuccessFully";
        }
        if (!Auth::attempt([
            'email'=>$request->email, 
            'password'=>$request->password
            ])) {
            $response = [
                'success' => false,
                'message' => 'Incorrect Username or Password !!'
            ];
            return response()->json($response, 401);
        }
 
        // Authentication successful
        // $success['accessToken'] = $user->createToken('Blinds & Shades')->plainTextToken;
        $accessToken = $user->createToken('accessToken', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
        $refreshToken = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.rt_expiration')));
        
        $success['accessToken'] = $accessToken->plainTextToken;
        $success['refreshToken'] = $refreshToken->plainTextToken;
        $success['email'] = $user->email;
        // $success['name'] = $user->name;
 
        // $this->tokencheck($success['accessToken']);
 
        $response = [
            'success' => true,
            'data' => $success,
            'message' => $msg,
        ];
 
        return response()->json($response, 200);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        return $this->success(null, 'Logout Successfully');
    }

    public function forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return Response(['status' => false, 'error' => $validator->errors()], 401);
        }

        $opt = rand(1000, 9990);

        // dd($opt);
        // $currentDate = Carbon::now()->format('d-M-Y');

        $check_user = User::where('email', $request->email)->select('id', 'email')->first();
        // dd($check_user);
        if (!isset($check_user)) {
            return response()->json(['status' => false, 'message' => "User not found"],401);
        }

        ForgetOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'email'     => $request->email,
                'otp'      => $opt
            ]
        );

        $data = [
            'email' => $request->email,
            'user' => $check_user,
            'details' => [
                'heading' => 'Forget Password Opt',
                'content' => 'Your forget password otp : ' . $opt,
                'WebsiteName' => 'Blinds and shades'
            ]

        ];
        $datamail = Mail::send('mail.sendopt', $data, function ($message) use ($data) {
            $message->to($data['email'])->subject($data['details']['heading']);
        });

        if (!$datamail) {
            return response()->json(['status' => false, 'message' => 'Failed to send email'],401);
        }


        return response()->json(['status' => true, 'data' => $check_user, 'message' => "OTP send on your email address"]);
    }
    public function otp_verification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return Response(['status' => false, 'error' => $validator->errors()], 401);
        }

        $user = ForgetOtp::where(['email' => $request->email, 'otp' => $request->otp])->first();
        if (!isset($user)) {
            return response()->json(['status' => false, 'message' => "Otp is wrong"],401);
        }
        $data['email'] = $user->email;
        $data['code'] = $user->otp;

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return Response(['status' => false, 'error' => $validator->errors()], 401);
        }

        // dd(uniqid());
        $get_otp = ForgetOtp::where(['email' => $request->email, 'otp' => $request->otp])->first();
        if (!isset($get_otp)) {
            return response()->json(['status' => false, 'message' => "Otp is wrong"],401);
        } else {
            $get_otp->delete();
        }
        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request['password']);

        if ($user->save()) {
            return response()->json(['status' => true, 'message' => "Password Reset"]);
        }
    }

    public function checkLogin(){
        if(Auth::check()){
            return response()->json(['status' => true, 'message' => "Loged in"]);
        }else{
            return response()->json(['status' => true, 'message' => "Loged out"]);
        }
    }
    
    // public function refreshToken(Request $request)
    // {
    //     // if (!auth()->user()->tokenCan(TokenAbility::ISSUE_ACCESS_TOKEN->value)) {
    //     //     return response()->json(['status' => false,'message' => "Invalid reset token provided"]);
    //     // }
    //     $accessToken = $request->user()->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
    //     return response()->json(['status' => true,'message' => "Token generated", 'accessToken' => $accessToken->plainTextToken]);
    // }
    
    public function refreshToken(Request $request)
    {
        // Retrieve the current user
        $user = $request->user();
    
        // Retrieve the provided token from the request
        $token = $request->bearerToken(); 
    
        // Split the token to get the token ID
        $tokenId = explode('|', $token)[0];
    
        // Fetch the token record from the database
        $tokenRecord = \DB::table('personal_access_tokens')->where('id', $tokenId)->first();
    
        if (!$tokenRecord) {
            return response()->json(['status' => false, 'message' => 'Invalid token provided'], 403);
        }
    
        // Check if the token has expired
        if ($tokenRecord->expires_at && Carbon::parse($tokenRecord->expires_at)->isPast()) {
            return response()->json(['status' => false, 'message' => 'Token has expired'], 403);
        }
    
        // Check if the user has the correct ability
        if (!$user->tokenCan(TokenAbility::ISSUE_ACCESS_TOKEN->value)) {
            return response()->json(['status' => false, 'message' => 'Invalid reset token provided, You do not have ability to reset token'], 403);
        }
    
        // Generate a new access token
        $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
    
        return response()->json([
            'status' => true,
            'message' => 'Token generated successfully',
            'accessToken' => $accessToken->plainTextToken
        ]);
    }


    public function notLogin(Request $request) {
        
        return Response()->json(["status" => false, 'message' => 'Unauthorized!!'],401);
    }
    
    // public function resetPassword(Request $request){
    //     validator = Validator::make($request->all(), [
    //         'old_password' => 'required',
    //         'password' => 'required',
    //         'password_confirmation' => 'required|confirmed', //checking the password and password_confirmation field are same 
    //     ]);

    //     if ($validator->fails()) {
    //         return Response(['status' => false, 'error' => $validator->errors()], 401);
    //     }
    //     // checking user set the correct user password
    //     $user = User::where('email', $request->email)->first();
    //     if($user !== null){
    //         if(!Hash::check($user->password,$request->old_password)){
    //              return response()->json(['status' => false, 'message' => "provided old password is wrong"]);
    //         }
    //     }else{
    //         return response()->json(['status' => false, 'message' => "user not found"]);
    //     }
        
    //     $user->password = $request->password;  //already casted as 'password' => 'hashed' in model so don't need to make has etc

    //     if ($user->save()) {
    //         return response()->json(['status' => true, 'message' => "Password Updated Successfully"]);
    //     }
    // }
    public function resetPassword(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email', // Ensure email is provided and valid
            'old_password' => 'required',
            'password' => 'required', // Optionally enforce a minimum password length
            'password_confirmation' => 'required|same:password', // Ensures passwords match
        ]);
    
        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 401);
        }
    
        // Retrieve the user based on the email
        $user = User::where('email', $request->email)->first();
    
        if ($user === null) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }
    
        // Check if the old password is correct
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['status' => false, 'message' => 'Provided old password is incorrect'], 401);
        }
    
        // Update the password (make sure to hash it before saving)
        // $user->password = Hash::make($request->password);
        $user->password = $request->password;
    
        // Save the user and handle possible errors
        if ($user->save()) {
            return response()->json(['status' => true, 'message' => 'Password updated successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to update password'], 500);
        }
    }


}
