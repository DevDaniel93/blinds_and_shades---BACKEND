<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Addon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;

class AddonController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addons = Addon::get();
      
        foreach($addons as $addon){
            if(count($addon->addonOptions)>0){
                $options=[];
                foreach($addon->addonOptions as $addonOption){
                    $options[] = $addonOption->option;
                }
                $addon->options = $options;
            }
        }
            $response =
            [
                'status' => true,
                'data' => $addons,
                'message' => 'All addons'
            ];
            return response()->json($response, 200);
    }
     
    public function indexForUserside()
    {
        $addons = Addon::where('is_hidden',0)->get();
         foreach($addons as $addon){
            if(count($addon->addonOptions)>0){
                $options=[];
                foreach($addon->addonOptions as $addonOption){
                    $options[] = $addonOption->option;
                }
                $addon->options = $options;
            }
        }
            $response =
            [
                'status' => true,
                'data' => $addons,
                'message' => 'All addons'
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
                'title' => 'required|string',
                'description' => 'nullable|string',
                'options_id' => 'sometimes|array',
                'options_id.*' => 'sometimes|integer|exists:options,id',
                'is_hidden' => 'required|integer',
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
        $addon = Addon::create($input);
        if($request->has('options_id') || count($request->options_id)>0){
            foreach($request->options_id as $optionId){
                \App\Models\AddonOption::create([
                    'addon_id' => $addon->id,
                    'option_id' => $optionId,
                    ]);    
            }
        }
        $msg = "addon added successfully";
        $response = [
            'status' => true,
            'data' => $addon,
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
        $addon = Addon::find($id);
        if($addon !== null){
            if(count($addon->addonOptions)>0){
                $options=[];
                foreach($addon->addonOptions as $addonOption){
                    $options[] = $addonOption->option;
                }
                $addon->options = $options;
            }
            $response =
            [
                'status' => true,
                'data' => $addon,
                'message' => 'addon found'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'addon not found!!'
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
                'title' => 'required|string',
                'description' => 'nullable|string',
                'options_id' => 'sometimes|array',
                'options_id.*' => 'sometimes|integer|exists:options,id',
                'is_hidden' => 'required|integer',
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
        
        $addon = Addon::find($id);
        if($addon !== null){
            $addon->title = $request->title;
            if($request->has('description')){
                $addon->description = $request->description;    
            }
            $addon->is_hidden = $request->is_hidden;
            $updated = $addon->update();
            
            if(!$updated){
                $msg = "Failed to update!";
                $response = [
                    'status' => false,
                    'data' => $addon,
                    'message' => $msg,
                ];
        
                return response()->json($response, 400);        
            }
            // remove previous records 
             \App\Models\AddonOption::where('addon_id',$addon->id)->delete();
            //  add new record
            if($request->has('options_id') || count($request->options_id)>0){
                foreach($request->options_id as $optionId){
                    \App\Models\AddonOption::create([
                        'addon_id' => $addon->id,
                        'option_id' => $optionId,
                        ]);    
                }
            }
            $msg = "Addon updated successfully";
            $response = [
                'status' => true,
                'data' => $addon,
                'message' => $msg,
            ];
    
            return response()->json($response, 200);    
        }else{
            $response =
                [
                    'status' => false,
                    'message' => 'Addon not found !!'
                ];
            return response()->json($response, 400);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $addon = Addon::find($id);
        if($addon !== null){
            $addon->delete();
            $response =
            [
                'status' => true,
                'message' => 'Addon Removed Successfully'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'addon not found!!'
            ];
            return response()->json($response, 400);
        }
    }
}
