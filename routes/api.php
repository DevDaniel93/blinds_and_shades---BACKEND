<?php

use Illuminate\Http\Request;
use App\Enums\TokenAbility;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\BlogController;
// use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AddonOptionController;
use App\Http\Controllers\AddonOptionVariationItemController;
use App\Http\Controllers\PortfolioController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::POST('user-login', [AuthController::class, 'login']);
Route::get('check-login', [AuthController::class, 'checkLogin']);
Route::POST('user-register', [AuthController::class, 'register']);
Route::post('forgot-password', [AuthController::class, 'forgot_password']); //email
Route::post('otp-verification', [AuthController::class, 'otp_verification']); //email,otp
Route::post('reset-password', [AuthController::class, 'reset_password']); //email,otp,newpass

// __________ get all ___________
// portfolios
Route::get('portfolio', [PortfolioController::class, 'indexForUserside']);
// gallery images
Route::get('gallery', [GalleryController::class, 'indexForUserside']);
// blogs
Route::get('blogs', [BlogController::class, 'indexForUserside']);
Route::get('blog/{slug}', [BlogController::class, 'blogDetails']); 

// rooms
Route::get('rooms', [RoomController::class, 'indexForUserside']);
// products
Route::get('products', [ProductController::class, 'indexForUserside']);
Route::get('product-details/{id}', [ProductController::class, 'prodcutDetails']);
// categories
Route::get('categories', [CategoryController::class, 'indexForUserside']);
// addons
Route::get('addons', [AddonController::class, 'indexForUserside']);
// addons options
Route::get('addon-options', [AddonOptionController::class, 'indexForUserside']);
// addons options variation items
Route::get('addon-option-variations', [AddonOptionVariationItemController::class, 'indexForUserside']);
// colors
Route::get('colors', [ColorController::class, 'indexForUserside']);
// reviews
Route::get('reviews', [ReviewController::class, 'indexForUserside']);
Route::get('product-reviews/{prod_id}', [ReviewController::class, 'productReviewsForUserside']);
// checkout
Route::POST('checkout', [OrderController::class, 'store']);
Route::POST('request-quote', [OrderController::class, 'requestQuote']);
// Route::POST('order/pay',[PaymentController::class,'processPayment']);

// contact form
Route::POST('submit-query', [GeneralController::class, 'contactForm']);


Route::group(['middleware' => ['auth:sanctum','custom_ability:' . TokenAbility::ACCESS_API->value]], function () {
    // _____________Auth Restricted routes_________________
    Route::post('logout', [AuthController::class, 'logout']);
    Route::POST('change-password', [AuthController::class, 'resetPassword']);
    // wishlist
    Route::resource('wishlist',WishlistController::class);
    // user
    Route::get('edit-profile/{id}', [UserController::class, 'edit']);
    Route::POST('update-profile/{id}', [UserController::class, 'update']);
    Route::POST('delete-profile/{id}', [UserController::class, 'destroy']);
    // order
    Route::get('order', [OrderController::class, 'indexForUserside']);
    
    // reviews for user side
    Route::get('reviews/{user_id}', [ReviewController::class, 'index2']);
    // Route::get('edit-review/{id}', [ReviewController::class, 'edit']);
    Route::POST('add-review', [ReviewController::class, 'store']);
    // Route::POST('update-review/{id}', [ReviewController::class, 'update']);
    // if required deleting functionality
    Route::POST('is-user-review/{review_id}', [ReviewController::class, 'isUserReview']);
    Route::POST('delete-review/{id}', [ReviewController::class, 'destroy']);


    Route::middleware(['admin'])->group(function () {
        // _______________Auth & Admin restricted routes____________
        // portfolio
        Route::get('admin/portfolio', [PortfolioController::class, 'index']);
        Route::get('admin/edit-portfolio/{id}', [PortfolioController::class, 'edit']);
        Route::POST('admin/add-portfolio', [PortfolioController::class, 'store']);
        Route::POST('admin/update-portfolio/{id}', [PortfolioController::class, 'update']);
        Route::POST('admin/delete-portfolio/{id}', [PortfolioController::class, 'destroy']);
        // gallery
        Route::get('admin/gallery', [GalleryController::class, 'index']);
        Route::POST('admin/add-gallery', [GalleryController::class, 'store']);
        Route::get('admin/edit-gallery/{id}', [GalleryController::class, 'edit']);
        Route::POST('admin/update-gallery/{id}', [GalleryController::class, 'update']);
        Route::POST('admin/delete-gallery/{id}', [GalleryController::class, 'destroy']);
        // users
        Route::get('admin/users', [UserController::class, 'index']);
        // blogs
        Route::get('admin/blogs', [BlogController::class, 'index']);
        Route::get('admin/edit-blog/{slug}', [BlogController::class, 'edit']);
        Route::POST('admin/add-blog', [BlogController::class, 'store']);
        Route::POST('admin/update-blog/{id}', [BlogController::class, 'update']);
        Route::POST('admin/delete-blog/{id}', [BlogController::class, 'destroy']);
        // products
        Route::get('admin/products', [ProductController::class, 'index']);
        Route::get('admin/edit-product/{id}', [ProductController::class, 'edit']);
        Route::POST('admin/add-product', [ProductController::class, 'store']);
        Route::POST('admin/update-product/{id}', [ProductController::class, 'update']);
        Route::POST('admin/delete-product/{id}', [ProductController::class, 'destroy']);
        // reviews
        Route::get('admin/reviews', [ReviewController::class, 'index']);
        Route::get('admin/edit-review/{id}', [ReviewController::class, 'edit']);
        Route::POST('admin/add-review', [ReviewController::class, 'store']);
        Route::POST('admin/update-review/{id}', [ReviewController::class, 'update']);
        Route::POST('admin/delete-review/{id}', [ReviewController::class, 'destroy']);
        // Rooms
        Route::get('admin/rooms', [RoomController::class, 'index']);
        Route::get('admin/edit-room/{id}', [RoomController::class, 'edit']);
        Route::POST('admin/add-room', [RoomController::class, 'store']);
        Route::POST('admin/update-room/{id}', [RoomController::class, 'update']);
        Route::POST('admin/delete-room/{id}', [RoomController::class, 'destroy']);
        // category
        Route::get('admin/categories', [CategoryController::class, 'index']);
        Route::get('admin/edit-category/{id}', [CategoryController::class, 'edit']);
        Route::POST('admin/add-category', [CategoryController::class, 'store']);
        Route::POST('admin/update-category/{id}', [CategoryController::class, 'update']);
        Route::POST('admin/delete-category/{id}', [CategoryController::class, 'destroy']);
        // addon
        Route::get('admin/addons', [AddonController::class, 'index']);
        Route::get('admin/edit-addon/{id}', [AddonController::class, 'edit']);
        Route::POST('admin/add-addon', [AddonController::class, 'store']);
        Route::POST('admin/update-addon/{id}', [AddonController::class, 'update']);
        Route::POST('admin/delete-addon/{id}', [AddonController::class, 'destroy']);
        // addon options
        Route::get('admin/addon-options', [AddonOptionController::class, 'index']);
        Route::get('admin/edit-addon-option/{id}', [AddonOptionController::class, 'edit']);
        Route::POST('admin/add-addon-option', [AddonOptionController::class, 'store']);
        Route::POST('admin/update-addon-option/{id}', [AddonOptionController::class, 'update']);
        Route::POST('admin/delete-addon-option/{id}', [AddonOptionController::class, 'destroy']);
        // addon options variations
        Route::get('admin/addon-option-variations', [AddonOptionVariationItemController::class, 'index']);
        Route::get('admin/edit-addon-option-variation/{id}', [AddonOptionVariationItemController::class, 'edit']);
        Route::POST('admin/add-addon-option-variation', [AddonOptionVariationItemController::class, 'store']);
        Route::POST('admin/update-addon-option-variation/{id}', [AddonOptionVariationItemController::class, 'update']);
        Route::POST('admin/delete-addon-option-variation/{id}', [AddonOptionVariationItemController::class, 'destroy']);
        // color
        Route::get('admin/colors', [ColorController::class, 'index']);
        Route::get('admin/edit-color/{id}', [ColorController::class, 'edit']);
        Route::POST('admin/add-color', [ColorController::class, 'store']);
        Route::POST('admin/update-color/{id}', [ColorController::class, 'update']);
        Route::POST('admin/delete-color/{id}', [ColorController::class, 'destroy']);
        Route::POST('admin/color-image/add/{id}', [ColorController::class, 'uploadImages']);
        Route::POST('admin/delete-images/{id}', [ColorController::class, 'deletedImages']);
        
        // order
        Route::get('admin/order', [OrderController::class, 'index']);
        Route::get('admin/order/{id}', [OrderController::class, 'edit']);
        Route::POST('admin/order/update/{id}', [OrderController::class, 'update']);
        Route::DELETE('admin/order/delete/{id}', [OrderController::class, 'destroy']);
        
    });

    // Route::middleware(['user'])->group(function () {
        // Route::controller(UserController::class)->group(function () {});
    // });

});

Route::middleware([
    'auth:sanctum',
    'custom_ability:'.TokenAbility::ISSUE_ACCESS_TOKEN->value
])->group(function () {
    Route::get('/auth/refresh-token', [AuthController::class, 'refreshToken']);
});

Route::any('/login',[AuthController::class, 'notLogin'])->name('login');
