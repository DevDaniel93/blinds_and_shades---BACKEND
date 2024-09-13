<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AddonOptionVariationItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;

class AddonOptionVariationItemController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    
    
    public function index()
    {
        $addonOptionsVarItems = AddonOptionVariationItem::get();
            $response =
            [
                'status' => true,
                'data' => $addonOptionsVarItems,
                'message' => 'All addon options variation items'
            ];
            return response()->json($response, 200);
    }
    
    public function indexForUserside()
    {
        $addonOptionsVarItems = AddonOptionVariationItem::where('is_hidden',0)->get();
            $response =
            [
                'status' => true,
                'data' => $addonOptionsVarItems,
                'message' => 'All addon options variation items'
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
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'price' => 'required|numeric',
            'is_hidden' => 'required|integer',
        ]);
        if ($validator->fails()) {
            $response =
                [
                    'status' => false,
                    'message' => $validator->errors()
                ];
            return response()->json($response, 400);
        }
 
        // Create a new entry
        $input = $request->all();
        $addonOption = AddonOptionVariationItem::create($input);
      
        $msg = "addon option variation item added successfully";
        $response = [
            'status' => true,
            'data' => $addonOption,
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
        $addonOption = AddonOptionVariationItem::find($id);
        if($addonOption !== null){
            $response =
            [
                'status' => true,
                'data' => $addonOption,
                'message' => 'addon option variation item found'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'addon option variation item not found!!'
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
            
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'price' => 'required|numeric',
                'is_hidden' => 'required|integer',
            ]);
            if ($validator->fails()) {
                $response =
                    [
                        'status' => false,
                        'message' => $validator->errors()
                    ];
                return response()->json($response, 400);
            }
     
            
            // $input = $request->all();
            
            $addonOption = AddonOptionVariationItem::find($id);
            if (!$addonOption) {
                return response()->json([
                    'status' => false,
                    'message' => 'addon option not found!',
                ], 404);
            }
            $addonOption->title = $request->title;
            // $addonOption->addon_option_id = $request->addon_option_id;
            $addonOption->price = $request->price;
            $addonOption->is_hidden = $request->is_hidden;
            $updated = $addonOption->update();
            if(!$updated){
                $msg = "Failed to update!";
                $response = [
                    'status' => false,
                    'data' => $addonOption,
                    'message' => $msg,
                ];
        
                return response()->json($response, 400);        
            }

            $msg = "addon option variation item updated successfully";
            $response = [
                'status' => true,
                'data' => $addonOption,
                'message' => $msg,
            ];
    
            return response()->json($response, 200);    
        
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $addonOption = AddonOptionVariationItem::find($id);
        if($addonOption !== null){
            $addonOption->delete();
            $response =
            [
                'status' => true,
                'message' => 'addon option Removed Successfully'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'addon option not found!!'
            ];
            return response()->json($response, 400);
        }
    }
}
