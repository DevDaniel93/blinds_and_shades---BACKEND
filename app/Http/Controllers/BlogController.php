<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Str;
use App\Traits\ApiResponser;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
     public function index()
    {
        $blogs = Blog::get();
            $response =
            [
                'status' => true,
                'data' => $blogs,
                'message' => 'All blogs'
            ];
            return response()->json($response, 200);
    }
     
    public function indexForUserside()
    {
        $blogs = Blog::where('is_hidden',0)->get();
            $response =
            [
                'status' => true,
                'data' => $blogs,
                'message' => 'All blogs'
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
                'title' => 'required|string|unique:blogs,title',
                //  'slug' => 'required|string|unique:blogs,slug',
                'short_description' => 'required',
                'image' => 'required',
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
        $slug = Str::slug($request->title);
        $extension = $request->file('image')->getClientOriginalExtension();
        $fileName = time().'.'.$extension;
        $imagePath = 'images/blog-images/'.$fileName;
        $request->file('image')->move(public_path('images/blog-images/'), $fileName);
        $input['image'] = $imagePath;
        $input['slug'] = $slug;
        $blog = Blog::create($input);
        $msg = "blog added successfully";
        $response = [
            'status' => true,
            'data' => $blog,
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
        $blog = Blog::find($id);
        if($blog !== null){
            $response =
            [
                'status' => true,
                'data' => $blog,
                'message' => 'blog found'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'blog not found!!'
            ];
            return response()->json($response, 400);
        }
    }
    
    
    public function blogDetails(string $slug) 
    {
        $blog = Blog::where('slug',$slug)->first();
        if($blog !== null){
            $response =
            [
                'status' => true,
                'data' => $blog,
                'message' => 'blog found'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'blog not found!!'
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
                // 'title' => 'sometimes|string|unique:blogs,title',
                // 'title' => 'sometimes|string',
                'title' => [
                    'sometimes',
                    'string',
                    Rule::unique('blogs')->ignore($id)
                    ],
                // 'slug' => 'required|string|unique:blogs,slug',
                'short_description' => 'required',
                // 'image' => 'required',
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
        
        $blog = Blog::find($id);
        if($blog !== null){
            $blog->title = $request->title;
            $slug = Str::slug($request->title);
            $blog->slug = $slug;
            $blog->short_description = $request->short_description;
            if($request->has('long_description')){
                $blog->long_description = $request->long_description;    
            }
            if($request->hasFile('image')){
            if($request->file('image') !== null && $request->file('image') !== ''){
                $reqImage = $request->file('image');
                if ($reqImage->isValid()) {
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $fileName = time().'.'.$extension;
                    $filePath = 'images/blog-images/'.$fileName;
                    $request->file('image')->move(public_path('images/blog-images/'), $fileName);
                    $blog->image = $filePath;
                }
            }
        }
            $blog->is_hidden = $request->is_hidden;
            $updated = $blog->update();
            if(!$updated){
                $msg = "Failed to update!";
                $response = [
                    'status' => false,
                    'data' => $blog,
                    'message' => $msg,
                ];
        
                return response()->json($response, 400);        
            }

            $msg = "blog updated successfully";
            $response = [
                'status' => true,
                'data' => $blog,
                'message' => $msg,
            ];
    
            return response()->json($response, 200);    
        }else{
            $response =
                [
                    'status' => false,
                    'message' => 'blog not found !!'
                ];
            return response()->json($response, 400);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blog = Blog::find($id);
        if($blog !== null){
            $blog->delete();
            $response =
            [
                'status' => true,
                'message' => 'blog Removed Successfully'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'blog not found!!'
            ];
            return response()->json($response, 400);
        }
    }
    
    /**
     * Generate a unique slug based on the title.
     *
     * @param string $title
     * @return string
     */
}
