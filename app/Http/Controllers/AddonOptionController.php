<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Option;
use App\Models\AddonOptionVariationItem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;

class AddonOptionController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    
    
    public function index()
    {
        $options = Option::get();
        
         foreach($options as $option){
             if (is_array( $option->variables)) {
                    $parsedWarrantyOptions = array_map(function($option) {
                        if (is_string($option)) {
                            $jsonString = str_replace("'", '"', $option);
                            $decodedOption = json_decode($jsonString, true);
                            // Check if json_decode results are valid
                            if (json_last_error() === JSON_ERROR_NONE) {
                                return $decodedOption;
                            }
                        }
                        return $option; // Return the original value if not a string or decoding fails
                    },  $option->variables);
        
                     $option->variables = $parsedWarrantyOptions;
            }
        }
        
            $response =
            [
                'status' => true,
                'data' => $options,
                'message' => 'All addon options'
            ];
            return response()->json($response, 200);
    }
    
    public function indexForUserside()
    {
        $options = Option::where('is_hidden',0)->get();
        
        foreach($options as $option){
             if (is_array( $option->variables)) {
                    $parsedWarrantyOptions = array_map(function($option) {
                        if (is_string($option)) {
                            $jsonString = str_replace("'", '"', $option);
                            $decodedOption = json_decode($jsonString, true);
                            // Check if json_decode results are valid
                            if (json_last_error() === JSON_ERROR_NONE) {
                                return $decodedOption;
                            }
                        }
                        return $option; // Return the original value if not a string or decoding fails
                    },  $option->variables);
        
                     $option->variables = $parsedWarrantyOptions;
            }
        }
        
            $response =
            [
                'status' => true,
                'data' => $options,
                'message' => 'All addon options'
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
            // 'addon_id' => 'required|integer|exists:addons,id',
            'description' => 'nullable|string',
            'video_link' => 'nullable|string',
            'is_paid' => 'integer|in:0,1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg', // Adjust as needed
            'amount' => 'nullable|numeric',
            'type' => 'nullable|string', 
            'variables' => 'nullable|array', 
            'is_kid_friendly' => 'integer|in:0,1',
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
         if ($request->hasFile('image')) {
            $image = $request->file('image');
            if ($image->isValid()) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileName = time().'.'.$extension;
            $filePath = 'images/addon-options/'.$fileName;
            $image = $request->file('image')->move(public_path('images/addon-options/'), $fileName);
            $input['image'] = $filePath;
            }
         }
        
        // if($request->has('variables')){
        //     $input['variables'] = json_encode($request->variables);
        // }

        // Variables [ 
        //     - Variables Label 
        //     - Variables Description 
        //     - Variables Items [By Default [] Only use when type = Variable ] 
        //     ]
        $option = Option::create($input);
      
        $msg = "addon option added successfully";
        $response = [
            'status' => true,
            'data' => $option,
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
        $option = Option::find($id);
        if($option !== null){
            // Decode JSON attributes if they are strings
            // $option->variables = $this->decodeJsonIfNeeded($option->variables);
    
            // Ensure warranty_options is an array before further processing
            if (is_array( $option->variables)) {
                $parsedWarrantyOptions = array_map(function($option) {
                    if (is_string($option)) {
                        $jsonString = str_replace("'", '"', $option);
                        $decodedOption = json_decode($jsonString, true);
                        // Check if json_decode results are valid
                        if (json_last_error() === JSON_ERROR_NONE) {
                            return $decodedOption;
                        }
                    }
                    return $option; // Return the original value if not a string or decoding fails
                },  $option->variables);
    
                 $option->variables = $parsedWarrantyOptions;
            }
            
            $response =
            [
                'status' => true,
                'data' => $option,
                'message' => 'addon option found'
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
            $msg = "";
            
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                // 'addon_id' => 'required|integer|exists:addons,id',
                'description' => 'nullable|string',
                'video_link' => 'nullable|string',
                'is_paid' => 'integer|in:0,1',
                // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg', // Adjust as needed
                'amount' => 'nullable|numeric',
                'type' => 'nullable|string', 
                'variables' => 'nullable|array', 
                'is_kid_friendly' => 'integer|in:0,1',
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
            
            $option = Option::find($id);
            if (!$option) {
                return response()->json([
                    'status' => false,
                    'message' => 'addon option not found!',
                ], 404);
            }
            
            $option->title = $request->title;
            // $option->addon_id = $request->addon_id;
            $option->description = $request->description;
            $option->video_link = $request->video_link;
            $option->is_paid = $request->is_paid;
            $option->amount = $request->amount;
            $option->type = $request->type;
            $option->variables = $request->variables;
            $option->is_kid_friendly = $request->is_kid_friendly;
            $option->is_hidden = $request->is_hidden;
            
             // Handle image
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                if ($image->isValid()) {
                    if ($option->image && File::exists(public_html($option->image))) {
                        File::delete(public_html($option->image));
                    }
                    // Store new image
                    $fileName = time().'.'.$image->getClientOriginalName();
                    $filePath = 'images/addon-options/'.$fileName;
                    $image->move(public_html('images/addon-options/'), $fileName);
                    $option->image = $filePath;
                }
            }
            
            $updated = $option->update();
            if(!$updated){
                $msg = "Failed to update!";
                $response = [
                    'status' => false,
                    'data' => $option,
                    'message' => $msg,
                ];
        
                return response()->json($response, 400);        
            }

            $msg = "addon option updated successfully";
            $response = [
                'status' => true,
                'data' => $option,
                'message' => $msg,
            ];
    
            return response()->json($response, 200);    
        
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $option = Option::find($id);
        if($option !== null){
            $option->delete();
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
    //  private function decodeJsonIfNeeded($value) {
    //     if (is_string($value)) {
    //         $decoded = json_decode($value, true);
    //         // Check if json_decode did not fail and result is an array
    //         if (json_last_error() === JSON_ERROR_NONE) {
    //             return $decoded;
    //         }
    //     }
    //     // Return the original value if not a string or decoding fails
    //     return $value;
    // }
}
