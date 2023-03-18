<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);


Route::get('privacy-policy', function(){
    return view('privacy_policy');
});

Route::get('terms-and-conditions', function(){
    return view('terms_and_conditions');
});

//Route::get('/home', 'HomeController@index')->name('home');
Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/analytics', 'Admin\AnalyticsController@index')->name('analytics.index');
    Route::get('/analytics/{year}/{month}', 'Admin\AnalyticsController@filter')->name('analytics.filter');
    Route::get('/clear-cache', function(){
        Artisan::call('optimize:clear');
        flash()->success('Cache Cleared Successfully!');
        return back();
    });
});
Route::group(['middleware' => ['role:admin|vendor']], function () {
    Route::namespace('Admin')->group(function(){
        Route::get('/dashboard', 'DashboardController@index')->name('dashboard.index');
        Route::get('/dashboard/{shop}/{year}/{month}', 'DashboardController@dashboardFilter')->name('dashboard.filter');

        Route::get('/categories/{category}/order', 'CategoryController@orderChange')->name('order_change');
        Route::get('/shops/prior', 'ShopController@priorChange')->name('prior_change');
        Route::put('/shop-update/{id}', 'ShopController@shopUpdate')->name('shop-update');
        Route::get('/shops/{id}/category', 'ShopController@shopCatFilter')->name('shops.filter');
        Route::get('notifications', 'DashboardController@notifications')->name('notifications');
        Route::get('notifications-read-all', 'DashboardController@notificationsReadAll')->name('notifications-read-all');
        Route::post('/review-order', 'OrderController@reviewOrder')->name('review-order.index');
        Route::post('/show-review', 'OrderController@showReview')->name('show-review.index');
        Route::post('/check-unique', 'DashboardController@checkUnique')->name('check-unique.index');

        Route::get('/slots/{id}/filter', 'SlotController@filterSlot')->name('slots.filter');
//        Route::get('/products/{shop}/datas', 'ProductController@filterProduct')->name('slots.filter');
         Route::get(rtrim('orders_filter/{shop}/{type}'), 'OrderController@filter')->name('orders.filter');

        Route::get('/shops/{id}/active', 'ShopController@activeFilter')->name('shops-active.filter');
        Route::post('/manual-delivery', 'OrderController@manualDelivery')->name('manual.index');
        Route::get('/payments', 'PaymentController@index')->name('payments.list');
        Route::get('/payments/{shop}/{status}', 'PaymentController@show')->name('payments.show');
        Route::get('/payments-history-view/{id}', 'PaymentController@historyView')->name('payments.view');
        Route::get('/payments-history', 'PaymentController@paymentHistoryList')->name('payments.index');
        Route::get('/payments-history/{shop}/{status}', 'PaymentController@paymentHistory')->name('payments.history');
        Route::post('/payments/{shop}', 'PaymentController@payment')->name('payments.update');
        Route::post('/payments-response/{id}', 'PaymentController@paymentResponse')->name('payments.response');
        Route::get('/product-categories/{shop}/filter', 'ProductCategoryController@filter')->name('product-categories.filter');
        Route::get('/product-categories/{category}/change', 'ProductCategoryController@orderChange')->name('product-categories.change');
        Route::post('/banks/create', 'BankController@store')->name('banks.store');
        Route::post('/charges-update', 'ContactController@chargesUpdate')->name('charges.update');
        Route::post('/product_TimingsAdd', 'ProductController@product_TimingsAdd')->name('product_TimingsAdd');
        Route::post('/deleteTimingProduct', 'ProductController@deleteTimingProduct')->name('deleteTimingProduct');
        
        Route::post('/get_nearest_deliveryBoy', 'DoorToDoorDeliveryController@get_nearest_deliveryBoy')->name('get_nearest_deliveryBoy');
        Route::post('/assign_deliveryBoy', 'DoorToDoorDeliveryController@assign_deliveryBoy')->name('assign_deliveryBoy');
        Route::post('/save_newVehicle', 'DoorToDoorDeliveryController@save_newVehicle')->name('save_newVehicle');
        
        Route::post('/getdelivery_boyBy_type', 'DoorToDoorDeliveryOrdersController@getdelivery_boyBy_type')->name('getdelivery_boyBy_type');
        Route::post('/getDeliveryboy_pendingOrders', 'DoorToDoorDeliveryOrdersController@getDeliveryboy_pendingOrders')->name('getDeliveryboy_pendingOrders');
        
        
        Route::post('/get_fourWheelerCharges', 'ContactController@get_fourWheelerCharges')->name('get_fourWheelerCharges');
        
        Route::post('/get_TwoWheelerCharges', 'DoorToDoorDeliveryController@get_TwoWheelerCharges')->name('get_TwoWheelerCharges');
        Route::get(rtrim('charge_filter/{type}'), 'DoorToDoorDeliveryController@charge_filter')->name('door-To-Door-Delivery.charge_filter');
        Route::post('/edit', 'DoorToDoorDeliveryController@edit')->name('edit');
        
        Route::post('/removeCartProduct', 'OrderController@removeCartProduct')->name('removeCartProduct');
        Route::post('/getvariantOfProduct', 'OrderController@getvariantOfProduct')->name('getvariantOfProduct');
        Route::post('/getActualPriceOfProduct', 'OrderController@getActualPriceOfProduct')->name('getActualPriceOfProduct');
        Route::post('/addToCart', 'OrderController@addToCart')->name('addToCart');
        Route::post('/changeOrderStatus', 'OrderController@changeOrderStatus')->name('changeOrderStatus');

        Route::post('/notification_add', 'ContactController@notificationAdd')->name('notificationAdd');
        Route::post('/change-status', 'DashboardController@change_status')->name('change_status.index');
        Route::get('/paid-orders/{shop}/datas', 'PaidOrderController@filter')->name('paid-orders.filter');
        Route::post('/get_Product_Category', 'VendorCouponController@getProductCategory')->name('getProductCategory');
        Route::post('/get_Products', 'VendorCouponController@getProducts')->name('getProducts');
        Route::post('/getshopdtls', 'VendorCouponController@getshopdtls')->name('getshopdtls');
Route::post('/deletevendorCoupons', 'VendorCouponController@deletevendorCoupons')->name('deletevendorCoupons');

Route::post('/changeOrderStatus', 'OrderController@changeOrderStatus')->name('changeOrderStatus');
Route::post('/delete_orders', 'OrderController@delete_orders')->name('delete_orders');


        Route::get('/paid-sales/{id}', 'SalesController@paidSales')->name('sales.detail');
        Route::resource('/push-notifications', 'PushNotificationController');
        Route::resource('/roles', 'RoleController');
        Route::resource('/users', 'UserController');
        Route::resource('/shops', 'ShopController');
        Route::resource('/cuisines', 'CuisineController');
        Route::resource('/categories', 'CategoryController');
        Route::resource('/products', 'ProductController');
        Route::resource('/product-categories', 'ProductCategoryController');
        Route::resource('/stocks', 'StockController');
        Route::resource('/coupons', 'CouponController');
        Route::resource('/vendorcoupons', 'VendorCouponController');
        Route::resource('/addresses', 'AddressController');
        Route::resource('/orders', 'OrderController');
        Route::resource('/paid-orders', 'PaidOrderController');
        Route::resource('/slots', 'SlotController');
        Route::resource('/titles', 'TitleController');
        Route::resource('/banners', 'BannerController');
        Route::resource('/shop-banners', 'ShopBannerController');
        Route::resource('/toppings', 'ToppingController');
        Route::resource('/sales', 'SalesController');
        Route::resource('/contacts', 'ContactController');
        Route::resource('/reviews', 'ReviewController');
        Route::resource('/directions', 'DirectionController');
        Route::resource('/manage-deliveries', 'ManageDeliveryController');
        Route::resource('/door-To-Door-Delivery','DoorToDoorDeliveryController');
        Route::resource('/door-To-Door-Delivery-Order','DoorToDoorDeliveryOrdersController');
    });
});

    //Demand and Services
    // Route::group(['middleware' => ['role:admin|provider']], function () {
    //     Route::namespace('Demand')->prefix('demand')->name('demand.')->group(function(){
    //     Route::get('/dashboard', 'DashboardController@index')->name('dashboard.index');
    //     Route::get('/dashboard/{shop}/{year}/{month}', 'DashboardController@filter')->name('dashboard.filter');

    //     // Booking Review
    //     Route::post('/show-booking-review', 'BookingController@showReview')->name('show-booking-review.index');
    //     Route::post('/review-book', 'BookingController@reviewOrder')->name('review-book.index');

    //     Route::get('/services/{service}/order', 'ServiceController@orderChange')->name('order_change');
    //     Route::get('/slots/{id}/filter', 'SlotController@filterSlot')->name('slots.filter');
    //     Route::post('/add-sub-services', 'ProviderController@addSubServices')->name('add-sub-services.store');
    //     Route::delete('/delete-sub-service/{id}', 'ProviderController@deleteSubServices')->name('delete-sub-services.store');
    //     Route::get('/bookings/{shop}/{type}/datas', 'BookingController@filter')->name('bookings.filter');
    //     Route::get('/bookings/{book}/{shop}/{type}/datas', 'BookingController@bookFilter')->name('bookings.bookfilter');
    //     Route::post('/check-unique-car', 'CarProviderController@checkUniqueCar')->name('check-unique-car.index');
    //     Route::get('/provider-cars/{provider}/datas', 'CarProviderController@filter')->name('provider-cars.filter');
    //     Route::put('/provider-update/{id}', 'ProviderController@providerUpdate')->name('provider-update');

    //     Route::resource('/providers', 'ProviderController');
    //     Route::resource('/bookings', 'BookingController');
    //     Route::resource('/provider-cars', 'CarProviderController');
    //     Route::resource('/users', 'UserController');
    //     Route::resource('/cars', 'CarController');
    //     Route::resource('/services', 'ServiceController');
    //     Route::resource('/sub-services', 'SubServiceController');
    //     Route::resource('/slots', 'SlotController');
    //     });
    // });