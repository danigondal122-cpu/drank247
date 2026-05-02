<?php

use App\Http\Controllers\Api\ApiDeliveryController;
use App\Http\Controllers\Api\ApiLoginController;
use App\Http\Controllers\Api\ApiOrderController;
use App\Http\Controllers\Api\ApiProfileController;
use App\Http\Controllers\Api\ApiScheduleController;
use App\Http\Controllers\CustomerApp\AddressController;
use App\Http\Controllers\CustomerApp\CartController;
use App\Http\Controllers\CustomerApp\CategoryController;
use App\Http\Controllers\CustomerApp\ContactUsController;
use App\Http\Controllers\CustomerApp\FavouriteController;
use App\Http\Controllers\CustomerApp\LoginController as CustomerLoginController;
use App\Http\Controllers\CustomerApp\OrderController;
use App\Http\Controllers\CustomerApp\ProductController;
use App\Http\Controllers\CustomerApp\ProfileController as CustomerProfileController;
use App\Http\Controllers\CustomerApp\SignUpController;
use App\Http\Controllers\FranchiseApp\DeliverypersonController;
use App\Http\Controllers\FranchiseApp\FSStockController;
use App\Http\Controllers\FranchiseApp\LoginController as FranchiseLoginController;
use App\Http\Controllers\FranchiseApp\ProfileController as FranchiseProfileController;
use App\Http\Controllers\TakeawayController;
use App\Http\Controllers\UberController;
use App\Http\Middleware\AddContext;
use App\Models\Franchise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//# Takeaway ##
Route::post('takeaway_save_data', [TakeawayController::class, 'takeawayStoreData']); // #!
// Route::post('takeaway_order_status', [TakeawayController::class, 'takeawayOrderStatus']);

// Uber APIs
Route::post('uber-webhook', [UberController::class, 'uberWebhook'])
    ->middleware(AddContext::class); // #! TEST WEBHOOK REQUIRED

Route::group(['namespace' => 'Api'], function () {
    //# login ##
    Route::post('login', [ApiLoginController::class, 'login']);// #!

    //# logout ##
    Route::post('logOut', [ApiLoginController::class, 'logout']);// #!

    //# ForgotPassword ##
    Route::post('forgotPassword', [ApiLoginController::class, 'forgotPassword']);// #!
});

Route::group(['namespace' => 'Api', 'middleware' => 'authApi'], function () {

    //# Profile ##
    Route::post('getProfile', [ApiProfileController::class, 'getProfile']);// #!
    Route::post('updateProfileDetail', [ApiProfileController::class, 'updateProfileDetail']);// #!
    Route::post('updateProfile', [ApiProfileController::class, 'updateProfile']);// #!
    Route::post('uploadDocument', [ApiProfileController::class, 'uploadDocument']);// #!
    //# Change Password ##
    Route::post('changePassword', [ApiProfileController::class, 'changePassword']);// #!

    //# online/Offline ##
    Route::post('onlineOffline', [ApiLoginController::class, 'onlineOffline']);// #!

    //# orderlist ##
    Route::post('listOrders', [ApiOrderController::class, 'listOrders']);// #!
    Route::post('orderDetail', [ApiOrderController::class, 'orderDetail']);// #!
    Route::post('updateOrderStatus', [ApiOrderController::class, 'updateOrderStatus']);// #!

    //# Schedulelist ##
    Route::post('updateScheduleStatus', [ApiScheduleController::class, 'updateScheduleStatus']);// #!
    Route::post('RequestSchedulelist', [ApiScheduleController::class, 'RequestSchedulelist']);// #!
    Route::post('ApproveSchedulelist', [ApiScheduleController::class, 'ApproveSchedulelist']);// #!
    Route::post('setAbsencedate', [ApiScheduleController::class, 'setAbsencedate']);// #!
    Route::post('absenceList', [ApiScheduleController::class, 'absenceList']);// #!
    Route::post('deleteAbsence', [ApiScheduleController::class, 'deleteAbsence']);// #!

    Route::post('startDelivery', [ApiDeliveryController::class, 'startDelivery']);// #!
    Route::post('endDelivery', [ApiDeliveryController::class, 'endDelivery']);// #!
    Route::post('updateLatLng', [ApiDeliveryController::class, 'updateLatLng']);// #!
    Route::post('rateAndReview', [ApiDeliveryController::class, 'rateAndReview']);// #!

    //# Working Hour List ##
    Route::post('workingHours', [ApiDeliveryController::class, 'workingHours']);// #!

    //# FOr Help ##
    Route::post('help', [ApiDeliveryController::class, 'saveHelpdata']);// #!
    Route::post('helplist', [ApiDeliveryController::class, 'helpList']);// #!

    //# Product and category List ##
    // Route::post('insertProduct', [ApiDeliverect::class, 'insertProduct']);// #!

});

Route::group(['namespace' => 'CustomerApp'], function () {

    //# SignUP ##
    Route::post('customer/signUpWithSocial', [SignUpController::class, 'signUpWithSocial']);// #!
    Route::post('customer/signUp', [SignUpController::class, 'signUp']);// #!
    //# login ##
    Route::post('customer/login', [CustomerLoginController::class, 'login']);// #!
    //# ForgotPassword ##
    Route::post('customer/forgotPassword', [CustomerLoginController::class, 'forgotPassword']);// #!
    //# logout ##
    Route::post('customer/logOut', [CustomerLoginController::class, 'logout']);// #!
    //# Profile ##
    Route::post('customer/getProfile', [CustomerProfileController::class, 'getProfile']);// #!
    Route::post('customer/updateProfileDetail', [CustomerProfileController::class, 'updateProfileDetail']);// #!
    //# Change Password ##
    Route::post('customer/changePassword', [CustomerProfileController::class, 'changePassword']);// #!
    //#Category list And Subcategory list ##
    Route::post('customer/categoryList', [CategoryController::class, 'categoryList']);// #!
    Route::post('customer/subcategorylist', [CategoryController::class, 'subcategorylist']);// #!
    //# Product list And Detail ##
    Route::post('customer/productList', [ProductController::class, 'productList']);// #!
    Route::post('customer/assignExtraProductList', [ProductController::class, 'assignExtraProductList']);// #!
    Route::post('customer/productDetail', [ProductController::class, 'productDetail']);// #!
    //# Popular Product list ##
    Route::post('customer/popularProductList', [ProductController::class, 'popularProductList']);// #!
    //# Favourite Product list ##
    Route::post('customer/favouriteProductList', [FavouriteController::class, 'favouriteProductList']);// #!
    Route::post('customer/addToFavourite', [FavouriteController::class, 'addToFavourite']);// #!
    Route::post('customer/removeFromFavourite', [FavouriteController::class, 'removeFromFavourite']);// #!
    //# Cart##
    Route::post('customer/cartList', [CartController::class, 'cartList']);// #!
    Route::post('customer/getCartCounts ', [CartController::class, 'getCartCounts']);// #!
    Route::post('customer/updateQty', [CartController::class, 'updateQty']);// #!
    Route::post('customer/addToCart', [CartController::class, 'addToCart']);// #!
    Route::post('customer/removeFromCart', [CartController::class, 'removeFromCart']);// #!
    Route::post('customer/customizedAddToCart', [CartController::class, 'customizedAddToCart']);// #!
    Route::post('customer/addToCartAfterLogin', [CartController::class, 'addToCartAfterLogin']);// #!
    //# Address
    Route::post('customer/addressList', [AddressController::class, 'addressList']);// #!
    Route::post('customer/addUpdateAddress', [AddressController::class, 'addUpdateAddress']);// #!
    Route::post('customer/setDefaultAddress', [AddressController::class, 'setDefaultAddress']);// #!
    Route::post('customer/addManualAddress', [AddressController::class, 'addManualAddress']);// #!
    Route::post('customer/deleteAddress', [AddressController::class, 'deleteAddress']);// #!
    //# Order list ##
    Route::post('customer/orderList', [OrderController::class, 'orderList']);// #!
    Route::post('customer/orderDetail', [OrderController::class, 'orderDetail']);// #!
    //# Order Detail ##
    Route::post('customer/checkOut', [OrderController::class, 'checkOut']);// #!
    Route::post('customer/getdeliverycharge', [OrderController::class, 'getdeliverycharge']);// #!
    Route::post('customer/guestcheckOut', [OrderController::class, 'guestCheckout']);// #!
    Route::post('customer/checkPostCode', [CustomerLoginController::class, 'checkPostCode']);// #!
    Route::post('customer/checkPromoCode', [CustomerLoginController::class, 'checkPromoCode']);// #!
    Route::post('customer/orderStatus', [OrderController::class, 'orderStatus']);// #!

    //# Order list ##
    Route::post('customer/bannerList', [CustomerProfileController::class, 'bannerList']);// #!

    //# Contact US ##
    Route::post('customer/contactUs', [ContactUsController::class, 'contactUs']);// #!

    //# delievery time list
    Route::post('customer/timeList', [CustomerLoginController::class, 'getDeliveryTimeList']);// #!

});

// TODO!: rename -> auth:sanctum,auth:customer?
Route::middleware(['authApi'])->group(function () {

    // For Delivery App

});

// TODO!: rename -> auth:sanctum,auth:customer?
Route::middleware(['customerAppAuth'])->group(function () {

    // For Customer App

});

Route::group(['namespace' => 'FranchiseApp'], function () {

    //# login ##
    Route::post('franchise/login', [FranchiseLoginController::class, 'login']);// #!
    //# logout ##
    Route::post('franchise/logout', [FranchiseLoginController::class, 'logout']);// #!
    //# ForgotPassword ##
    Route::post('franchise/forgotPassword', [FranchiseLoginController::class, 'forgotPassword']);// #!
    //# Get Pools ##
    Route::get('franchise/getPools', [FranchiseLoginController::class, 'getPools']); // #!
    //# Get Warehouse ##
    Route::get('franchise/getWarehouse', [FSStockController::class, 'getWarehouse']); // #!

});

Route::middleware(['middleware' => 'franchiseAppAuth'])->group(function () {

    // For Franchise App
    Route::group(['namespace' => 'FranchiseApp'], function () {

        //# Profile ##
        Route::post('franchise/getProfile', [FranchiseProfileController::class, 'getProfile']);// #!
        Route::post('franchise/updateProfileDetail', [FranchiseProfileController::class, 'updateProfileDetail']);// #!
        //# Change Password ##
        Route::post('franchise/changePassword', [FranchiseProfileController::class, 'changePassword']);// #!
        //#Delivery Person list ##
        Route::post('franchise/deliveryperson', [DeliverypersonController::class, 'getList']);// #!
        //#Delivery Person create ##
        Route::post('franchise/add-edit-deliveryperson', [DeliverypersonController::class, 'create']);// #!
        //#Delivery Person updateonoff ##
        Route::post('franchise/deliveryperson-updateonoff', [DeliverypersonController::class, 'updateonoff']);// #!
        //#Delivery Person view ##
        Route::post('franchise/deliveryperson-view', [DeliverypersonController::class, 'viewDeliveryperson']);// #!
        //#Delivery Person historydetail ##
        Route::post('franchise/deliveryperson-historydetail', [DeliverypersonController::class, 'historydetail']);// #!
        //#Delivery Person delete ##
        Route::post('franchise/deliveryperson-delete', [DeliverypersonController::class, 'deleteDelivery']);// #!
        //#Delivery history export ##
        Route::post('franchise/deliveryperson-export', [DeliverypersonController::class, 'historyHoursExport']);// #!
        //#Orders list ##
        Route::post('franchise/orders-list', [OrderController::class, 'getOrders']);// #!
        //#Orders list ##
        Route::post('franchise/orders-updatestatus', [OrderController::class, 'updateOrderstatus']);// #!
        //#Orders view ##
        Route::post('franchise/orders-view', [OrderController::class, 'orderView']);// #!
        //#Stock List ##
        Route::post('franchise/stock-list', [FSStockController::class, 'getList']);// #!
    });

});
