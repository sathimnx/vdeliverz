<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MakeApiRequestController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



//Version 2
Route::namespace('Api\v2')->prefix('v2')->group(function(){
    Route::get('/change-decimal', 'AuthController@decimalNumber');
    Route::get('/products-pagination', 'BaseController@pagination');
    Route::get('/change-orders', 'BaseController@changeOrders');
    Route::post('/login', 'AuthController@login');
    Route::post('/delivery-login', 'AuthController@deliveryLogin');
    Route::post('/verify-otp', 'AuthController@verifyOTP');
    Route::post('/social-login', 'AuthController@socialLogin');
    Route::post('/apple-login', 'AuthController@appleLogin');
    Route::post('/get-otp', 'AuthController@resendOTP');
    Route::get('/fcm', 'BaseController@sendfcm');

    Route::group(['middleware' => 'active'], function () {
        Route::get('/dashboard', 'BaseController@dashboard');
        Route::post('/shops', 'BaseController@shops');
        Route::post('/single-shop', 'BaseController@singleShop');
        Route::post('/products', 'BaseController@products');
        Route::post('/category-products', 'BaseController@categoryProducts');
        Route::post('/manage-cart', 'CartController@manageCart');
        Route::post('/manage-cart-product', 'CartController@manageCartProduct');
        Route::post('/cart-data', 'CartController@getCartData');
        Route::post('/refresh-cart', 'CartController@refreshCartData');
        Route::get('/categories', 'BaseController@categories');
        Route::get('/coupons-list', 'CartController@couponsList');
        Route::post('/apply-coupon', 'CartController@applyCoupon');
        Route::post('/slots', 'CartController@slot');
        Route::post('/book-slot', 'CartController@bookSlot');
        Route::get('/cuisines', 'FilterController@cuisines');
        Route::post('/sort', 'FilterController@filter');
        Route::post('/product-customize', 'BaseController@productCustomize');
        Route::post('/cart-customize', 'CartController@cartCustomize');
        Route::post('/cart-product-update', 'CartController@updateCartProduct');
        Route::post('/multi-sort', 'FilterController@multiFilter');
        Route::post('/product-search', 'FilterController@productSearch');
        Route::post('/wishlists', 'AccountController@wishlists');
        Route::post('/add-instructions', 'CartController@addInstructions');
    });

    Route::group(['middleware' => ['auth:api', 'role:delivery-boy', 'active']], function () {
        Route::get('/delivery-dashboard', 'DeliveryController@deliveryOrders');
        Route::get('/delivered-orders', 'DeliveryController@deliveredOrders');
        Route::get('/canceled-orders', 'DeliveryController@canceledOrders');
        Route::post('/accept-order', 'DeliveryController@acceptOrder');
        Route::post('/pick-order', 'DeliveryController@orderPicked');
        Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
        Route::post('/cancel-delivery', 'DeliveryController@cancelOrder');
        Route::post('/deliver-order', 'DeliveryController@orderDelivered');
        Route::get('/delivery-notifications', 'DeliveryController@notifications');
        Route::post('/reject-order', 'DeliveryController@rejectOrder');
        Route::post('/delivery-detail', 'DeliveryController@orderDetail');
        Route::post('/delivery-search', 'DeliveryController@searchDelivery');
        Route::post('/delivered-order-search', 'DeliveryController@deliveredSearch');
        Route::post('/delivery-cash', 'DeliveryController@cashNote');
        Route::get('/delivery-logout', 'AccountController@deliverylogoutApi');

    });

    Route::group(['middleware' => ['auth:api', 'active']], function () {
        Route::get('/address-list', 'AddressController@index');
        Route::post('/add-address', 'AddressController@store');
        Route::post('/edit-address', 'AddressController@edit');
        Route::post('/update-address', 'AddressController@update');
        Route::post('/delete-address', 'AddressController@destroy');
        Route::post('/give-rating', 'AddressController@giveRating');
        Route::post('/order-summary', 'OrderController@orderSummary');
        Route::post('/order-confirm', 'OrderController@orderConfirm');
        Route::post('/cancel-order', 'OrderController@cancelOrder');
        Route::get('/notifications', 'NotificationController@notifications');
        Route::get('/user', 'AccountController@user');
        Route::post('/user-update', 'AccountController@userUpdate');
        Route::post('/manage-wishlist', 'AccountController@addWishlist');
        Route::get('/my-orders', 'AccountController@myOrders');
        Route::post('/order-details', 'OrderController@orderDetails');
        Route::post('/cancel-reason', 'OrderController@cancelReason');
        Route::post('/track-order', 'AccountController@trackOrder');
        Route::post('/order-again', 'OrderController@orderAgain');
        Route::post('/payment-initiate', 'CheckoutController@payment_initiate');
        Route::post('/customer-delivery-rating', 'OrderController@submitRating');

        Route::get('/logout', 'AccountController@logoutApi');
    });
    // Route::get('/order-kms', 'BaseController@orderKms');
});

//Version 3
Route::namespace('Api\v3')->prefix('v3')->group(function(){
    Route::get('/change-decimal', 'AuthController@decimalNumber');
    Route::get('/products-pagination', 'BaseController@pagination');
    Route::get('/change-orders', 'BaseController@changeOrders');
    Route::post('/login', 'AuthController@login');
    Route::post('/delivery-login', 'AuthController@deliveryLogin');
    Route::post('/verify-otp', 'AuthController@verifyOTP');
    Route::post('/social-login', 'AuthController@socialLogin');
    Route::post('/apple-login', 'AuthController@appleLogin');
    Route::post('/get-otp', 'AuthController@resendOTP');
    Route::get('/fcm', 'BaseController@sendfcm');
   // Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
   Route::get('/notifications', 'AuthController@demandNotifications');
   Route::post('/post',[MakeApiRequestController::class,'store']);
     Route::post('/payment-callback', 'CheckoutController@payment_callback');
   
    Route::group(['middleware' => 'active'], function () {
        Route::get('/dashboard', 'BaseController@dashboard');
        Route::post('/shops', 'BaseController@shops');
        Route::post('/single-shop', 'BaseController@singleShop');
        Route::post('/products', 'BaseController@products');
        Route::post('/category-products', 'BaseController@categoryProducts');
        Route::post('/manage-cart', 'CartController@manageCart');
        Route::post('/manage-cart-product', 'CartController@manageCartProduct');
        Route::post('/cart-data', 'CartController@getCartData');
        Route::post('/refresh-cart', 'CartController@refreshCartData');
        Route::get('/categories', 'BaseController@categories');
        Route::get('/coupons-list', 'CartController@couponsList');
        Route::post('/apply-coupon', 'CartController@applyCoupon');
        Route::post('/slots', 'CartController@slot');
        Route::post('/book-slot', 'CartController@bookSlot');
        Route::get('/cuisines', 'FilterController@cuisines');
        Route::post('/sort', 'FilterController@filter');
        Route::post('/product-customize', 'BaseController@productCustomize');
        Route::post('/cart-customize', 'CartController@cartCustomize');
        Route::post('/cart-product-update', 'CartController@updateCartProduct');
        Route::post('/multi-sort', 'FilterController@multiFilter');
        Route::post('/product-search', 'FilterController@productSearch');
        Route::post('/wishlists', 'AccountController@wishlists');
        Route::post('/add-instructions', 'CartController@addInstructions');
    });

    Route::group(['middleware' => ['auth:api', 'role:delivery-boy', 'active']], function () {
        Route::get('/delivery-dashboard', 'DeliveryController@deliveryOrders');
        Route::get('/delivered-orders', 'DeliveryController@deliveredOrders');
        Route::get('/canceled-orders', 'DeliveryController@canceledOrders');
        Route::post('/accept-order', 'DeliveryController@acceptOrder');
        Route::post('/pick-order', 'DeliveryController@orderPicked');
        Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
        Route::post('/cancel-delivery', 'DeliveryController@cancelOrder');
        Route::post('/deliver-order', 'DeliveryController@orderDelivered');
        Route::get('/delivery-notifications', 'DeliveryController@notifications');
        Route::post('/reject-order', 'DeliveryController@rejectOrder');
        Route::post('/delivery-detail', 'DeliveryController@orderDetail');
        Route::post('/delivery-search', 'DeliveryController@searchDelivery');
        Route::post('/delivered-order-search', 'DeliveryController@deliveredSearch');
        Route::post('/delivery-cash', 'DeliveryController@cashNote');
        Route::get('/delivery-logout', 'AccountController@deliverylogoutApi');

    });

    Route::group(['middleware' => ['auth:api', 'active']], function () {
        Route::get('/address-list', 'AddressController@index');
        Route::post('/add-address', 'AddressController@store');
        Route::post('/edit-address', 'AddressController@edit');
        Route::post('/update-address', 'AddressController@update');
        Route::post('/delete-address', 'AddressController@destroy');
        Route::post('/give-rating', 'AddressController@giveRating');
        Route::post('/order-summary', 'OrderController@orderSummary');
        Route::post('/order-confirm', 'OrderController@orderConfirm');
        Route::post('/cancel-order', 'OrderController@cancelOrder');
        Route::get('/notifications', 'NotificationController@notifications');
        Route::get('/user', 'AccountController@user');
        Route::post('/user-update', 'AccountController@userUpdate');
        Route::post('/manage-wishlist', 'AccountController@addWishlist');
        Route::get('/my-orders', 'AccountController@myOrders');
        Route::post('/order-details', 'OrderController@orderDetails');
        Route::post('/cancel-reason', 'OrderController@cancelReason');
        Route::post('/track-order', 'AccountController@trackOrder');
        Route::post('/order-again', 'OrderController@orderAgain');
        Route::post('/payment-initiate', 'CheckoutController@payment_initiate');
        Route::post('/customer-delivery-rating', 'OrderController@submitRating');

        Route::get('/logout', 'AccountController@logoutApi');
    });
    // Route::get('/order-kms', 'BaseController@orderKms');
});


//Version 4
Route::namespace('Api\v4')->prefix('v4')->group(function(){
    Route::get('/change-decimal', 'AuthController@decimalNumber');
    Route::get('/products-pagination', 'BaseController@pagination');
    Route::get('/change-orders', 'BaseController@changeOrders');
    Route::post('/login', 'AuthController@login');
    Route::post('/delivery-login', 'AuthController@deliveryLogin');
    Route::post('/verify-otp', 'AuthController@verifyOTP');
    Route::post('/social-login', 'AuthController@socialLogin');
    Route::post('/apple-login', 'AuthController@appleLogin');
    Route::post('/get-otp', 'AuthController@resendOTP');
    Route::get('/fcm', 'BaseController@sendfcm');
    Route::post('/request-top', 'AuthController@verifyLoginOTP');
    Route::post('/verify-login', 'AuthController@verifyLogin');
   // Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
   Route::get('/notifications', 'AuthController@demandNotifications');
   Route::post('/post',[MakeApiRequestController::class,'store']);
     Route::post('/payment-callback', 'CheckoutController@payment_callback');
   
    Route::group(['middleware' => 'active'], function () {
        Route::post('/dashboard', 'BaseController@dashboard');
        Route::post('/shops', 'BaseController@shops');
        Route::post('/single-shop', 'BaseController@singleShop');
        Route::post('/products', 'BaseController@products');
        Route::post('/category-products', 'BaseController@categoryProducts');
        Route::post('/manage-cart', 'CartController@manageCart');
        Route::post('/manage-cart-product', 'CartController@manageCartProduct');
        Route::post('/cart-data', 'CartController@getCartData');
        Route::post('/refresh-cart', 'CartController@refreshCartData');
        Route::get('/categories', 'BaseController@categories');
        Route::get('/coupons-list', 'CartController@couponsList');
        Route::post('/apply-coupon', 'CartController@applyCoupon');
        Route::post('/slots', 'CartController@slot');
        Route::post('/book-slot', 'CartController@bookSlot');
        Route::get('/cuisines', 'FilterController@cuisines');
        Route::post('/sort', 'FilterController@filter');
        Route::post('/product-customize', 'BaseController@productCustomize');
        Route::post('/cart-customize', 'CartController@cartCustomize');
        Route::post('/cart-product-update', 'CartController@updateCartProduct');
        Route::post('/multi-sort', 'FilterController@multiFilter');
        Route::post('/product-search', 'FilterController@productSearch');
        Route::post('/wishlists', 'AccountController@wishlists');
        Route::post('/add-instructions', 'CartController@addInstructions');
    });

    Route::group(['middleware' => ['auth:api', 'role:delivery-boy', 'active']], function () {
        Route::get('/delivery-dashboard', 'DeliveryController@deliveryOrders');
        Route::get('/delivered-orders', 'DeliveryController@deliveredOrders');
        Route::get('/canceled-orders', 'DeliveryController@canceledOrders');
        Route::post('/accept-order', 'DeliveryController@acceptOrder');
        Route::post('/pick-order', 'DeliveryController@orderPicked');
        Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
        Route::post('/cancel-delivery', 'DeliveryController@cancelOrder');
        Route::post('/deliver-order', 'DeliveryController@orderDelivered');
        Route::get('/delivery-notifications', 'DeliveryController@notifications');
        Route::post('/reject-order', 'DeliveryController@rejectOrder');
        Route::post('/delivery-detail', 'DeliveryController@orderDetail');
        Route::post('/delivery-search', 'DeliveryController@searchDelivery');
        Route::post('/delivered-order-search', 'DeliveryController@deliveredSearch');
        Route::post('/delivery-cash', 'DeliveryController@cashNote');
        Route::get('/delivery-logout', 'AccountController@deliverylogoutApi');

    });

    Route::group(['middleware' => ['auth:api', 'active']], function () {
        Route::get('/address-list', 'AddressController@index');
        Route::post('/add-address', 'AddressController@store');
        Route::post('/edit-address', 'AddressController@edit');
        Route::post('/update-address', 'AddressController@update');
        Route::post('/delete-address', 'AddressController@destroy');
        Route::post('/give-rating', 'AddressController@giveRating');
        Route::post('/order-summary', 'OrderController@orderSummary');
        Route::post('/order-confirm', 'OrderController@orderConfirm');
        Route::post('/cancel-order', 'OrderController@cancelOrder');
        Route::get('/notifications', 'NotificationController@notifications');
        Route::get('/user', 'AccountController@user');
        Route::post('/user-update', 'AccountController@userUpdate');
        Route::post('/manage-wishlist', 'AccountController@addWishlist');
        Route::get('/my-orders', 'AccountController@myOrders');
        Route::post('/order-details', 'OrderController@orderDetails');
        Route::post('/cancel-reason', 'OrderController@cancelReason');
        Route::post('/track-order', 'AccountController@trackOrder');
        Route::post('/order-again', 'OrderController@orderAgain');
        Route::post('/payment-initiate', 'CheckoutController@payment_initiate');
        Route::post('/customer-delivery-rating', 'OrderController@submitRating');

        Route::get('/logout', 'AccountController@logoutApi');
    });
    // Route::get('/order-kms', 'BaseController@orderKms');
});



//Version 5
Route::namespace('Api\v5')->prefix('v5')->group(function(){
    Route::get('/change-decimal', 'AuthController@decimalNumber');
    Route::get('/products-pagination', 'BaseController@pagination');
    Route::get('/change-orders', 'BaseController@changeOrders');
    Route::post('/login', 'AuthController@login');
    Route::post('/delivery-login', 'AuthController@deliveryLogin');
    Route::post('/verify-otp', 'AuthController@verifyOTP');
    Route::post('/social-login', 'AuthController@socialLogin');
    Route::post('/apple-login', 'AuthController@appleLogin');
    Route::post('/get-otp', 'AuthController@resendOTP');
    Route::get('/fcm', 'BaseController@sendfcm');
    Route::post('/request-top', 'AuthController@verifyLoginOTP');
    Route::post('/verify-login', 'AuthController@verifyLogin');
   // Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
   Route::get('/notifications', 'AuthController@demandNotifications');
   Route::post('/post',[MakeApiRequestController::class,'store']);
     Route::post('/payment-callback', 'CheckoutController@payment_callback');
   
    Route::group(['middleware' => 'active'], function () {
        Route::post('/dashboard', 'BaseController@dashboard');
        Route::post('/shops', 'BaseController@shops');
        Route::post('/single-shop', 'BaseController@singleShop');
        Route::post('/products', 'BaseController@products');
        Route::post('/category-products', 'BaseController@categoryProducts');
        Route::post('/manage-cart', 'CartController@manageCart');
        Route::post('/manage-cart-product', 'CartController@manageCartProduct');
        Route::post('/cart-data', 'CartController@getCartData');
        Route::post('/refresh-cart', 'CartController@refreshCartData');
        Route::get('/categories', 'BaseController@categories');
        Route::get('/coupons-list', 'CartController@couponsList');
        Route::post('/apply-coupon', 'CartController@applyCoupon');
        Route::post('/slots', 'CartController@slot');
        Route::post('/book-slot', 'CartController@bookSlot');
        Route::get('/cuisines', 'FilterController@cuisines');
        Route::post('/sort', 'FilterController@filter');
        Route::post('/product-customize', 'BaseController@productCustomize');
        Route::post('/cart-customize', 'CartController@cartCustomize');
        Route::post('/cart-product-update', 'CartController@updateCartProduct');
        Route::post('/multi-sort', 'FilterController@multiFilter');
        Route::post('/product-search', 'FilterController@productSearch');
        Route::post('/wishlists', 'AccountController@wishlists');
        Route::post('/add-instructions', 'CartController@addInstructions');
    });

    Route::group(['middleware' => ['auth:api', 'role:delivery-boy', 'active']], function () {
        Route::post('/delivery-dashboard', 'DeliveryController@deliveryOrders');
        Route::get('/delivered-orders', 'DeliveryController@deliveredOrders');
        Route::get('/canceled-orders', 'DeliveryController@canceledOrders');
        Route::post('/accept-order', 'DeliveryController@acceptOrder');
        Route::post('/pick-order', 'DeliveryController@orderPicked');
        Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
        Route::post('/cancel-delivery', 'DeliveryController@cancelOrder');
        Route::post('/deliver-order', 'DeliveryController@orderDelivered');
        Route::get('/delivery-notifications', 'DeliveryController@notifications');
        Route::post('/reject-order', 'DeliveryController@rejectOrder');
        Route::post('/delivery-detail', 'DeliveryController@orderDetail');
        Route::post('/delivery-search', 'DeliveryController@searchDelivery');
        Route::post('/delivered-order-search', 'DeliveryController@deliveredSearch');
        Route::post('/delivery-cash', 'DeliveryController@cashNote');
        Route::get('/delivery-logout', 'AccountController@deliverylogoutApi');

    });

    Route::group(['middleware' => ['auth:api', 'active']], function () {
        Route::get('/address-list', 'AddressController@index');
        Route::post('/add-address', 'AddressController@store');
        Route::post('/edit-address', 'AddressController@edit');
        Route::post('/update-address', 'AddressController@update');
        Route::post('/delete-address', 'AddressController@destroy');
        Route::post('/give-rating', 'AddressController@giveRating');
        Route::post('/order-summary', 'OrderController@orderSummary');
        Route::post('/order-confirm', 'OrderController@orderConfirm');
        Route::post('/cancel-order', 'OrderController@cancelOrder');
        Route::get('/notifications', 'NotificationController@notifications');
        Route::get('/user', 'AccountController@user');
        Route::post('/user-update', 'AccountController@userUpdate');
        Route::post('/manage-wishlist', 'AccountController@addWishlist');
        Route::get('/my-orders', 'AccountController@myOrders');
        Route::post('/order-details', 'OrderController@orderDetails');
        Route::post('/cancel-reason', 'OrderController@cancelReason');
        Route::post('/track-order', 'AccountController@trackOrder');
        Route::post('/order-again', 'OrderController@orderAgain');
        Route::post('/payment-initiate', 'CheckoutController@payment_initiate');
        Route::post('/customer-delivery-rating', 'OrderController@submitRating');

        Route::get('/logout', 'AccountController@logoutApi');
    });
    // Route::get('/order-kms', 'BaseController@orderKms');
});



//Version 5
Route::namespace('Api\v6')->prefix('v6')->group(function(){
    Route::get('/change-decimal', 'AuthController@decimalNumber');
    Route::get('/products-pagination', 'BaseController@pagination');
    Route::get('/change-orders', 'BaseController@changeOrders');
    Route::post('/login', 'AuthController@login');
    Route::post('/delivery-login', 'AuthController@deliveryLogin');
    Route::post('/verify-otp', 'AuthController@verifyOTP');
    Route::post('/social-login', 'AuthController@socialLogin');
    Route::post('/apple-login', 'AuthController@appleLogin');
    Route::post('/get-otp', 'AuthController@resendOTP');
    Route::get('/fcm', 'BaseController@sendfcm');
    Route::post('/request-top', 'AuthController@verifyLoginOTP');
    Route::post('/verify-login', 'AuthController@verifyLogin');
   // Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
   Route::get('/notifications', 'AuthController@demandNotifications');
   Route::post('/post',[MakeApiRequestController::class,'store']);
     Route::post('/payment-callback', 'CheckoutController@payment_callback');
   
    Route::group(['middleware' => 'active'], function () {
        Route::post('/dashboard', 'BaseController@dashboard');
        Route::post('/shops', 'BaseController@shops');
        Route::post('/vendor_coupondtl', 'BaseController@vendor_coupondtl');
        Route::post('/single-shop', 'BaseController@singleShop');
        Route::post('/products', 'BaseController@products');
        Route::post('/category-products', 'BaseController@categoryProducts');
        Route::post('/manage-cart', 'CartController@manageCart');
        Route::post('/manage-cart-product', 'CartController@manageCartProduct');
        Route::post('/cart-data', 'CartController@getCartData');
        Route::post('/refresh-cart', 'CartController@refreshCartData');
        Route::get('/categories', 'BaseController@categories');
        Route::get('/coupons-list', 'CartController@couponsList');
        Route::post('/apply-coupon', 'CartController@applyCoupon');
        Route::post('/slots', 'CartController@slot');
        Route::post('/book-slot', 'CartController@bookSlot');
        Route::get('/cuisines', 'FilterController@cuisines');
        Route::post('/sort', 'FilterController@filter');
        Route::post('/product-customize', 'BaseController@productCustomize');
        Route::post('/cart-customize', 'CartController@cartCustomize');
        Route::post('/cart-product-update', 'CartController@updateCartProduct');
        Route::post('/multi-sort', 'FilterController@multiFilter');
        Route::post('/product-search', 'FilterController@productSearch');
        Route::post('/wishlists', 'AccountController@wishlists');
        Route::post('/add-instructions', 'CartController@addInstructions');
        
        Route::post('/edit-checks', 'BaseController@editchecks');
    });

    Route::group(['middleware' => ['auth:api', 'role:delivery-boy', 'active']], function () {
        Route::post('/delivery-dashboard', 'DeliveryController@deliveryOrders');
        Route::get('/delivered-orders', 'DeliveryController@deliveredOrders');
        Route::get('/canceled-orders', 'DeliveryController@canceledOrders');
        Route::post('/accept-order', 'DeliveryController@acceptOrder');
        Route::post('/pick-order', 'DeliveryController@orderPicked');
        Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
        Route::post('/cancel-delivery', 'DeliveryController@cancelOrder');
        Route::post('/deliver-order', 'DeliveryController@orderDelivered');
        Route::get('/delivery-notifications', 'DeliveryController@notifications');
        Route::post('/reject-order', 'DeliveryController@rejectOrder');
        Route::post('/delivery-detail', 'DeliveryController@orderDetail');
        Route::post('/delivery-search', 'DeliveryController@searchDelivery');
        Route::post('/delivered-order-search', 'DeliveryController@deliveredSearch');
        Route::post('/delivery-cash', 'DeliveryController@cashNote');
        Route::get('/delivery-logout', 'AccountController@deliverylogoutApi');

    });

    Route::group(['middleware' => ['auth:api', 'active']], function () {
        Route::get('/address-list', 'AddressController@index');
        Route::post('/add-address', 'AddressController@store');
        Route::post('/edit-address', 'AddressController@edit');
        Route::post('/update-address', 'AddressController@update');
        Route::post('/delete-address', 'AddressController@destroy');
        Route::post('/give-rating', 'AddressController@giveRating');
        Route::post('/order-summary', 'OrderController@orderSummary');
        Route::post('/order-confirm', 'OrderController@orderConfirm');
        Route::post('/cancel-order', 'OrderController@cancelOrder');
        Route::get('/notifications', 'NotificationController@notifications');
        Route::get('/user', 'AccountController@user');
        Route::post('/user-update', 'AccountController@userUpdate');
        Route::post('/manage-wishlist', 'AccountController@addWishlist');
        Route::get('/my-orders', 'AccountController@myOrders');
        Route::post('/order-details', 'OrderController@orderDetails');
        Route::post('/cancel-reason', 'OrderController@cancelReason');
        Route::post('/track-order', 'AccountController@trackOrder');
        Route::post('/order-again', 'OrderController@orderAgain');
        Route::post('/payment-initiate', 'CheckoutController@payment_initiate');
        Route::post('/customer-delivery-rating', 'OrderController@submitRating');

        Route::get('/logout', 'AccountController@logoutApi');
    });
    // Route::get('/order-kms', 'BaseController@orderKms');
});

Route::namespace('Api\v7')->prefix('v7')->group(function(){
    Route::get('/change-decimal', 'AuthController@decimalNumber');
    Route::get('/products-pagination', 'BaseController@pagination');
    Route::get('/change-orders', 'BaseController@changeOrders');
    Route::post('/login', 'AuthController@login');
    Route::post('/delivery-login', 'AuthController@deliveryLogin');
    Route::post('/verify-otp', 'AuthController@verifyOTP');
    Route::post('/social-login', 'AuthController@socialLogin');
    Route::post('/apple-login', 'AuthController@appleLogin');
    Route::post('/get-otp', 'AuthController@resendOTP');
    Route::get('/fcm', 'BaseController@sendfcm');
    Route::post('/request-top', 'AuthController@verifyLoginOTP');
    Route::post('/verify-login', 'AuthController@verifyLogin');
   // Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
   Route::get('/notifications', 'AuthController@demandNotifications');
   Route::post('/post',[MakeApiRequestController::class,'store']);
     Route::post('/payment-callback', 'CheckoutController@payment_callback');
   
    Route::group(['middleware' => 'active'], function () {
        Route::post('/dashboard', 'BaseController@dashboard');
        Route::post('/shops', 'BaseController@shops');
        Route::post('/vendor_coupondtl', 'BaseController@vendor_coupondtl');
        Route::post('/single-shop', 'BaseController@singleShop');
        Route::post('/products', 'BaseController@products');
        Route::post('/category-products', 'BaseController@categoryProducts');
        Route::post('/manage-cart', 'CartController@manageCart');
        Route::post('/manage-cart-product', 'CartController@manageCartProduct');
        Route::post('/cart-data', 'CartController@getCartData');
        Route::post('/refresh-cart', 'CartController@refreshCartData');
        Route::get('/categories', 'BaseController@categories');
        Route::get('/coupons-list', 'CartController@couponsList');
        Route::post('/apply-coupon', 'CartController@applyCoupon');
        Route::post('/slots', 'CartController@slot');
        Route::post('/book-slot', 'CartController@bookSlot');
        Route::get('/cuisines', 'FilterController@cuisines');
        Route::post('/sort', 'FilterController@filter');
        Route::post('/product-customize', 'BaseController@productCustomize');
        Route::post('/cart-customize', 'CartController@cartCustomize');
        Route::post('/cart-product-update', 'CartController@updateCartProduct');
        Route::post('/multi-sort', 'FilterController@multiFilter');
        Route::post('/product-search', 'FilterController@productSearch');
        Route::post('/wishlists', 'AccountController@wishlists');
        Route::post('/add-instructions', 'CartController@addInstructions');
        
        Route::post('/edit-checks', 'BaseController@editchecks');
    });

    Route::group(['middleware' => ['auth:api', 'role:delivery-boy', 'active']], function () {
        Route::post('/delivery-dashboard', 'DeliveryController@deliveryOrders');
        Route::get('/delivered-orders', 'DeliveryController@deliveredOrders');
        Route::get('/canceled-orders', 'DeliveryController@canceledOrders');
        Route::post('/accept-order', 'DeliveryController@acceptOrder');
        Route::post('/pick-order', 'DeliveryController@orderPicked');
        Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
        Route::post('/cancel-delivery', 'DeliveryController@cancelOrder');
        Route::post('/deliver-order', 'DeliveryController@orderDelivered');
        Route::get('/delivery-notifications', 'DeliveryController@notifications');
        Route::post('/reject-order', 'DeliveryController@rejectOrder');
        Route::post('/delivery-detail', 'DeliveryController@orderDetail');
        Route::post('/delivery-search', 'DeliveryController@searchDelivery');
        Route::post('/delivered-order-search', 'DeliveryController@deliveredSearch');
        Route::post('/delivery-cash', 'DeliveryController@cashNote');
        Route::get('/delivery-logout', 'AccountController@deliverylogoutApi');

    });

    Route::group(['middleware' => ['auth:api', 'active']], function () {
        Route::get('/address-list', 'AddressController@index');
        Route::post('/add-address', 'AddressController@store');
        Route::post('/edit-address', 'AddressController@edit');
        Route::post('/update-address', 'AddressController@update');
        Route::post('/delete-address', 'AddressController@destroy');
        Route::post('/give-rating', 'AddressController@giveRating');
        Route::post('/order-summary', 'OrderController@orderSummary');
        Route::post('/order-confirm', 'OrderController@orderConfirm');
        Route::post('/cancel-order', 'OrderController@cancelOrder');
        Route::get('/notifications', 'NotificationController@notifications');
        Route::get('/user', 'AccountController@user');
        Route::post('/user-update', 'AccountController@userUpdate');
        Route::post('/manage-wishlist', 'AccountController@addWishlist');
        Route::get('/my-orders', 'AccountController@myOrders');
        Route::post('/order-details', 'OrderController@orderDetails');
        Route::post('/cancel-reason', 'OrderController@cancelReason');
        Route::post('/track-order', 'AccountController@trackOrder');
        Route::post('/order-again', 'OrderController@orderAgain');
        Route::post('/payment-initiate', 'CheckoutController@payment_initiate');
        Route::post('/customer-delivery-rating', 'OrderController@submitRating');

        Route::get('/logout', 'AccountController@logoutApi');
    });
    // Route::get('/order-kms', 'BaseController@orderKms');
});

Route::namespace('Api\v8')->prefix('v8')->group(function(){
    Route::get('/change-decimal', 'AuthController@decimalNumber');
    Route::get('/products-pagination', 'BaseController@pagination');
    Route::get('/change-orders', 'BaseController@changeOrders');
    Route::post('/login', 'AuthController@login');
    Route::post('/delivery-login', 'AuthController@deliveryLogin');
    Route::post('/verify-otp', 'AuthController@verifyOTP');
    Route::post('/social-login', 'AuthController@socialLogin');
    Route::post('/apple-login', 'AuthController@appleLogin');
    Route::post('/get-otp', 'AuthController@resendOTP');
    Route::get('/fcm', 'BaseController@sendfcm');
    Route::post('/request-top', 'AuthController@verifyLoginOTP');
    Route::post('/verify-login', 'AuthController@verifyLogin');
   // Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
   Route::get('/notifications', 'AuthController@demandNotifications');
   Route::post('/post',[MakeApiRequestController::class,'store']);
     Route::post('/payment-callback', 'CheckoutController@payment_callback');
   
    Route::group(['middleware' => 'active'], function () {
        Route::post('/dashboard', 'BaseController@dashboard');
        Route::post('/shops', 'BaseController@shops');
        Route::post('/vendor_coupondtl', 'BaseController@vendor_coupondtl');
        Route::post('/single-shop', 'BaseController@singleShop');
        Route::post('/products', 'BaseController@products');
        Route::post('/category-products', 'BaseController@categoryProducts');
        Route::post('/manage-cart', 'CartController@manageCart');
        Route::post('/manage-cart-product', 'CartController@manageCartProduct');
        Route::post('/cart-data', 'CartController@getCartData');
        Route::post('/refresh-cart', 'CartController@refreshCartData');
        Route::get('/categories', 'BaseController@categories');
        Route::get('/coupons-list', 'CartController@couponsList');
        Route::post('/apply-coupon', 'CartController@applyCoupon');
        Route::post('/slots', 'CartController@slot');
        Route::post('/book-slot', 'CartController@bookSlot');
        Route::get('/cuisines', 'FilterController@cuisines');
        Route::post('/sort', 'FilterController@filter');
        Route::post('/product-customize', 'BaseController@productCustomize');
        Route::post('/cart-customize', 'CartController@cartCustomize');
        Route::post('/cart-product-update', 'CartController@updateCartProduct');
        Route::post('/multi-sort', 'FilterController@multiFilter');
        Route::post('/product-search', 'FilterController@productSearch');
        Route::post('/wishlists', 'AccountController@wishlists');
        Route::post('/add-instructions', 'CartController@addInstructions');
        
        Route::post('/edit-checks', 'BaseController@editchecks');
    });

    Route::group(['middleware' => ['auth:api', 'role:delivery-boy', 'active']], function () {
        Route::post('/delivery-dashboard', 'DeliveryController@deliveryOrders');
        Route::get('/delivered-orders', 'DeliveryController@deliveredOrders');
        Route::get('/canceled-orders', 'DeliveryController@canceledOrders');
        Route::post('/accept-order', 'DeliveryController@acceptOrder');
        Route::post('/pick-order', 'DeliveryController@orderPicked');
        Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
        Route::post('/cancel-delivery', 'DeliveryController@cancelOrder');
        Route::post('/deliver-order', 'DeliveryController@orderDelivered');
        Route::get('/delivery-notifications', 'DeliveryController@notifications');
        Route::post('/reject-order', 'DeliveryController@rejectOrder');
        Route::post('/delivery-detail', 'DeliveryController@orderDetail');
        Route::post('/delivery-search', 'DeliveryController@searchDelivery');
        Route::post('/delivered-order-search', 'DeliveryController@deliveredSearch');
        Route::post('/delivery-cash', 'DeliveryController@cashNote');
        Route::get('/delivery-logout', 'AccountController@deliverylogoutApi');

    });

    Route::group(['middleware' => ['auth:api', 'active']], function () {
        Route::get('/address-list', 'AddressController@index');
        Route::post('/add-address', 'AddressController@store');
        Route::post('/edit-address', 'AddressController@edit');
        Route::post('/update-address', 'AddressController@update');
        Route::post('/delete-address', 'AddressController@destroy');
        Route::post('/give-rating', 'AddressController@giveRating');
        Route::post('/order-summary', 'OrderController@orderSummary');
        Route::post('/order-confirm', 'OrderController@orderConfirm');
        Route::post('/cancel-order', 'OrderController@cancelOrder');
        Route::get('/notifications', 'NotificationController@notifications');
        Route::get('/user', 'AccountController@user');
        Route::post('/user-update', 'AccountController@userUpdate');
        Route::post('/manage-wishlist', 'AccountController@addWishlist');
        Route::get('/my-orders', 'AccountController@myOrders');
        Route::post('/order-details', 'OrderController@orderDetails');
        Route::post('/cancel-reason', 'OrderController@cancelReason');
        Route::post('/track-order', 'AccountController@trackOrder');
        Route::post('/order-again', 'OrderController@orderAgain');
        Route::post('/payment-initiate', 'CheckoutController@payment_initiate');
        Route::post('/customer-delivery-rating', 'OrderController@submitRating');

        Route::get('/logout', 'AccountController@logoutApi');
    });
    // Route::get('/order-kms', 'BaseController@orderKms');
});

Route::namespace('Api\v9')->prefix('v9')->group(function(){
    Route::get('/change-decimal', 'AuthController@decimalNumber');
    Route::get('/products-pagination', 'BaseController@pagination');
    Route::get('/change-orders', 'BaseController@changeOrders');
    Route::post('/login', 'AuthController@login');
    Route::post('/delivery-login', 'AuthController@deliveryLogin');
    Route::post('/verify-otp', 'AuthController@verifyOTP');
    Route::post('/social-login', 'AuthController@socialLogin');
    Route::post('/apple-login', 'AuthController@appleLogin');
    Route::post('/get-otp', 'AuthController@resendOTP');
    Route::get('/fcm', 'BaseController@sendfcm');
    Route::post('/request-top', 'AuthController@verifyLoginOTP');
    Route::post('/verify-login', 'AuthController@verifyLogin');
   // Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
   Route::get('/notifications', 'AuthController@demandNotifications');
   Route::post('/post',[MakeApiRequestController::class,'store']);
     Route::post('/payment-callback', 'CheckoutController@payment_callback');
   
    Route::group(['middleware' => 'active'], function () {
        Route::post('/dashboard', 'BaseController@dashboard');
        Route::post('/shops', 'BaseController@shops');
        Route::post('/vendor_coupondtl', 'BaseController@vendor_coupondtl');
        Route::post('/single-shop', 'BaseController@singleShop');
        Route::post('/products', 'BaseController@products');
        Route::post('/category-products', 'BaseController@categoryProducts');
        Route::post('/manage-cart', 'CartController@manageCart');
        Route::post('/manage-cart-product', 'CartController@manageCartProduct');
        Route::post('/cart-data', 'CartController@getCartData');
        Route::post('/refresh-cart', 'CartController@refreshCartData');
        Route::get('/categories', 'BaseController@categories');
        Route::get('/coupons-list', 'CartController@couponsList');
        Route::post('/apply-coupon', 'CartController@applyCoupon');
        Route::post('/slots', 'CartController@slot');
        Route::post('/book-slot', 'CartController@bookSlot');
        Route::get('/cuisines', 'FilterController@cuisines');
        Route::post('/sort', 'FilterController@filter');
        Route::post('/product-customize', 'BaseController@productCustomize');
        Route::post('/cart-customize', 'CartController@cartCustomize');
        Route::post('/cart-product-update', 'CartController@updateCartProduct');
        Route::post('/multi-sort', 'FilterController@multiFilter');
        Route::post('/product-search', 'FilterController@productSearch');
        Route::post('/wishlists', 'AccountController@wishlists');
        Route::post('/add-instructions', 'CartController@addInstructions');
        
        Route::post('/edit-checks', 'BaseController@editchecks');
        
        // DTD Start
        Route::post('/deliveryBooking', 'BaseController@dTd_deliveryBooking');
        Route::post('/dTd_orderPlaced', 'BaseController@dTd_orderPlaced');
        Route::post('/dtd_payment_initiate', 'BaseController@dtd_payment_initiate');
        Route::post('/dtd_payment_callback', 'BaseController@dtd_payment_callback');
        Route::post('/dtd_payment_detail', 'BaseController@dtd_payment_detail');
        Route::get('/dtd_myOrders', 'BaseController@dtd_myOrders');
        Route::post('/dTd_trackOrder', 'BaseController@dTd_trackOrder');
        //DTD End
    });

    Route::group(['middleware' => ['auth:api', 'role:delivery-boy', 'active']], function () {
        Route::post('/delivery-dashboard', 'DeliveryController@deliveryOrders');
        Route::get('/delivered-orders', 'DeliveryController@deliveredOrders');
        Route::get('/canceled-orders', 'DeliveryController@canceledOrders');
        Route::post('/accept-order', 'DeliveryController@acceptOrder');
        Route::post('/pick-order', 'DeliveryController@orderPicked');
        Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
        Route::post('/cancel-delivery', 'DeliveryController@cancelOrder');
        Route::post('/deliver-order', 'DeliveryController@orderDelivered');
        Route::get('/delivery-notifications', 'DeliveryController@notifications');
        Route::post('/reject-order', 'DeliveryController@rejectOrder');
        Route::post('/delivery-detail', 'DeliveryController@orderDetail');
        Route::post('/delivery-search', 'DeliveryController@searchDelivery');
        Route::post('/delivered-order-search', 'DeliveryController@deliveredSearch');
        Route::post('/delivery-cash', 'DeliveryController@cashNote');
        Route::get('/delivery-logout', 'AccountController@deliverylogoutApi');
        Route::post('/accept-d2dOrder', 'DeliveryController@accept_d2dOrder');
        Route::post('/reject-d2dOrder', 'DeliveryController@reject_d2dOrder');
        Route::post('/ongoing-reject-d2dOrder', 'DeliveryController@ongoing_reject_d2dOrder');
        Route::post('/dtd_orderDelivered', 'DeliveryController@dtd_orderDelivered');
        Route::post('/dtd-orderPicked', 'DeliveryController@dtd_orderPicked');

    });

    Route::group(['middleware' => ['auth:api', 'active']], function () {
        Route::get('/address-list', 'AddressController@index');
        Route::post('/add-address', 'AddressController@store');
        Route::post('/edit-address', 'AddressController@edit');
        Route::post('/update-address', 'AddressController@update');
        Route::post('/delete-address', 'AddressController@destroy');
        Route::post('/give-rating', 'AddressController@giveRating');
        Route::post('/order-summary', 'OrderController@orderSummary');
        Route::post('/order-confirm', 'OrderController@orderConfirm');
        Route::post('/cancel-order', 'OrderController@cancelOrder');
        Route::get('/notifications', 'NotificationController@notifications');
        Route::get('/user', 'AccountController@user');
        Route::post('/user-update', 'AccountController@userUpdate');
        Route::post('/manage-wishlist', 'AccountController@addWishlist');
        Route::get('/my-orders', 'AccountController@myOrders');
        Route::post('/order-details', 'OrderController@orderDetails');
        Route::post('/cancel-reason', 'OrderController@cancelReason');
        Route::post('/track-order', 'AccountController@trackOrder');
        Route::post('/order-again', 'OrderController@orderAgain');
        Route::post('/payment-initiate', 'CheckoutController@payment_initiate');
        Route::post('/customer-delivery-rating', 'OrderController@submitRating');

        Route::get('/logout', 'AccountController@logoutApi');
    });
    // Route::get('/order-kms', 'BaseController@orderKms');
});

Route::namespace('Api\v10')->prefix('v10')->group(function(){
    Route::get('/change-decimal', 'AuthController@decimalNumber');
    Route::get('/products-pagination', 'BaseController@pagination');
    Route::get('/change-orders', 'BaseController@changeOrders');
    Route::post('/login', 'AuthController@login');
    Route::post('/delivery-login', 'AuthController@deliveryLogin');
    Route::post('/verify-otp', 'AuthController@verifyOTP');
    Route::post('/social-login', 'AuthController@socialLogin');
    Route::post('/apple-login', 'AuthController@appleLogin');
    Route::post('/get-otp', 'AuthController@resendOTP');
    Route::get('/fcm', 'BaseController@sendfcm');
    Route::post('/request-top', 'AuthController@verifyLoginOTP');
    Route::post('/verify-login', 'AuthController@verifyLogin');
   // Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
   Route::get('/notifications', 'AuthController@demandNotifications');
   Route::post('/post',[MakeApiRequestController::class,'store']);
     Route::post('/payment-callback', 'CheckoutController@payment_callback');
   
    Route::group(['middleware' => 'active'], function () {
        Route::post('/dashboard', 'BaseController@dashboard');
        Route::post('/shops', 'BaseController@shops');
        Route::post('/vendor_coupondtl', 'BaseController@vendor_coupondtl');
        Route::post('/single-shop', 'BaseController@singleShop');
        Route::post('/products', 'BaseController@products');
        Route::post('/category-products', 'BaseController@categoryProducts');
        Route::post('/manage-cart', 'CartController@manageCart');
        Route::post('/manage-cart-product', 'CartController@manageCartProduct');
        Route::post('/cart-data', 'CartController@getCartData');
        Route::post('/refresh-cart', 'CartController@refreshCartData');
        Route::get('/categories', 'BaseController@categories');
        Route::get('/coupons-list', 'CartController@couponsList');
        Route::post('/apply-coupon', 'CartController@applyCoupon');
        Route::post('/slots', 'CartController@slot');
        Route::post('/book-slot', 'CartController@bookSlot');
        Route::get('/cuisines', 'FilterController@cuisines');
        Route::post('/sort', 'FilterController@filter');
        Route::post('/product-customize', 'BaseController@productCustomize');
        Route::post('/cart-customize', 'CartController@cartCustomize');
        Route::post('/cart-product-update', 'CartController@updateCartProduct');
        Route::post('/multi-sort', 'FilterController@multiFilter');
        Route::post('/product-search', 'FilterController@productSearch');
        Route::post('/wishlists', 'AccountController@wishlists');
        Route::post('/add-instructions', 'CartController@addInstructions');
        
        Route::post('/edit-checks', 'BaseController@editchecks');
        
        // DTD Start
        Route::post('/deliveryBooking', 'BaseController@dTd_deliveryBooking');
        Route::post('/dTd_orderPlaced', 'BaseController@dTd_orderPlaced');
        Route::post('/dtd_payment_initiate', 'BaseController@dtd_payment_initiate');
        Route::post('/dtd_payment_callback', 'BaseController@dtd_payment_callback');
        Route::post('/dtd_payment_detail', 'BaseController@dtd_payment_detail');
        Route::get('/dtd_myOrders', 'BaseController@dtd_myOrders');
        Route::post('/dTd_trackOrder', 'BaseController@dTd_trackOrder');
        //DTD End
    });

    Route::group(['middleware' => ['auth:api', 'role:delivery-boy', 'active']], function () {
        Route::post('/delivery-dashboard', 'DeliveryController@deliveryOrders');
        Route::get('/delivered-orders', 'DeliveryController@deliveredOrders');
        Route::get('/canceled-orders', 'DeliveryController@canceledOrders');
        Route::post('/accept-order', 'DeliveryController@acceptOrder');
        Route::post('/pick-order', 'DeliveryController@orderPicked');
        Route::get('/ongoing-order', 'DeliveryController@onGoingOrder');
        Route::post('/cancel-delivery', 'DeliveryController@cancelOrder');
        Route::post('/deliver-order', 'DeliveryController@orderDelivered');
        Route::get('/delivery-notifications', 'DeliveryController@notifications');
        Route::post('/reject-order', 'DeliveryController@rejectOrder');
        Route::post('/delivery-detail', 'DeliveryController@orderDetail');
        Route::post('/delivery-search', 'DeliveryController@searchDelivery');
        Route::post('/delivered-order-search', 'DeliveryController@deliveredSearch');
        Route::post('/delivery-cash', 'DeliveryController@cashNote');
        Route::get('/delivery-logout', 'AccountController@deliverylogoutApi');
        Route::post('/accept-d2dOrder', 'DeliveryController@accept_d2dOrder');
        Route::post('/reject-d2dOrder', 'DeliveryController@reject_d2dOrder');
        Route::post('/ongoing-reject-d2dOrder', 'DeliveryController@ongoing_reject_d2dOrder');
        Route::post('/dtd_orderDelivered', 'DeliveryController@dtd_orderDelivered');
        Route::post('/dtd-orderPicked', 'DeliveryController@dtd_orderPicked');

    });

    Route::group(['middleware' => ['auth:api', 'active']], function () {
        Route::get('/address-list', 'AddressController@index');
        Route::post('/add-address', 'AddressController@store');
        Route::post('/edit-address', 'AddressController@edit');
        Route::post('/update-address', 'AddressController@update');
        Route::post('/delete-address', 'AddressController@destroy');
        Route::post('/give-rating', 'AddressController@giveRating');
        Route::post('/order-summary', 'OrderController@orderSummary');
        Route::post('/order-confirm', 'OrderController@orderConfirm');
        Route::post('/cancel-order', 'OrderController@cancelOrder');
        Route::get('/notifications', 'NotificationController@notifications');
        Route::get('/user', 'AccountController@user');
        Route::post('/user-update', 'AccountController@userUpdate');
        Route::post('/manage-wishlist', 'AccountController@addWishlist');
        Route::get('/my-orders', 'AccountController@myOrders');
        Route::post('/order-details', 'OrderController@orderDetails');
        Route::post('/cancel-reason', 'OrderController@cancelReason');
        Route::post('/track-order', 'AccountController@trackOrder');
        Route::post('/order-again', 'OrderController@orderAgain');
        Route::post('/payment-initiate', 'CheckoutController@payment_initiate');
        Route::post('/customer-delivery-rating', 'OrderController@submitRating');

        Route::get('/logout', 'AccountController@logoutApi');
    });
    // Route::get('/order-kms', 'BaseController@orderKms');
});

// Demand and Services
// Route::namespace('Api\Demand\v1')->prefix('demand/v1')->group(function(){
//     Route::get('/dashboard', 'DemandController@dashboard');
//     Route::get('/categories', 'DemandController@categories');
//     Route::post('/services', 'DemandController@services');
//     Route::post('/providers', 'DemandController@providers');
//     Route::post('/provider-view', 'DemandController@providerView');
//     Route::post('/booking', 'DemandController@booking');
//     Route::post('/add-instructions', 'DemandController@addInstructions');
//     Route::post('/available-cars', 'CarController@availableCars');
//     Route::post('/car-view', 'CarController@car');
//     Route::post('/car-booking', 'CarController@carBooking');
//     Route::post('/filter-services', 'FilterController@filterServices');
//     Route::post('/filter-providers', 'FilterController@filterProviders');
//     Route::post('/filter-car-providers', 'FilterController@filterCarProviders');
//     Route::post('/search-services', 'FilterController@searchServices');
//     Route::group(['middleware' => ['auth:api']], function () {
//         Route::post('/give-rating', 'ServiceController@giveRating');
//         Route::post('/give-car-rating', 'ServiceController@giveCarRating');
//         Route::get('/address-list', 'AddressController@index');
//         Route::post('/add-address', 'AddressController@store');
//         Route::post('/edit-address', 'AddressController@edit');
//         Route::post('/manage-wishlist', 'AuthController@addWishlist');
//         Route::post('/manage-car-wishlist', 'AuthController@addCarWishlist');
//         Route::post('/wishlists', 'AuthController@wishlists');
//         Route::post('/update-address', 'AddressController@update');
//         Route::post('/delete-address', 'AddressController@destroy');
//         Route::post('/service-summary', 'BookingController@summary');
//         Route::post('/book-service', 'BookingController@bookService');
//         Route::post('/booking-detail', 'BookingController@bookingDetail');
//         Route::post('/car-booking-detail', 'BookingController@carBookingDetail');
//         Route::get('/booking-list', 'ServiceController@bookingList');
//         Route::get('/car-booking-list', 'ServiceController@bookedCarsList');
//         Route::post('/cancel-booking', 'ServiceController@cancelBooking');
//         Route::post('/upload-proof', 'BookingController@documents');
//         Route::get('/car-summary', 'BookingController@carSummary');
//         Route::post('/add-booking-address', 'ServiceController@addBookingAddress');
//         Route::get('/notifications', 'AuthController@demandNotifications');
//     });
// });

Route::namespace('Api\vendor\v1')->prefix('vendor')->group(function(){

    Route::post('/login', 'LoginController@login');
    Route::post('/register', 'LoginController@register');
    Route::post('/verify-otp', 'LoginController@otpVerify');
    Route::post('/verify-mail-otp', 'LoginController@mailVerify');
    Route::post('/send-otp', 'LoginController@resendOTP');
    Route::post('/send-otp-mail', 'LoginController@sendMailOtp');
    Route::post('/reset-password', 'LoginController@resetPassword');

    Route::post('send-mail', 'LoginController@sendMail');
    Route::group(['middleware' => ['auth:api', 'role:vendor', 'verified_vendor']], function () {

        // Dashboard
        Route::get('/dashboard', 'DashboardController@dashboard');
        Route::post('/analytics', 'DashboardController@analytics');

        //Shops
        Route::get('/shops', 'OutletController@index');
        Route::get('/cuisines', 'CuisineController@index');
        Route::post('/shop-detail', 'OutletController@show');
        Route::post('/shop-products', 'OutletController@shopProducts');
        Route::post('/shop-update', 'OutletController@update');
        Route::post('/shop-create', 'OutletController@store');
        Route::post('/change-status', 'OutletController@change_status');
        Route::post('/shops-search', 'OutletController@search');

        // Products
        Route::get('/products', 'ProductController@index');
        Route::post('/product-stocks', 'ProductController@productCreate');
        Route::post('/products-search', 'ProductController@search');
        Route::get('/product-create', 'ProductController@create');
        Route::post('/product-detail', 'ProductController@show');
        Route::get('/products-list', 'StockController@create');
        Route::post('/add-product', 'ProductController@store');
        Route::post('/delete-product', 'ProductController@destroy');
        Route::post('/update-product', 'ProductController@update');
        Route::post('/category-products', 'ProductController@categoryProducts');

        // Product Categories
        Route::post('/product-categories', 'CategoryController@index');
        Route::post('/create-product-category', 'CategoryController@store');

        //Stocks
        Route::post('/stock-detail', 'StockController@show');
        Route::post('/add-stock', 'StockController@store');
        Route::post('/update-stock', 'StockController@update');
        Route::post('/delete-stock', 'StockController@destroy');

        // Toppings
        Route::get('/titles', 'ToppingController@titles');
        Route::post('/topping-detail', 'ToppingController@show');
        Route::post('/add-topping', 'ToppingController@store');
        Route::post('/update-topping', 'ToppingController@update');
        Route::post('/delete-topping', 'ToppingController@destroy');

        // Titles
        // Route::get('/titles', 'TitleController@index');
        Route::post('/add-title', 'TitleController@store');

        // Orders
        Route::get('/orders', 'OrderController@index');
        Route::post('/order-detail', 'OrderController@orderDetails');
        Route::post('/order-review', 'OrderController@orderReview');
        Route::post('/order-search', 'OrderController@search');

        // Payment
        Route::post('/payments', 'PaymentController@index');
        Route::post('/orders-history', 'PaymentController@orderHistory');
        Route::post('/request-withdrawal', 'PaymentController@requestWithdrawal');
        Route::post('/withdrawal-detail', 'PaymentController@withdrawalDetail');

        // Slots
        Route::post('/slots', 'SlotController@index');
        Route::post('/slot-detail', 'SlotController@show');
        Route::post('/slot-delete', 'SlotController@destroy');
        Route::post('/create-slot', 'SlotController@store');
        Route::post('/update-slot', 'SlotController@update');

        //Bank
        Route::post('/banks', 'BankController@index');
        Route::post('/add-bank', 'BankController@store');

        //Rating
        Route::post('/vendor-delivery-rating', 'OrderController@submitRating');

        Route::get('/notifications', 'AccountController@notifications');
        Route::get('/user', 'AccountController@user');
        Route::post('/user-update', 'AccountController@userUpdate');
        Route::get('/logout', 'AccountController@logoutApi');
    });
});

Route::namespace('Api\vendor\v2')->prefix('vendor-v5')->group(function(){

    Route::post('/login', 'LoginController@login');
    Route::post('/register', 'LoginController@register');
    Route::post('/verify-otp', 'LoginController@otpVerify');
    Route::post('/verify-mail-otp', 'LoginController@mailVerify');
    Route::post('/send-otp', 'LoginController@resendOTP');
    Route::post('/send-otp-mail', 'LoginController@sendMailOtp');
    Route::post('/reset-password', 'LoginController@resetPassword');

    Route::post('send-mail', 'LoginController@sendMail');
    Route::group(['middleware' => ['auth:api', 'role:vendor', 'verified_vendor']], function () {

        // Dashboard
        Route::get('/dashboard', 'DashboardController@dashboard');
        Route::post('/analytics', 'DashboardController@analytics');

        //Shops
        Route::get('/shops', 'OutletController@index');
        Route::get('/cuisines', 'CuisineController@index');
        Route::post('/shop-detail', 'OutletController@show');
        Route::post('/shop-products', 'OutletController@shopProducts');
        Route::post('/shop-update', 'OutletController@update');
        Route::post('/shop-create', 'OutletController@store');
        Route::post('/change-status', 'OutletController@change_status');
        Route::post('/shops-search', 'OutletController@search');

        // Products
        Route::get('/products', 'ProductController@index');
        Route::post('/product-stocks', 'ProductController@productCreate');
        Route::post('/products-search', 'ProductController@search');
        Route::get('/product-create', 'ProductController@create');
        Route::post('/product-detail', 'ProductController@show');
        Route::get('/products-list', 'StockController@create');
        Route::post('/add-product', 'ProductController@store');
        Route::post('/delete-product', 'ProductController@destroy');
        Route::post('/update-product', 'ProductController@update');
        Route::post('/category-products', 'ProductController@categoryProducts');

        // Product Categories
        Route::post('/product-categories', 'CategoryController@index');
        Route::post('/create-product-category', 'CategoryController@store');

        //Stocks
        Route::post('/stock-detail', 'StockController@show');
        Route::post('/add-stock', 'StockController@store');
        Route::post('/update-stock', 'StockController@update');
        Route::post('/delete-stock', 'StockController@destroy');

        // Toppings
        Route::get('/titles', 'ToppingController@titles');
        Route::post('/topping-detail', 'ToppingController@show');
        Route::post('/add-topping', 'ToppingController@store');
        Route::post('/update-topping', 'ToppingController@update');
        Route::post('/delete-topping', 'ToppingController@destroy');

        // Titles
        // Route::get('/titles', 'TitleController@index');
        Route::post('/add-title', 'TitleController@store');

        // Orders
        Route::get('/orders', 'OrderController@index');
        Route::post('/order-detail', 'OrderController@orderDetails');
        Route::post('/order-review', 'OrderController@orderReview');
        Route::post('/order-search', 'OrderController@search');

        // Payment
        Route::post('/payments', 'PaymentController@index');
        Route::post('/orders-history', 'PaymentController@orderHistory');
        Route::post('/request-withdrawal', 'PaymentController@requestWithdrawal');
        Route::post('/withdrawal-detail', 'PaymentController@withdrawalDetail');

        // Slots
        Route::post('/slots', 'SlotController@index');
        Route::post('/slot-detail', 'SlotController@show');
        Route::post('/slot-delete', 'SlotController@destroy');
        Route::post('/create-slot', 'SlotController@store');
        Route::post('/update-slot', 'SlotController@update');

        //Bank
        Route::post('/banks', 'BankController@index');
        Route::post('/add-bank', 'BankController@store');

        //Rating
        Route::post('/vendor-delivery-rating', 'OrderController@submitRating');

        Route::get('/notifications', 'AccountController@notifications');
        Route::get('/user', 'AccountController@user');
        Route::post('/user-update', 'AccountController@userUpdate');
        Route::get('/logout', 'AccountController@logoutApi');
    });
});

Route::namespace('Api\vendor\v3')->prefix('vendor-v6')->group(function(){

    Route::post('/login', 'LoginController@login');
    Route::post('/register', 'LoginController@register');
    Route::post('/verify-otp', 'LoginController@otpVerify');
    Route::post('/verify-mail-otp', 'LoginController@mailVerify');
    Route::post('/send-otp', 'LoginController@resendOTP');
    Route::post('/send-otp-mail', 'LoginController@sendMailOtp');
    Route::post('/reset-password', 'LoginController@resetPassword');

    Route::post('send-mail', 'LoginController@sendMail');
    Route::group(['middleware' => ['auth:api', 'role:vendor', 'verified_vendor']], function () {

        // Dashboard
        Route::post('/dashboard', 'DashboardController@dashboard');
        Route::post('/analytics', 'DashboardController@analytics');

        //Shops
        Route::get('/shops', 'OutletController@index');
        Route::get('/cuisines', 'CuisineController@index');
        Route::post('/shop-detail', 'OutletController@show');
        Route::post('/shop-products', 'OutletController@shopProducts');
        Route::post('/shop-update', 'OutletController@update');
        Route::post('/shop-create', 'OutletController@store');
        Route::post('/change-status', 'OutletController@change_status');
        Route::post('/shops-search', 'OutletController@search');

        // Products
        Route::get('/products', 'ProductController@index');
        Route::post('/product-stocks', 'ProductController@productCreate');
        Route::post('/products-search', 'ProductController@search');
        Route::get('/product-create', 'ProductController@create');
        Route::post('/product-detail', 'ProductController@show');
        Route::get('/products-list', 'StockController@create');
        Route::post('/add-product', 'ProductController@store');
        Route::post('/delete-product', 'ProductController@destroy');
        Route::post('/update-product', 'ProductController@update');
        Route::post('/category-products', 'ProductController@categoryProducts');

        // Product Categories
        Route::post('/product-categories', 'CategoryController@index');
        Route::post('/create-product-category', 'CategoryController@store');

        //Stocks
        Route::post('/stock-detail', 'StockController@show');
        Route::post('/add-stock', 'StockController@store');
        Route::post('/update-stock', 'StockController@update');
        Route::post('/delete-stock', 'StockController@destroy');

        // Toppings
        Route::get('/titles', 'ToppingController@titles');
        Route::post('/topping-detail', 'ToppingController@show');
        Route::post('/add-topping', 'ToppingController@store');
        Route::post('/update-topping', 'ToppingController@update');
        Route::post('/delete-topping', 'ToppingController@destroy');

        // Titles
        // Route::get('/titles', 'TitleController@index');
        Route::post('/add-title', 'TitleController@store');

        // Orders
        Route::get('/orders', 'OrderController@index');
        Route::post('/order-detail', 'OrderController@orderDetails');
        Route::post('/order-review', 'OrderController@orderReview');
        Route::post('/order-search', 'OrderController@search');

        // Payment
        Route::post('/payments', 'PaymentController@index');
        Route::post('/orders-history', 'PaymentController@orderHistory');
        Route::post('/request-withdrawal', 'PaymentController@requestWithdrawal');
        Route::post('/withdrawal-detail', 'PaymentController@withdrawalDetail');

        // Slots
        Route::post('/slots', 'SlotController@index');
        Route::post('/slot-detail', 'SlotController@show');
        Route::post('/slot-delete', 'SlotController@destroy');
        Route::post('/create-slot', 'SlotController@store');
        Route::post('/update-slot', 'SlotController@update');

        //Bank
        Route::post('/banks', 'BankController@index');
        Route::post('/add-bank', 'BankController@store');

        //Rating
        Route::post('/vendor-delivery-rating', 'OrderController@submitRating');

        Route::get('/notifications', 'AccountController@notifications');
        Route::get('/user', 'AccountController@user');
        Route::post('/user-update', 'AccountController@userUpdate');
        Route::get('/logout', 'AccountController@logoutApi');
    });
});


