<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;
// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\File;

class ColorController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colors = Color::get();
            $response =
            [
                'status' => true,
                'data' => $colors,
                'message' => 'All colors'
            ];
            return response()->json($response, 200);
    }
     
    public function indexForUserside()
    {
        $colors = Color::where('is_hidden',0)->get();
            $response =
            [
                'status' => true,
                'data' => $colors,
                'message' => 'All colors'
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
        // Validate input
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'primary_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp', //max:2048
            'is_hidden' => 'required',
             'variations' => 'nullable|array',
            'variations.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',  // validation for each variation
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }
    
        // Handle primary image
        $input = $request->all();
     
        if ($request->hasFile('primary_image')) {
                $primaryImage = $request->file('primary_image');
                $fileName = time() . '_' . uniqid() . '.' . $primaryImage->getClientOriginalExtension();
                $filePath = 'images/color-images/' . $fileName;
                $primaryImage->move(public_path('images/color-images'), $fileName);
                $input['primary_image'] = $filePath;
            }
    
         // Handle variations
            $variations = [];
            if ($request->hasFile('variations')) {
                foreach ($request->file('variations') as $file) {
                    if ($file->isValid()) {
                        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $filePath = 'images/color-images/' . $fileName;
                        $file->move(public_path('images/color-images'), $fileName);
                        $variations[] = $filePath;
                    }
                }
                $input['variations'] = $variations;
            }

    
        // Create new Color entry
        $color = Color::create($input);
    
        return response()->json([
            'status' => true,
            'data' => $color,
            'message' => 'Color added successfully',
        ], 200);
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
        $color = Color::find($id);
        if($color !== null){
            $response =
            [
                'status' => true,
                'data' => $color,
                'message' => 'color found'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'color not found!!'
            ];
            return response()->json($response, 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'is_hidden' => 'required',
            // 'primary_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp', // max:2048
            'variations' => 'nullable|array',
            // 'variations.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }
    
        $color = Color::find($id);
        if($color !== null){
        // Update fields
        $color->title = $request->input('title');
        $color->variations = $request->variations;
        
        $color->is_hidden = $request->input('is_hidden');
    
        // Handle primary image
        if ($request->hasFile('primary_image')) {
            $primaryImage = $request->file('primary_image');
            if ($primaryImage->isValid()) {
                // Remove old image if necessary
                // if ($color->primary_image && Storage::disk('public')->exists($color->primary_image)) {
                //     Storage::disk('public')->delete($color->primary_image);
                // }
                if ($color->primary_image && File::exists(public_path($color->primary_image))) {
                    // Delete the existing image
                    File::delete(public_path($color->primary_image));
                }
                // Store new image
                $fileName = time() . '.' . $primaryImage->getClientOriginalName();
                $filePath = 'images/color-images/' . $fileName;
                $primaryImage->move(public_path('images/color-images'), $fileName);
                $color->primary_image = $filePath;
            }
        }
    
        // Handle variations
        // $variations = [];
        // if ($request->hasFile('variations')) {
        //     foreach ($request->file('variations') as $file) {
        //         if ($file->isValid()) {
        //             $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        //             $filePath = 'images/color-images/' . $fileName;
        //             $file->move(public_path('images/color-images'), $fileName);
        //             $variations[] = $filePath;
        //         }
        //     }
        //     $color->variations = $variations;
        // }
    
        // Save the updated color entry
        $color->save();
    
        return response()->json([
            'status' => true,
            'data' => $color,
            'message' => 'Color updated successfully',
        ], 200);
        }
        
        
        return response()->json([
            'status' => false,
            'message' => 'Color Not Found',
        ], 404);
    }
    
        public function deletedImages(Request $request,$id){
            // Validate input
            $validator = Validator::make($request->all(), [
                'images' => 'required|array',
            ]);
        
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()
                ], 400);
            }
            
            $color = Color::find($id);
            if($color == null || $color == ''){
               return response()->json([
                    'status' => false,
                    'message' => 'Color not found !!',
                ], 400); 
            }
                $images = $request->images;
                if (json_last_error() === JSON_ERROR_NONE && is_array($images) && !empty($images)) {
                    $color->variations = array_filter($color->variations, function ($variation) use ($images) {
                        return !in_array($variation, $images, true);
                    });
                    $color->save();
                }
        
                return response()->json([
                    'status' => true,
                    'data' => $color,
                    'message' => 'Color Images updated successfully',
                ], 200);
        
        }
        
        public function uploadImages(Request $request,$id){
            // Validate input
            $validator = Validator::make($request->all(), [
                'images' => 'required|array',
                'images.*' => 'file|mimes:jpeg,png,jpg,gif,webp,svg',
            ]);
        
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()
                ], 400);
            }
            $color = Color::find($id);
            if($color == null || $color == ''){
               return response()->json([
                    'status' => false,
                    'message' => 'Color not found !!',
                ], 400); 
            }
            
                $filePaths = [];
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $file) {
                        if ($file->isValid()) {
                            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                            $filePath = 'images/color-images/' . $fileName;
                            $file->move(public_path('images/color-images'), $fileName);
                            $filePaths[] = $filePath;
                        }
                    }
            
                    $color->variations = array_merge($color->variations ?? [], $filePaths);
                    $color->save();
                }

        
                return response()->json([
                    'status' => true,
                    'data' => $color,
                    'message' => 'Color Images added successfully',
                ], 200);
        
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $color = Color::find($id);
        if($color !== null){
            $color->delete();
            $response =
            [
                'status' => true,
                'message' => 'color Removed Successfully'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'color not found!!'
            ];
            return response()->json($response, 400);
        }
    }
    
    static function getColorDetails($id){
        $color = Color::find($id);
        return $color? $color : null;
    }
}