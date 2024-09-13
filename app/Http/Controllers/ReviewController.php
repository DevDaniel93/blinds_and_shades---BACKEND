<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;

class ReviewController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
     public function index()
    {
        $reviews = Review::get();
            $response =
            [
                'status' => true,
                'data' => $reviews,
                'message' => 'All reviews'
            ];
            return response()->json($response, 200);
    }
    public function indexForUserside()
    {
        $reviews = Review::where('is_hidden',0)->get();
            $response =
            [
                'status' => true,
                'data' => $reviews,
                'message' => 'All reviews'
            ];
            return response()->json($response, 200);
    }
    
    public function index2($userId)
    {
        if(isset($userId) && $userId !== '' && $userId > 0){
            $user = User::find($userId);
            if($user===null){
                $response =
                [
                    'status' => false,
                    'message' => 'User Not Found !!'
                ];
                return response()->json($response, 401);
            }
            
            $reviews = Review::where('user_id',$userId)->get();
            $response =
            [
                'status' => true,
                'data' => $reviews,
                'message' => 'All reviews'
            ];
            return response()->json($response, 200);
        }
            $response =
            [
                'status' => false,
                'message' => 'Invalid/empty user id, User id required !!'
            ];
            return response()->json($response, 401);
    }
     public function productReviewsForUserside($prodId)
    {
        if(isset($prodId) && $prodId !== '' && $prodId > 0){
            $product = Product::find($prodId);
            if($product === null){
            $response =
                [
                    'status' => false,
                    'message' => 'Product Not Found !!'
                ];
                return response()->json($response, 401);
            }
            $reviews = Review::where('product_id',$prodId)->where('is_hidden',0)->get();
            $response =
            [
                'status' => true,
                'data' => $reviews,
                'message' => 'All reviews of the product'
            ];
            return response()->json($response, 200);
        }else{
                $response =
                [
                    'status' => false,
                    'message' => 'Invalid/empty product id, Product id is required !!'
                ];
                return response()->json($response, 401);
            }
    }
    public function productReviews($prodId)
    {
        if(isset($prodId) && $prodId !== '' && $prodId > 0){
            $product = Product::find($prodId);
            if($product === null){
            $response =
                [
                    'status' => false,
                    'message' => 'Product Not Found !!'
                ];
                return response()->json($response, 401);
            }
            $reviews = Review::where('product_id',$prodId)->get();
            $response =
            [
                'status' => true,
                'data' => $reviews,
                'message' => 'All reviews of the product'
            ];
            return response()->json($response, 200);
        }else{
                $response =
                [
                    'status' => false,
                    'message' => 'Invalid/empty product id, Product id is required !!'
                ];
                return response()->json($response, 401);
            }
    }
    public function isUserReview(Request $request,$reviewId)
    {
        $validate = validator::make(
            $request->all(),
            [
                'user_id'  => 'required',
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
        if(isset($reviewId) && $reviewId !== '' && $reviewId > 0){
            $review = Review::where('id',$reviewId)->where('user_id', $request->user_id)->first();
            if($review !== null){
                $response =
                [
                    'status' => true,
                    'message' => 'owner'
                ];
                return response()->json($response, 200);
            }else{
                $response =
                [
                    'status' => false,
                    'message' => 'not owner'
                ];
                return response()->json($response, 400);
            }
        }
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
                'user_id'  => 'required',
                'product_id'  => 'required',
                'ratings'  => 'required',
                'review'  => 'required',
                'is_hidden'  => 'required',
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
        $review = Review::create($input);
        $msg = "review added successfully";
        $response = [
            'status' => true,
            'data' => $review,
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
        $review = Review::find($id);
        if($review !== null){
            $response =
            [
                'status' => true,
                'data' => $review,
                'message' => 'Review found'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'Review not found!!'
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
                'user_id'  => 'required',
                'product_id'  => 'required',
                'ratings'  => 'required',
                'review'  => 'required',
                'is_hidden'  => 'required',
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
        
        $review = Review::find($id);
        if($review !== null){
            $review->user_id = $request->user_id;
            $review->product_id = $request->product_id;
            $review->ratings = $request->ratings;
            $review->review = $request->review;
            $review->is_hidden = $request->is_hidden;
            $updated = $review->update();
            if(!$updated){
                $msg = "Failed to update!";
                $response = [
                    'status' => false,
                    'data' => $review,
                    'message' => $msg,
                ];
        
                return response()->json($response, 400);        
            }

            $msg = "review updated successfully";
            $response = [
                'status' => true,
                'data' => $review,
                'message' => $msg,
            ];
    
            return response()->json($response, 200);    
        }else{
            $response =
                [
                    'status' => false,
                    'message' => 'Review not found !!'
                ];
            return response()->json($response, 400);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $review = Review::find($id);
        if($review !== null){
            $review->delete();
            $response =
            [
                'status' => true,
                'message' => 'Review Removed Successfully'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'Review not found!!'
            ];
            return response()->json($response, 400);
        }
    }
}
