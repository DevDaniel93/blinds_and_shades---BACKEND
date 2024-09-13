<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portfolio;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;

class PortfolioController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function indexForUserside()
    {
        $portfolios = Portfolio::where('is_hidden',0)->get();
            $response =
            [
                'status' => true,
                'data' => $portfolios,
                'message' => 'All portfolio items'
            ];
            return response()->json($response, 200);
    }
    public function index()
    {
        $portfolios = Portfolio::get();
            $response =
            [
                'status' => true,
                'data' => $portfolios,
                'message' => 'All portfolio items'
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
                'title' => 'required',
                'description' => 'required',
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
        $image = $request->file('image')->move('images/portfolio/', $fileName);
        $input['image'] = $image;
        $portfolio = Portfolio::create($input);
        $msg = "Portfolio added successfully";
        $response = [
            'status' => true,
            'data' => $portfolio,
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
        $portfolio = Portfolio::find($id);
        if($portfolio !== null){
            $response =
            [
                'status' => true,
                'data' => $portfolio,
                'message' => 'Item found'
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $msg = "";
        $validate = validator::make(
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
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
        
        $portfolio = Portfolio::find($id);
        if($portfolio !== null){
            $portfolio->title = $request->title;
            $portfolio->description = $request->description;
            $portfolio->is_hidden = $request->is_hidden;
            if($request->has('image')){
                if($request->file('image') !== null && $request->file('image') !== ''){
                    $reqImage = $request->file('image');
                    if ($reqImage->isValid()) {
                        $randomize = rand(111111, 999999);
                        $extension = $request->file('image')->getClientOriginalExtension();
                        $fileName = $randomize . '.' . $extension;
                        $image = $request->file('image')->move('images/portfolio/', $fileName);
                        // $input['image'] = $image;
                         $portfolio->image = $image;
                    }
                }
            }
            $updated = $portfolio->update();
            if(!$updated){
                $msg = "Failed to update!";
                $response = [
                    'status' => false,
                    'data' => $portfolio,
                    'message' => $msg,
                ];
        
                return response()->json($response, 400);        
            }

            $msg = "Portfolio updated successfully";
            $response = [
                'status' => true,
                'data' => $portfolio,
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
        $portfolio = Portfolio::find($id);
        if($portfolio !== null){
            $portfolio->delete();
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
