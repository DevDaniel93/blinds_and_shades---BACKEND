<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;

class CategoryController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::get();
            $response =
            [
                'status' => true,
                'data' => $categories,
                'message' => 'All categories'
            ];
            return response()->json($response, 200);
    }
     
    public function indexForUserside()
    {
        $categories = Category::where('is_hidden',0)->get();
            $response =
            [
                'status' => true,
                'data' => $categories,
                'message' => 'All categories'
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
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg',
                // 'parent_id' => 'nullable|exists:categories,id',
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
         if ($request->hasFile('image')) {
                $image = $request->file('image');
                if ($image->isValid()) {
                $extension = $request->file('image')->getClientOriginalExtension();
                $fileName = time().'.'.$extension;
                $path = 'images/category-images/'.$fileName;
                $request->file('image')->move('images/category-images/', $fileName);
                $input['image'] = $path;
            }
        }
        
        $category = Category::create($input);
        $msg = "category added successfully";
        $response = [
            'status' => true,
            'data' => $category,
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
        $category = Category::find($id);
        if($category !== null){
            $response =
            [
                'status' => true,
                'data' => $category,
                'message' => 'category found'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'category not found!!'
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
                // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg',
                // 'parent_id' => 'nullable|exists:categories,id',
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
        
        $category = Category::find($id);
        if($category !== null){
            $category->title = $request->title;
            if($request->has('description')){
                $category->description = $request->description;    
            }
            // if($request->has('parent_id')){
            //     $category->parent_id = $request->parent_id;    
            // }
        
          // Handle image
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                if ($image->isValid()) {
                    // Remove old image if necessary
                     if ($category->image && File::exists(public_path($category->image))) {
                        // Delete the existing image
                        File::delete(public_path($category->image));
                    }
                    // Store new image
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $fileName = time().'.'.$extension;
                    $path = 'images/category-images/'.$fileName;
                    $request->file('image')->move('images/category-images/', $fileName);
                        $category->image = $path;
                    }
            }
            $category->is_hidden = $request->is_hidden;
            $updated = $category->update();
            if(!$updated){
                $msg = "Failed to update!";
                $response = [
                    'status' => false,
                    'data' => $category,
                    'message' => $msg,
                ];
        
                return response()->json($response, 400);        
            }

            $msg = "category updated successfully";
            $response = [
                'status' => true,
                'data' => $category,
                'message' => $msg,
            ];
    
            return response()->json($response, 200);    
        }else{
            $response =
                [
                    'status' => false,
                    'message' => 'category not found !!'
                ];
            return response()->json($response, 400);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        if($category !== null){
            $category->delete();
            $response =
            [
                'status' => true,
                'message' => 'category Removed Successfully'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'category not found!!'
            ];
            return response()->json($response, 400);
        }
    }
}
