<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;

class RoomController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
     public function index()
    {
        $rooms = Room::get();
            $response =
            [
                'status' => true,
                'data' => $rooms,
                'message' => 'All rooms'
            ];
            return response()->json($response, 200);
    }
     
    public function indexForUserside()
    {
        $rooms = Room::where('is_hidden',0)->get();
            $response =
            [
                'status' => true,
                'data' => $rooms,
                'message' => 'All rooms'
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $msg = "";
        $validate = validator::make(
            $request->all(),
            [
                'name' => 'required',
                'is_hidden' => 'required',
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
 
        // Create a new entry
        $input = $request->all();
        $room = Room::create($input);
        $msg = "room added successfully";
        $response = [
            'status' => true,
            'data' => $room,
            'message' => $msg,
        ];
 
        return response()->json($response, 200);
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
        $room = Room::find($id);
        if($room !== null){
            $response =
            [
                'status' => true,
                'data' => $room,
                'message' => 'Room found'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'Room not found !!'
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
                'name' => 'required',
                'is_hidden' => 'required',
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
 
        // $input = $request->all();
        
        $room = Room::find($id);
        if($room !== null){
            $room->name = $request->name;
            $room->is_hidden = $request->is_hidden;
            $updated = $room->update();
            if(!$updated){
                $msg = "Failed to update!";
                $response = [
                    'status' => false,
                    'data' => $room,
                    'message' => $msg,
                ];
        
                return response()->json($response, 400);        
            }

            $msg = "Room updated successfully";
            $response = [
                'status' => true,
                'data' => $room,
                'message' => $msg,
            ];
    
            return response()->json($response, 200);    
        }else{
            $response =
                [
                    'status' => false,
                    'message' => 'Room not found !!'
                ];
            return response()->json($response, 400);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $room = Room::find($id);
        if($room !== null){
            $room->delete();
            $response =
            [
                'status' => true,
                'message' => 'Room Removed Successfully'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'Room not found!!'
            ];
            return response()->json($response, 400);
        }
    }
}