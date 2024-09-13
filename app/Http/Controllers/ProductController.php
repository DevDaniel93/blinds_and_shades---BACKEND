<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductAddon;
use App\Models\ProductColor;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;

class ProductController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    
    
    public function index()
    {
        $products = Product::with(['reviews','color','category','addon'])->get();
            $response =
            [
                'status' => true,
                'data' => $products,
                'message' => 'All products'
            ];
            return response()->json($response, 200);
    }
    
    public function indexForUserside()
    {
        $products = Product::with(['reviews','color','category','addon'])->where('is_hidden',0)->get();
            $response =
            [
                'status' => true,
                'data' => $products,
                'message' => 'All products'
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
            'name' => 'required|string|max:255',
            'short_desc' => 'nullable|string',
            'long_desc' => 'nullable|string',
            'shipping_desc' => 'nullable|string',
            'is_kid_friendly' => 'nullable|boolean',
            'measuring_protection_guarantee' => 'nullable|integer',
            'shipping' => 'nullable|integer',
            'in_stock' => 'nullable|integer',
            'stock_value' => 'nullable|integer',
            'warranty_options' => 'nullable|array',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp', // Adjust as needed
            // 'video' => 'nullable|mimes:mp4,mov,avi,wmv',
            'videos' => 'nullable|array', 
            'price' => 'required|numeric',
            'color' => 'nullable|array', 
            'addon' => 'nullable|array', 
            'category.*' => 'nullable|exists:categories,id', 
            'width_min' => 'required|integer|min:0',
            'width_max' => 'required|integer|min:0|gte:width_min', // Greater than or equal to width_min
            'height_min' => 'required|integer|min:0',
            'height_max' => 'required|integer|min:0|gte:height_min', // Greater than or equal to height_min
            'is_hidden' => 'required|boolean',
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
        $extension = $request->file('image')->getClientOriginalExtension();
        $fileName = time().'.'.$extension;
        $filePath = 'images/products/'.$fileName;
        $image = $request->file('image')->move(public_path('images/products/'), $fileName);
        $input['image'] = $filePath;
         
        // if($request->warranty_options !== null && $request->warranty_options !== ''){
        //     $input['warranty_options'] = json_encode($request->warranty_options);    
        // }
        // if($request->videos !== null && $request->videos !== ''){
        //         $input['videos'] = json_encode($request->videos);    
        // }
        
         // Process multiple video files if present
            // if ($request->hasFile('videos')) {
            //     $videos = $request->file('videos');
            //     $videoFileNames = [];
        
            //     foreach ($videos as $video) {
            //         $videoExtension = $video->getClientOriginalExtension();
            //         $videoFileName = time() . '-' . uniqid() . '.' . $videoExtension; // Ensure unique filenames
            //         $video->move('videos/product-videos/', $videoFileName);
            //         $videoFileNames[] = $videoFileName; // Store filenames in an array
            //     }
        
            //     $input['videos'] = json_encode($videoFileNames); // Store array of filenames as JSON
            // }
        
        
        $product = Product::create($input);
        if($product){
             // add here color,category,addon using created entry id $product->id
            if($request->has('category') && count($request->category?? [])){
                foreach($request->category as $catId){
                    ProductCategory::create([
                        'category_id' => $catId,
                        'product_id' => $product->id,
                        ]);
                }
            }
            if($request->has('color') && count($request->color?? [])){
                foreach($request->color as $colorId){
                    ProductColor::create([
                        'color_id' => $colorId,
                        'product_id' => $product->id,
                        ]);
                }
            }
            if($request->has('addon') && count($request->addon?? [])){
                foreach($request->addon as $addonId){
                    ProductAddon::create([
                        'addon_id' => $addonId,
                        'product_id' => $product->id,
                        ]);
                }
            }
        }
        $msg = "product added successfully";
        $response = [
            'status' => true,
            'data' => $product,
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
public function edit(string $id)
{
    // Fetch the product with its related data
    $product = Product::with(['reviews', 'color','category','addon'])
        ->where('id', $id)
        ->first();

    if ($product !== null) {
        // // Decode JSON attributes if they are strings
        // $product->warranty_options = $this->decodeJsonIfNeeded($product->warranty_options);
        // $product->videos = $this->decodeJsonIfNeeded($product->videos);
    
    
            foreach($product->addon as $addon){
                    foreach($addon->addon->addonOptions as $addonOptions){
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
                                }, $addonOptions->variables?? []);
                    
                                $addonOptions->variables = $parsedWarrantyOptions;
                    }
            }
         // Ensure warranty_options is an array before further processing
        if (is_array($product->warranty_options)) {
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
            }, $product->warranty_options);

            $product->warranty_options = $parsedWarrantyOptions;
        }

        $response = [
            'status' => true,
            'data' => $product,
            'message' => 'Product found'
        ];
        return response()->json($response, 200);
    } else {
        $response = [
            'status' => false,
            'message' => 'Product not found!!'
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
                'name' => 'required|string|max:255',
                'short_desc' => 'nullable|string',
                'long_desc' => 'nullable|string',
                'shipping_desc' => 'nullable|string',
                'is_kid_friendly' => 'nullable|boolean',
                'measuring_protection_guarantee' => 'nullable|integer',
                'shipping' => 'nullable|integer',
                'in_stock' => 'nullable|integer',
                'stock_value' => 'nullable|integer',
                'warranty_options' => 'nullable|array',
                // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp', // Adjust as needed
                // 'video' => 'nullable|mimes:mp4,mov,avi,wmv',
                'videos' => 'nullable|array', 
                'price' => 'required|numeric',
                'color' => 'nullable|array', 
                'addon' => 'nullable|array', 
                // 'category' => 'nullable|array',
                 'category.*' => 'nullable|exists:categories,id', 
                'width_min' => 'required|integer|min:0',
                'width_max' => 'required|integer|min:0|gte:width_min', // Greater than or equal to width_min
                'height_min' => 'required|integer|min:0',
                'height_max' => 'required|integer|min:0|gte:height_min', // Greater than or equal to height_min
                'is_hidden' => 'required|boolean',
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
            
            $product = Product::find($id);
            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found!',
                ], 404);
            }

            $product->name = $request->name;
            $product->short_desc = $request->short_desc;
            $product->long_desc = $request->long_desc;
            $product->is_hidden = $request->is_hidden;
            $product->price = $request->price;
            $product->shipping_desc = $request->shipping_desc;
            $product->is_kid_friendly = $request->is_kid_friendly;
            $product->measuring_protection_guarantee = $request->measuring_protection_guarantee;
            $product->shipping = $request->shipping;
            $product->in_stock = $request->in_stock;
            $product->stock_value = $request->stock_value;
            $product->width_min = $request->width_min;
            $product->width_max = $request->width_max;
            $product->height_min = $request->height_min;
            $product->height_max = $request->height_max;
            $product->warranty_options = $request->warranty_options;   
            $product->videos = $request->videos;  
            
            // if($request->warranty_options !== null && $request->warranty_options !== ''){
            //     $product->warranty_options = json_encode($request->warranty_options);    
            // }
                 
             // Handle image
            if ($request->hasFile('image')) {
                if ($request->file('image')->isValid()) {
                    if ($product->image && File::exists(public_path($product->image))) {
                        // delete the existing image
                        File::delete(public_path($product->image));
                    }
                    
                    // Store new image
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $fileName = time().'.'.$extension;
                    $filePath = 'images/products/'.$fileName;
                    $image = $request->file('image')->move(public_path('images/products/'), $fileName);
                    $product->image = $filePath;
                }
            }
            
            // if ($request->hasFile('videos')) {
            //     $videos = $request->file('videos');
            //     $videoFileNames = [];
        
            //     foreach ($videos as $video) {
            //         $videoExtension = $video->getClientOriginalExtension();
            //         $videoFileName = time() . '-' . uniqid() . '.' . $videoExtension; // Ensure unique filenames
            //         $video->move('videos/product-videos/', $videoFileName);
            //         $videoFileNames[] = $videoFileName; 
            //     }
            //     $product->videos = json_encode($videoFileNames); 
            // }
            
            // if($request->videos !== null && $request->videos !== ''){
            //     $product->videos = json_encode($request->videos);    
            // }
                  
            
            
            $updated = $product->update();
            if($updated){
                // remove prevoius product category
                ProductCategory::where('product_id', $product->id)->delete();
                // remove prevoius product color
                ProductColor::where('product_id', $product->id)->delete();
                // remove prevoius product addon
                ProductAddon::where('product_id', $product->id)->delete();
                
                 // add new color,category,addon using created entry id $product->id
                if($request->has('category') && count($request->category?? [])){
                    foreach($request->category as $catId){
                        ProductCategory::create([
                            'category_id' => $catId,
                            'product_id' => $product->id,
                            ]);
                    }
                }
              
                if($request->has('color') && count($request->color?? [])){
                    foreach($request->color as $colorId){
                        ProductColor::create([
                            'color_id' => $colorId,
                            'product_id' => $product->id,
                            ]);
                    }
                }
                if($request->has('addon') && count($request->addon?? [])){
                    foreach($request->addon as $addonId){
                        ProductAddon::create([
                            'addon_id' => $addonId,
                            'product_id' => $product->id,
                            ]);
                    }
                }
            }
            if(!$updated){
                $msg = "Failed to update!";
                $response = [
                    'status' => false,
                    // 'data' => $product,
                    'message' => $msg,
                ];
        
                return response()->json($response, 400);        
            }

            $msg = "product updated successfully";
            $response = [
                'status' => true,
                // 'data' => $product,
                'message' => $msg,
            ];
    
            return response()->json($response, 200);    
        
        
    }
    
    public function prodcutDetails(string $id)
    {
        // Fetch the product with its related data
        $product = Product::with(['reviews', 'color','category','addon'])
            ->where('id', $id)
            ->where('is_hidden',0)
            ->first();
    
        if ($product !== null) {
            // Decode JSON attributes if they are strings
            // $product->warranty_options = $this->decodeJsonIfNeeded($product->warranty_options);
            // $product->videos = $this->decodeJsonIfNeeded($product->videos);
            
            foreach($product->addon as $addon){
                    foreach($addon->addon->addonOptions as $addonOptions){
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
                                }, $addonOptions->variables?? []);
                    
                                $addonOptions->variables = $parsedWarrantyOptions;
                    }
            }
            
             // Ensure warranty_options is an array before further processing
            if (is_array($product->warranty_options)) {
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
                }, $product->warranty_options);
    
                $product->warranty_options = $parsedWarrantyOptions;
            }
    
            $response = [
                'status' => true,
                'data' => $product,
                'message' => 'Product found'
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => 'Product not found!!'
            ];
            return response()->json($response, 400);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if($product !== null){
            $product->delete();
            $response =
            [
                'status' => true,
                'message' => 'product Removed Successfully'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'product not found!!'
            ];
            return response()->json($response, 400);
        }
    }
        
    // private function decodeJsonIfNeeded($value) {
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
    static function getProductDetails($id){
        $product = Product::find($id);
        return $product? $product : null;
    }
}
