<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;

class UserController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::get();
            $response =
            [
                'status' => true,
                'data' => $users,
                'message' => 'All users'
            ];
            return response()->json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::find($id);
        if($user !== null){
            $response =
            [
                'status' => true,
                'data' => $user,
                'message' => 'user found'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'user not found!!'
            ];
            return response()->json($response, 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $msg = "";
        $validate = validator::make(
            $request->all(),
            [
                'email' => 'required|email:strict'
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
 
        $input = $request->all();
        
        $user = User::find($id);
        if($user !== null){
            if($request->hasFile('image')){
                if($request->file('image') !== null && $request->file('image') !== ''){
                    $reqImage = $request->file('image');
                    if ($reqImage->isValid()) {
                        $extension = $request->file('image')->getClientOriginalExtension();
                        $fileName = time().'.'.$extension;
                        $imagePath = 'images/users-profile/'.$fileName;
                        $request->file('image')->move(public_path('images/users-profile/'), $fileName);
                        $input['image'] = $imagePath;
                    }
                }
            }else{
                $input['image'] = $user->image;
            }
            $updated = $user->update($input);
            if(!$updated){
                $msg = "Failed to update!";
                $response = [
                    'status' => false,
                    'data' => $user,
                    'message' => $msg,
                ];
        
                return response()->json($response, 400);        
            }

            $msg = "User updated successfully";
            $response = [
                'status' => true,
                'data' => $user,
                'message' => $msg,
            ];
    
            return response()->json($response, 200);    
        }else{
            $response =
                [
                    'status' => false,
                    'message' => 'user not found !!'
                ];
            return response()->json($response, 400);
        }
        
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if($user !== null){
            $user->delete();
            $response =
            [
                'status' => true,
                'message' => 'user Removed Successfully'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'user not found!!'
            ];
            return response()->json($response, 400);
        }
    }
}
