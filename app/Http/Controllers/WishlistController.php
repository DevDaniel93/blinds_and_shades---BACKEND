<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Traits\ApiResponser;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class WishlistController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wishlists = Wishlist::where('user_id',Auth::id())->get();
            $response =
            [
                'status' => true,
                'data' => $wishlists,
                'message' => 'All wishlist items'
            ];
            return response()->json($response, 200);
    }
    
    public function show(string $id)
    {
        $wishlist = Wishlist::find($id);
            $response =
            [
                'status' => true,
                'data' => $wishlist,
                'message' => 'Item found'
            ];
            return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_items' => 'required|array',
            // 'order_items.*.id' => 'required|integer|exists:products,id',
            // 'order_items.*.color_id' => 'required|integer|exists:colors,id',
            // 'order_items.*.mount_type' => 'required|string',
            // 'order_items.*.height' => 'required|numeric',
            // 'order_items.*.width' => 'required|numeric',
            // 'order_items.*.guarantee_fit' => 'required|integer',
            // 'order_items.*.room_name_wall' => 'nullable|array',
            // 'order_items.*.room_name_wall.name' => 'nullable|string',
            // 'order_items.*.room_name_wall.wall' => 'nullable|string',
            // 'order_items.*.customizations_selected' => 'required|array',
            // 'order_items.*.customizations_selected.*.id' => 'required|integer',
            // 'order_items.*.customizations_selected.*.addon_id' => 'required|integer',
            // 'order_items.*.customizations_selected.*.title' => 'required|string',
            // 'order_items.*.customizations_selected.*.description' => 'nullable|string',
            // 'order_items.*.customizations_selected.*.video_link' => 'nullable|url',
            // 'order_items.*.customizations_selected.*.status' => 'required|string',
            // 'order_items.*.customizations_selected.*.amount' => 'required|numeric',
            // 'order_items.*.customizations_selected.*.image' => 'nullable|string',
            // 'order_items.*.customizations_selected.*.type' => 'required|string',
            // 'order_items.*.customizations_selected.*.variables' => 'nullable|array',
            // 'order_items.*.customizations_selected.*.variables.*.title' => 'nullable|string',
            // 'order_items.*.customizations_selected.*.variables.*.description' => 'nullable|string',
            // 'order_items.*.customizations_selected.*.variables.*.var_itmes' => 'nullable|array',
            // 'order_items.*.customizations_selected.*.variables.*.var_itmes.*.title' => 'nullable|string',
            // 'order_items.*.customizations_selected.*.variables.*.var_itmes.*.price' => 'nullable|numeric',
            // 'order_items.*.warranty_option' => 'required|array',
            // 'order_items.*.warranty_option.title' => 'required|string',
            // 'order_items.*.warranty_option.price' => 'required|numeric',
            // 'order_items.*.quantity' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }
        
        $entry='';
        if(count($request->order_items) > 0){
            foreach ($request->order_items as $item) {
                $item = json_decode($item);
                $entry = new Wishlist();
                $entry->user_id = Auth::id();
                $entry->product_id = $item->id;
                $entry->color_id = $item->color_id;
                $entry->mount_type = $item->mount_type;
                $entry->height = $item->height;
                $entry->width = $item->width;
                $entry->guarantee_fit = $item->guarantee_fit??0;  //not coming from frontend cart
                $entry->room_name = $item->room_name;
                $entry->room_wall = $item->room_wall;
                $entry->customizations_selected = $item->customizations_selected;
                $entry->warranty_options = $item->warranty_options;
                $entry->quantity = $item->quantity;
                $entry->save();
            }
        }else{
               return response()->json([
                'status' => false,
                'message' => 'Please provide wishlist product',
                ], 400);  
        }
                    
    //   $data = [
    //         'order' => $order,
    //         'order_items' => $request->order_items,
    //         'shipping_info' => [
    //             'first_name' => $request->shipping_first_name,
    //             'last_name' => $request->shipping_last_name,
    //             'address' => $request->shipping_address,
    //             'apt' => $request->shipping_apt,
    //             'city' => $request->shipping_city,
    //             'state' => $request->shipping_state,
    //             'zipcode' => $request->shipping_zipcode,
    //         ],
    //         'billing_info' => $request->is_billing_address_same == 0 ? [
    //             'first_name' => $request->billing_first_name,
    //             'last_name' => $request->billing_last_name,
    //             'address' => $request->billing_address,
    //             'apt' => $request->billing_apt,
    //             'city' => $request->billing_city,
    //             'state' => $request->billing_state,
    //             'zipcode' => $request->billing_zipcode,
    //         ] : null,
    //         'order_amount' => $orderAmount,
    //     ];
        
    //     $datamail = Mail::send('mail.sendOrderEmail', $data, function ($message) use ($request) {
    //         $message->to($request->email)->subject('Order Confirmation');
    //     });
        
    //     if (!$datamail) {
    //          return response()->json([
    //         'status' => false,
    //         'message' => 'Failed to send email.',
    //         ], 401);
    //     }
        
        return response()->json([
            'status' => true,
            'order_id' => $entry,
            'message' => 'Added to wishlist successfully.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $wishlist = Wishlist::find($id);
        if($wishlist !== null){
            $wishlist->delete();
            $response =
            [
                'status' => true,
                'message' => 'wishlist Removed Successfully'
            ];
            return response()->json($response, 200);
        }else{
            $response =
            [
                'status' => false,
                'message' => 'wishlist not found!!'
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
