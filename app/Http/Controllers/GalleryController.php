<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gallery;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;

class GalleryController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gallery = Gallery::get();
            $response =
            [
                'status' => true,
                'data' => $gallery,
                'message' => 'All images'
            ];
            return response()->json($response, 200);
    }
     
    public function indexForUserside()
    {
        $gallery = Gallery::where('is_hidden',0)->get();
            $response =
            [
                'status' => true,
                'data' => $gallery,
                'message' => 'All images'
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
        $randomize = rand(111111, 999999);
        $extension = $request->file('image')->getClientOriginalExtension();
        $fileName = $randomize . '.' . $extension;
        $image = $request->file('image')->move('images/gallery/', $fileName);
        $input['image'] = $image;
        $galleryItem = Gallery::create($input);
        $msg = "Gallery item added successfully";
        $response = [
            'status' => true,
            'data' => $galleryItem,
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
        $galleryItem = Gallery::find($id);
        if($galleryItem !== null){
            $response =
            [
                'status' => true,
                'data' => $galleryItem,
                'message' => 'Gallery item found'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'Gallery item not found!!'
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
 
        $galleryItem = Gallery::find($id);
        if($galleryItem !== null){
            $galleryItem->is_hidden = $request->is_hidden;
            
            if($request->has('image')){
                if($request->file('image') !== null && $request->file('image') !== ''){
                    $reqImage = $request->file('image');
                    if ($reqImage->isValid()) {
                        $randomize = rand(111111, 999999);
                        $extension = $request->file('image')->getClientOriginalExtension();
                        $fileName = $randomize . '.' . $extension;
                        $image = $request->file('image')->move('images/gallery/', $fileName);
                        $galleryItem->image = $image;
                    }
                }
            }
            $updated = $galleryItem->update();
            if(!$updated){
                $msg = "Failed to update!";
                $response = [
                    'status' => false,
                    'data' => $galleryItem,
                    'message' => $msg,
                ];
        
                return response()->json($response, 400);        
            }

            $msg = "Gallery Item updated successfully";
            $response = [
                'status' => true,
                'data' => $$galleryItem,
                'message' => $msg,
            ];
    
            return response()->json($response, 200);    
        }else{
            $response =
                [
                    'status' => false,
                    'message' => 'Item not found !!'
                ];
            return response()->json($response, 400);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $galleryItem = Gallery::find($id);
        if($galleryItem !== null){
            $galleryItem->delete();
            $response =
            [
                'status' => true,
                'message' => 'Item Removed Successfully'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'Item not found!!'
            ];
            return response()->json($response, 400);
        }
    }
}
