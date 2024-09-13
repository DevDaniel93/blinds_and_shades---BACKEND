<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;

class GeneralController extends Controller
{
    use ApiResponser;
    public function contactForm(Request$request){

        $msg = "";
        $validate = validator::make(
            $request->all(),
            [
                'username' => 'required',
                'phone' => 'required',
                'email' => 'required|email:strict',
                'budget' => 'required',
                'message' => 'required',
            ]
        );
        if ($validate->fails()) {
            $response =
                [
                    'status' => false,
                    'message' => $validate->errors()
                ];
            return response()->json($response, 400);
        }

        $data = [
            'username' => $request->username,
            'phone' => $request->phone,
            'email' => $request->email,
            'budget' => $request->budget,
            'description' => $request->message,
            'details' => [
                'title' => 'Query/Contact Form',
                'heading' => 'Query/Contact Form',
                // 'content' => $request->message,
                'WebsiteName' => 'Blinds & Shades'
            ]

        ];
        $datamail = Mail::send('mail.sendContactEmail', $data, function ($message) use ($data) {
            $message->to('devjames166@gmail.com')->subject($data['details']['heading']);
        });

        if (!$datamail) {
            $response = [
                'status' => false, 
                'message' => 'Failed to send email'
            ];
            return response()->json($response,403);
        }

        $response = [
            'status' => true,
            'message' => 'Your query has been submitted successfully, we will contact you shortly',
        ];

        return response()->json($response, 200);
    }
}
