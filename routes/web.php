<?php

use App\Http\Controllers\Guest\GuestAjaxController;
use App\Http\Controllers\Guest\GuestPageController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// import admin controller
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCmsController;
use App\Http\Controllers\Admin\AdminHelpController;
use App\Http\Controllers\Admin\AdminPoolController;
use App\Http\Controllers\Admin\AdminStockController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminMessageController;
use App\Http\Controllers\Admin\AdminCmsPageController;
use App\Http\Controllers\Admin\AdminAllergenController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminContactUsController;
use App\Http\Controllers\Admin\AdminPromoCodeController;
use App\Http\Controllers\Admin\AdminUberAdminController;
use App\Http\Controllers\Admin\AdminWarehouseController;
use App\Http\Controllers\Admin\AdminStockOrderController;
use App\Http\Controllers\Admin\AdminExtraProductController;
use App\Http\Controllers\Admin\AdminPaymentMethodController;
use App\Http\Controllers\Admin\AdminUpdateProfileController;
use App\Http\Controllers\Admin\AdminChangePasswordController;
use App\Http\Controllers\Admin\AdminWarehouseStockController;
use App\Http\Controllers\Admin\AdminWarehouseProductController;
use App\Http\Controllers\Admin\AdminWarehouseStockOrderController;

use App\Http\Controllers\CustomerService\CSContactUsController;
use App\Http\Controllers\CustomerService\CSDeliveryPersonController;
use App\Http\Controllers\CustomerService\CSHelpController;
use App\Http\Controllers\CustomerService\CSHoursController;
use App\Http\Controllers\CustomerService\CSOrderController;
use App\Http\Controllers\CustomerService\CSProfileController;
use App\Http\Controllers\CustomerService\CustomerServiceController;

use App\Http\Controllers\Franchise\FSApiStockOrderController;
use App\Http\Controllers\Franchise\FranchiseController;
use App\Http\Controllers\Franchise\FSController;
use App\Http\Controllers\Franchise\FSDeliveryPersonController;
use App\Http\Controllers\Franchise\FSHelpController;
use App\Http\Controllers\Franchise\FSHoursController;
use App\Http\Controllers\Franchise\FSOrderController;
use App\Http\Controllers\Franchise\FSProfileController;
use App\Http\Controllers\Franchise\FSScheduleController;
use App\Http\Controllers\Franchise\FSStockController;

use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Customer\CustomerProfileController;
use App\Http\Controllers\Customer\CustomerCartController;
use App\Http\Controllers\Customer\CustomerOrderController;
use App\Http\Controllers\Customer\CustomerAJAXController;
use App\Http\Controllers\Customer\CustomerWebAuthController;
use App\Http\Controllers\Customer\CustomerSocialAuthController;


use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Base\SyncController;

use App\Http\Controllers\Authentication\ResetPasswordController;
use App\Http\Controllers\Authentication\PasswordResetLinkController;
use App\Http\Controllers\Authentication\AuthenticatedSessionController;
use App\Http\Controllers\CustomerService\CSRouteController;
use App\Http\Controllers\DeliveryPersonController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\Franchise\FranchiseGetOrderDataOfYear;
use App\Http\Controllers\Franchise\FranchiseReportingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderInvoiceController;
use App\Http\Controllers\OrderPdfController;
use App\Http\Controllers\SyncProductFromStockController;
use App\Http\Middleware\AuthGuardSegment;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// TODO: uncomment loader_show() di public\js\page\common.js setelah semua selesai
// Route::get('/', function () {
//     return view('welcome');
// })->name('home');

Route::post('timezone', function (Request $request) {
    Session::put('time_zone', $request->timeZone);
    return response()->json(['message' => $request->timeZone]);
});

Route::get('order/downloadPDF/{id}', [OrderPdfController::class, 'downloadPDF']); #!
Route::get('order/cronforinvoicepdf', [OrderInvoiceController::class, 'downloadInvoicePdf']); #!

Route::group(['prefix' => 'admin'], function () {
    Route::middleware('guest:admin')->group(function () {
        Route::middleware(AuthGuardSegment::class)->group(function () {
            Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('admin.login'); // Done
            Route::post('login', [AuthenticatedSessionController::class, 'store']); // Done
            Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('admin.forgot_password'); // Done
            Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('admin.password.email'); // Done
            Route::get('reset-password/{id}/{token}', [ResetPasswordController::class, 'create'])->name('admin.password.reset'); // Done
            Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('admin.password.store'); // Done
        });
    });

    Route::group(['middleware' => 'auth:admin'], function () {
        /** Profile: Done */
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard'); // Done
        Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('admin.logout'); // Done
        Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile'); // Done
        Route::put('/profile-update', AdminUpdateProfileController::class)->name('admin.profile.update'); // Done
        Route::get('/change-password', [AdminController::class, 'changePassword'])->name('admin.change_password'); // Done
        Route::put('/change-password', AdminChangePasswordController::class)->name('admin.process.change_password.'); // Done

        /** Admin: Done */
        Route::get('/admin/add', [AdminController::class, 'adminAdd']); // Done
        Route::get('/admin/edit/{id}', [AdminController::class, 'adminEdit']); // Done
        Route::get('/admin/list', [AdminController::class, 'adminList']); // Done
        Route::post('/admin/add', [AdminController::class, 'save']); // Done
        Route::post('/admin/list', [AdminController::class, 'getList']); // Done
        Route::post('/admin/delete', [AdminController::class, 'deleteAdmin']); // Done
        Route::post('/admin/updateAssignmodule', [AdminController::class, 'updateAssignmodule']); // Done
        Route::post('/admin/changeaccountant', [AdminController::class, 'changeaccountant']); // Done
        Route::get('/admin/showassignmodule', [AdminController::class, 'showassignmodule']); // Done

        /** Allergen: Done  */
        Route::get('/allergen/add', [AdminAllergenController::class, 'create']); // Done
        Route::get('/allergen/edit/{id}', [AdminAllergenController::class, 'edit']); // Done
        Route::get('/allergen/list', [AdminAllergenController::class, 'index']); // Done
        Route::post('/allergen/add', [AdminAllergenController::class, 'save']); // Done
        Route::post('/allergen/list', [AdminAllergenController::class, 'getList']); // Done
        Route::post('/allergen/delete', [AdminAllergenController::class, 'deleteAllergen']); // Done

        /** category: Done */
        Route::get('/category/list', [AdminCategoryController::class, 'index']); // Done
        Route::get('/category/add', [AdminCategoryController::class, 'create']); // Done
        Route::get('/category/edit/{category}', [AdminCategoryController::class, 'edit']); // Done
        Route::get('/category/subcategorylist/{category}', [AdminCategoryController::class, 'subCategoryList']); // Done
        Route::get('/category/assignproduct/{category}', [AdminCategoryController::class, 'assignProduct']); // Done
        Route::post('/category/add', [AdminCategoryController::class, 'save']); // Done
        Route::post('/category/list', [AdminCategoryController::class, 'getList']); // Done
        Route::delete('/category/{category}', [AdminCategoryController::class, 'destroy']); // Done
        Route::post('/category/updateCategoryOrder', [AdminCategoryController::class, 'updateCategoryOrder']); // Done
        Route::post('/subcategory/list', [AdminCategoryController::class, 'getSubList']); // Done
        Route::post('/category/assignProductSave', [AdminCategoryController::class, 'assignProductSave']); // Done

        /** products: Done */
        Route::get('/product/add', [AdminProductController::class, 'create']); // Done
        Route::get('/product/edit/{id}', [AdminProductController::class, 'edit']); // Done
        Route::get('/product/list', [AdminProductController::class, 'index']); // Done
        Route::get('/product/sessionDestroy', [AdminProductController::class, 'sessionDestroy']); // Done
        Route::post('/product/add', [AdminProductController::class, 'save']); // Done
        Route::post('/product/list', [AdminProductController::class, 'getList']); // Done
        Route::post('/product/delete', [AdminProductController::class, 'deleteProduct']); // Done
        Route::post('/product/updateProductOrder', [AdminProductController::class, 'updateProductOrder']); // Done

        /** Extra Items: Done */
        Route::get('/extraproduct/list', [AdminExtraProductController::class, 'index']); // Done
        Route::get('/extraproduct/add', [AdminExtraProductController::class, 'create']); // Done
        Route::get('/extraproduct/edit/{id}', [AdminExtraProductController::class, 'edit']); // Done
        Route::post('/extraproduct/add', [AdminExtraProductController::class, 'save']); // Done
        Route::post('/extraproduct/list', [AdminExtraProductController::class, 'getList']); // Done
        Route::post('/extraproduct/delete', [AdminExtraProductController::class, 'deleteProduct']); // Done

        /** warehouse: Done */
        Route::get('/warehouse/list', [AdminWarehouseController::class, 'index']); // Done
        Route::get('/warehouse/add', [AdminWarehouseController::class, 'create']); // Done
        Route::get('/warehouse/edit/{warehouse}', [AdminWarehouseController::class, 'edit']); // Done
        Route::post('/warehouse/list', [AdminWarehouseController::class, 'getList']); // Done
        Route::post('/warehouse/add', [AdminWarehouseController::class, 'save']); // Done
        Route::delete('/warehouse/{id}', [AdminWarehouseController::class, 'destroy']); // Done

        /** warehouse product: Done */
        Route::get('/warehouseproduct/list', [AdminWarehouseProductController::class, 'index']); // Done
        // Route::get('/warehouseproduct/add', [AdminWarehouseProductController::class, 'wareHouseAdd']); // TODO!: Remove this later
        // Route::get('/warehouseproduct/edit/{id}', [AdminWarehouseProductController::class, 'wareHouseEdit']); // TODO!: Remove this later
        Route::post('/warehouseproduct/list', [AdminWarehouseProductController::class, 'getList']); // Done
        Route::post('/warehouseproduct/changeProductPrice', [AdminWarehouseProductController::class, 'changeProductPrice']); // Done
        Route::get('/warehousestock/list', [AdminWarehouseStockController::class, 'index']); // Done
        Route::post('/warehousestock/changeStock', [AdminWarehouseStockController::class, 'changeStock']); // Done
        Route::get('/warehousestockorder/list', [AdminWarehouseStockOrderController::class, 'wareHouseStockOrderList']); // Done
        Route::post('/warehousestockorder/orderlist', [AdminWarehouseStockOrderController::class, 'getOrderList']); // Done
        Route::get('/warehousestockorder/view/{id}', [AdminWarehouseStockOrderController::class, 'wareHouseStockOrderView']); // Done

        /** pool: Done */
        Route::get('/pool/list', [AdminPoolController::class, 'index']); // Done
        Route::get('/pool/add', [AdminPoolController::class, 'create']); // Done
        Route::get('/pool/edit/{id}', [AdminPoolController::class, 'edit']); // Done
        Route::post('/pool/add', [AdminPoolController::class, 'save']); // Done
        Route::post('/pool/list', [AdminPoolController::class, 'getList']); // Done
        Route::post('/pool/delete', [AdminPoolController::class, 'deletePool']); // Done

        /** Customer Services: Done */
        Route::get('/customerservice/add', [CustomerServiceController::class, 'adminCreate']); // Done
        Route::get('/customerservice/edit/{id}', [CustomerServiceController::class, 'adminEdit']); // Done
        Route::get('/customerservice/list', [CustomerServiceController::class, 'adminIndex']); // Done
        Route::post('/customerservice/add', [CustomerServiceController::class, 'save']); // Done
        Route::post('/customerservice/list', [CustomerServiceController::class, 'getList']); // Done
        Route::post('/customerservice/delete', [CustomerServiceController::class, 'deleteCustomer']); // Done
        Route::post('/customerservice/getDocument', [CustomerServiceController::class, 'getDocument']); // Done
        Route::get('/customerservice/hours/list/{id}', [CustomerServiceController::class, 'hoursList']); // Done
        Route::post('/customerservice/hours/lists', [CustomerServiceController::class, 'getHoursList']); // Done

        /** franchise: Done */
        Route::get('/franchise/add', [FranchiseController::class, 'adminCreate']); // Done
        Route::get('/franchise/edit/{id}', [FranchiseController::class, 'adminEdit']); // Done
        Route::get('/franchise/list', [FranchiseController::class, 'adminIndex']); // Done
        Route::post('/franchise/add', [FranchiseController::class, 'save']); // Done
        Route::post('/franchise/list', [FranchiseController::class, 'getList']); // Done
        Route::post('/franchise/delete', [FranchiseController::class, 'deleteFrenchise']); // Done
        Route::post('/franchise/getDocument', [FranchiseController::class, 'getDocument']); // Done
        Route::post('/franchise/updateonoff', [FranchiseController::class, 'updateonoff']); // Done

        /** Stock: Done */
        Route::get('/stock/add', [AdminStockController::class, 'create']); // Done
        Route::get('/stock/edit/{id}', [AdminStockController::class, 'edit']); // Done
        Route::get('/stock/list', [AdminStockController::class, 'index']); // Done
        Route::post('/stock/add', [AdminStockController::class, 'save']); // Done
        Route::post('/stock/list', [AdminStockController::class, 'getList']); // Done
        Route::post('/stock/delete', [AdminStockController::class, 'deleteStock']); // Done
        Route::post('/stock/getCategory', [AdminStockController::class, 'getCategory']); // Done

        /** Customer: Done */
        // Route::get('/customer/add', [CustomerController::class, 'adminAdd']); // TODO!: remove this later
        Route::get('/customer/edit/{id}', [CustomerController::class, 'adminEdit']); // TODO!: remove this later?
        Route::get('/customer/list', [CustomerController::class, 'adminIndex']); // Done
        // Route::post('/customer/add', [CustomerController::class, 'save']); // TODO!: remove this later
        Route::post('/customer/list', [CustomerController::class, 'getList']); // Done
        Route::post('/customer/delete', [CustomerController::class, 'deleteCustomer']); // Done

        /** Delivery Person: Done */
        Route::get('/deliveryperson/add', [DeliveryPersonController::class, 'adminCreate']); // Done
        Route::get('/deliveryperson/edit/{id}', [DeliveryPersonController::class, 'adminEdit']); // Done
        Route::get('/deliveryperson/list', [DeliveryPersonController::class, 'adminIndex']); // Done
        Route::get('/deliveryperson/list/map', [DeliveryPersonController::class, 'deliveryPersonMap']); // Done
        Route::get('/deliveryperson/schedule', [DeliveryPersonController::class, 'deliveryTimeschedule']); // Done
        Route::post('/deliveryperson/add', [DeliveryPersonController::class, 'save']); // Done
        Route::post('/deliveryperson/list', [DeliveryPersonController::class, 'getList']); // Done
        Route::post('/deliveryperson/delete', [DeliveryPersonController::class, 'deleteDelivery']); // Done
        Route::post('/deliveryperson/getStartTime', [DeliveryPersonController::class, 'getStartTime']); // Done
        Route::post('/deliveryperson/getChecked', [DeliveryPersonController::class, 'getChecked']); // // Done
        Route::post('/deliveryperson/scheduleOnOff', [DeliveryPersonController::class, 'scheduleOnOff']); // Done
        Route::post('/deliveryperson/getFranchises', [DeliveryPersonController::class, 'getFranchises']); // Done
        Route::post('/deliveryperson/getDocument', [DeliveryPersonController::class, 'getDocument']); // Done
        Route::post('/deliveryperson/updateonoff', [DeliveryPersonController::class, 'updateonoff']); // Done
        Route::post('/deliveryperson/map', [DeliveryPersonController::class, 'map']); // Done

        /** Uber Store: Done */
        Route::get('/uber/storelist', [AdminUberAdminController::class, 'index']); // Done
        Route::get('/uber/storeview/{id}', [AdminUberAdminController::class, 'storeView']); // Done
        Route::post('/uber/storelist', [AdminUberAdminController::class, 'storeList']); // Done
        Route::post('/uber/syncUberMenu', [AdminUberAdminController::class, 'syncUberMenu']); // Done
        Route::post('/uber/getProductList', [AdminUberAdminController::class, 'getProductList']); // Done
        Route::post('/uber/get_uber_store_list', [AdminUberAdminController::class, 'getStoreList']); // Done
        Route::post('/uber/get_uber_stores_menu', [AdminUberAdminController::class, 'getStoreMenu']); // Done
        Route::post('/uber/update_store_item', [AdminUberAdminController::class, 'updateStoreItem']); // Done

        /** Order: TODO: */
        // Route::get('/order/add', [OrderController::class, 'orderAdd']); // #! ------
        Route::get('/order/edit/{id}', [OrderController::class, 'orderEdit']); // Done
        Route::get('/order/view/{id}', [OrderController::class, 'orderView']); // Done
        Route::get('/order/list', [OrderController::class, 'orderList']); // Done
        Route::get('/order/invoice-pdf', [OrderController::class, 'franchiseinvoice']); // Done
        Route::post('/order/orderlist', [OrderController::class, 'orderInvoicelist']); // Done
        Route::get('/order/all-invoice', [OrderController::class, 'franchiseinvoicelist']); // Done
        Route::post('/order/invoicelist', [OrderController::class, 'getinvoicePdfList']); // Done
        // Route::post('/order/add', [OrderController::class, 'save']); // #! ------
        Route::post('/order/list', [OrderController::class, 'getList']); // Done
        Route::post('/order/delete', [OrderController::class, 'deleteOrder']); // #! ------
        Route::post('/order/updatestatus', [OrderController::class, 'updateStatus']); // Done
        Route::post('/order/export', [ExcelController::class, 'orderExport']); // Done
        Route::get('/edit-franchise-invoice', [OrderController::class, 'editInvoice']); // Done
        Route::get('/saveOrderchannel', [OrderController::class, 'saveOrderchannel']); // Done
        Route::get('/saveallOrderchannel', [OrderController::class, 'saveallOrderchannel']); // Done

        /** Stock Order: TODO: */
        Route::get('/stockorder/view/{id}', [AdminStockOrderController::class, 'stockOrderView']); // Done
        Route::get('/stockorder/list', [AdminStockOrderController::class, 'stockOrderList']); // Done
        Route::post('/stockorder/list', [AdminStockOrderController::class, 'getList']); // Done

        /** Promo code: Done  */
        Route::get('/promocode/add', [AdminPromoCodeController::class, 'create']); // Done
        Route::get('/promocode/edit/{id}', [AdminPromoCodeController::class, 'edit']); // Done
        Route::get('/promocode/view/{id}', [AdminPromoCodeController::class, 'promoCodeView']); // Done
        Route::get('/promocode/list', [AdminPromoCodeController::class, 'index']); // Done
        Route::post('/promocode/add', [AdminPromoCodeController::class, 'save']); // Done
        Route::post('/promocode/list', [AdminPromoCodeController::class, 'getList']); // Done
        Route::post('/promocode/delete', [AdminPromoCodeController::class, 'deletePromocode']); // Done
        Route::post('/promocode/ActivateCode', [AdminPromoCodeController::class, 'ActivateCode']); // Done
        Route::post('/promocode/viewPromoCodeOrder', [AdminPromoCodeController::class, 'viewPromoCodeOrder']); // Done

        /** Contact us: Done  */
        Route::get('/contactus/list', [AdminContactUsController::class, 'index']); // Done
        Route::post('/contactus/list', [AdminContactUsController::class, 'getList']); // Done
        Route::post('/contactus/delete', [AdminContactUsController::class, 'deleteContactUs']); // Done

        /** Help: DONE  */
        Route::get('/help/list', [AdminHelpController::class, 'helpList']); // Done
        Route::post('/help/list', [AdminHelpController::class, 'getList']); // Done
        Route::post('/help/delete', [AdminHelpController::class, 'deleteHelp']); // Done
        Route::post('/help/updatestatus', [AdminHelpController::class, 'updateStatus']); // #!------

        /** Broadcast Message: DONE */
        Route::get('/message/add', [AdminMessageController::class, 'create']); // Done
        Route::get('/message/edit/{id}', [AdminMessageController::class, 'edit']); // Done
        Route::get('/message/list', [AdminMessageController::class, 'index']); // Done
        Route::get('/message/view/{id}', [AdminMessageController::class, 'show']); // Done
        Route::post('/message/add', [AdminMessageController::class, 'save']); // Done
        Route::post('/message/list', [AdminMessageController::class, 'getList']); // Done
        Route::post('/message/delete', [AdminMessageController::class, 'deleteMessage']); // Done

        /** Payment Methods: Done */
        Route::get('/paymentmethod', [AdminPaymentMethodController::class, 'paymentmethodlist']); // Done
        Route::post('/paymentmethod/save', [AdminPaymentMethodController::class, 'paymentmethodsave']); // Done

        /** Settings: Done */
        Route::get('/settings', [AdminSettingsController::class, 'settings']); // Done
        Route::post('/settings', [AdminSettingsController::class, 'update']); // Done
        Route::get('/settings/banner', [AdminSettingsController::class, 'web_banner']); // Done
        Route::post('/settings/updateBanner', [AdminSettingsController::class, 'updateBanner']); // Done
        Route::post('/settings/deleteBanner', [AdminSettingsController::class, 'deleteBanner']); // Done

        /** CMS: Done */
        Route::post('/cms/saveCms', [AdminCmsController::class, 'saveCms']); // Done
        Route::post('/cms/getCmsDetail', [AdminCmsController::class, 'getCmsDetail']); // Done
        Route::get('/cms/privacy_policy', [AdminCmsController::class, 'privacyPolicy']); // Done
        Route::get('/cms/terms_condition', [AdminCmsController::class, 'termsAndCondition']); // Done
        Route::get('/cms/colophone', [AdminCmsController::class, 'coloPhone']); // Done
        Route::get('/cms/cookie_statement', [AdminCmsController::class, 'cookieStatement']); // Done
        Route::get('/cms/alcohol_law', [AdminCmsController::class, 'alcoholLaw']); // Done
        Route::get('/cms/technology', [AdminCmsController::class, 'Technology']); // Done
    });
});

Route::group(['prefix' => 'customer_service'], function () {
    Route::middleware('guest:customer_service')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('customer_service.login'); // Done
        Route::post('login', [AuthenticatedSessionController::class, 'store']); // Done
        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('customer_service.forgot_password'); // Done
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('customer_service.password.email'); // Done
        Route::get('reset-password/{id}/{token}', [ResetPasswordController::class, 'create'])->name('customer_service.password.reset'); // Done
        Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('customer_service.password.store'); // Done
    });

    Route::group(['middleware' => 'auth:customer_service'], function () {
        Route::get('/dashboard', [CustomerServiceController::class, 'adminDashboard'])->name('customer_service.dashboard'); // Done

        /** Profile */
        Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('customer_service.logout'); // Done
        Route::get('/profile', [CSProfileController::class, 'profile']); // Done
        Route::get('/changepassword', [CSProfileController::class, 'changePassword']); // Done
        Route::post('/profileupdate', [CSProfileController::class, 'profileUpdate']); // Done
        Route::post('/changepassword', [CSProfileController::class, 'updatePassword']); // Done
        Route::post('/updateonoff', [CSProfileController::class, 'updateOnOff']); // Done
        Route::post('/notificationread', [CSProfileController::class, 'notificationReadUnread']); // Done

        /** Delivery Person */
        // Route::get('/deliveryperson/add', [CSDeliveryPersonController::class, 'deliveryAdd']); // Unused, Done
        Route::get('/deliveryperson/edit/{id}', [CSDeliveryPersonController::class, 'deliveryEdit']); // Done
        Route::get('/deliveryperson/list', [CSDeliveryPersonController::class, 'deliveryList']); // Done
        Route::post('/deliveryperson/add', [CSDeliveryPersonController::class, 'save']); // Done
        Route::post('/deliveryperson/list', [CSDeliveryPersonController::class, 'getList']); // Done
        Route::post('/deliveryperson/delete', [CSDeliveryPersonController::class, 'deleteDelivery']); // Done
        Route::post('/deliveryperson/getFranchises', [CSDeliveryPersonController::class, 'getFranchises']); // Done

        /** Order */
        // TODO!: Hapus ini nanti karena tidak dipakai
        // Route::get('/order/add', [CSOrderController::class, 'orderAdd']); // Done
        // Route::get('/order/edit/{id}', [CSOrderController::class, 'orderEdit']); // Done
        // Route::post('/order/add', [CSOrderController::class, 'save']); // Done
        // Route::post('/order/delete', [CSOrderController::class, 'deleteOrder']); // Done
        Route::get('/order/view/{id}', [CSOrderController::class, 'orderView']); // Done

        Route::get('/order/list', [CSOrderController::class, 'orderList']); // Done
        Route::post('/order/list', [CSOrderController::class, 'getList']); // Done
        Route::post('/order/orderapprovedPopup', [CSOrderController::class, 'orderapprovedPopup']); // Done
        Route::post('/order/orderApproved', [CSOrderController::class, 'orderApproved']); // Done
        Route::post('/order/orderCancelled', [CSOrderController::class, 'orderCancelled']); // Done
        Route::post('/order/showCancelledPopup', [CSOrderController::class, 'showCancelledPopup']); // Done
        Route::post('/order/updatestatus', [OrderController::class, 'updateStatus']); // NEED API TEST, Done
        Route::post('/order/ReassignPopup', [CSOrderController::class, 'ReassignPopup']); // Done
        Route::post('/order/Reassign', [CSOrderController::class, 'Reassign']); // Done

        /** franchise hide because unused. Only admin can CRUD franchise */
        // Route::get('/franchise/add', [CSFranchiseController::class, 'franchiseAdd']); // Done
        // Route::get('/franchise/edit/{id}', [CSFranchiseController::class, 'franchiseEdit']); // Done
        // Route::get('/franchise/list', [CSFranchiseController::class, 'franchiseList']); // Done
        // Route::post('/franchise/add', [CSFranchiseController::class, 'save']); // Done
        // Route::post('/franchise/list', [CSFranchiseController::class, 'getList']); // Done
        // Route::post('/franchise/delete', [CSFranchiseController::class, 'deleteFrenchise']); // Done

        /** Hours */
        Route::get('/hours/add', [CSHoursController::class, 'hoursAdd']); // Done
        Route::get('/hours/edit/{id}', [CSHoursController::class, 'hoursEdit']); // Done
        Route::get('/hours/list', [CSHoursController::class, 'hoursList']); // Done
        Route::post('/hours/add', [CSHoursController::class, 'save']); // Done
        Route::post('/hours/list', [CSHoursController::class, 'getList']); // Done
        Route::post('/hours/delete', [CSHoursController::class, 'deleteHour']); // Done

        /** Contact us  */
        Route::get('/contactus/list', [CSContactUsController::class, 'ContactUsList']); // Done
        Route::post('/contactus/list', [CSContactUsController::class, 'getList']); // Done
        Route::post('/contactus/delete', [CSContactUsController::class, 'deleteContactUs']); // Done

        /** Help  */
        Route::get('/help/list', [CSHelpController::class, 'helpList']); // Done
        Route::post('/help/list', [CSHelpController::class, 'getList']); // Done
        Route::post('/help/delete', [CSHelpController::class, 'deleteHelp']); // // Done
        Route::post('/help/updatestatus', [CSHelpController::class, 'updateStatus']); // // Done
    });
});

Route::group(['prefix' => 'franchise'], function () {
    Route::middleware('guest:franchise')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('franchise.login'); // Done
        Route::post('login', [AuthenticatedSessionController::class, 'store']); // Done
        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('franchise.forgot_password'); // Done
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('franchise.password.email'); // Done
        Route::get('reset-password/{id}/{token}', [ResetPasswordController::class, 'create'])->name('franchise.password.reset'); // Done
        Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('franchise.password.store'); // Done
    });

    Route::group(['middleware' => 'auth:franchise'], function () {
        Route::get('/dashboard', [FSController::class, 'adminDashboard'])->name('franchise.dashboard'); // Done
        Route::any('/getOrderdataOfyears', FranchiseGetOrderDataOfYear::class); // Done

        /** Profile */
        Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('franchise.logout'); // Done
        Route::get('/profile', [FSProfileController::class, 'profile']); // Done
        Route::get('/changepassword', [FSProfileController::class, 'changePassword']); // Done
        Route::post('/profileupdate', [FSProfileController::class, 'profileupdate']); // Done
        Route::post('/changepassword', [FSProfileController::class, 'updatePassword']); // Done
        Route::post('/updateonoff', [FSProfileController::class, 'updateOnOff']); // Done
        Route::post('/notificationread', [FSProfileController::class, 'notificationread']); // Done

        /** Stock */
        Route::get('/stock/add', [FSStockController::class, 'stockAdd']);  // Done
        Route::get('/stock/edit/{id}', [FSStockController::class, 'stockEdit']);  // Done
        Route::get('/stock/list', [FSStockController::class, 'stockList']);  // Done
        Route::post('/stock/add', [FSStockController::class, 'save']);  // Done
        Route::post('/stock/list', [FSStockController::class, 'getList']);  // Done
        Route::post('/stock/delete', [FSStockController::class, 'deleteStock']);  // Done
        Route::post('/stock/getCategory', [FSStockController::class, 'getCategory']);  // Done

        /** Delivery Person */
        Route::get('/deliveryperson/add', [FSDeliveryPersonController::class, 'deliveryAdd']); // Done
        Route::get('/deliveryperson/edit/{id}', [FSDeliveryPersonController::class, 'deliveryEdit']); // Done
        Route::get('/deliveryperson/list', [FSDeliveryPersonController::class, 'deliveryList']); // Done
        Route::get('/deliveryperson/view/{id}', [FSDeliveryPersonController::class, 'deliveryPersonView']); // Done
        // TODO!: untuk list delivery history, view delivery history, export history nanti buat controller sendiri?
        Route::get('/deliveryperson/historydetail/{id}', [FSDeliveryPersonController::class, 'historyDetail']); // Done
        Route::post('/deliveryperson/add', [FSDeliveryPersonController::class, 'save']); // Done
        Route::post('/deliveryperson/list', [FSDeliveryPersonController::class, 'getList']); // Done
        Route::post('/deliveryperson/dateList', [FSDeliveryPersonController::class, 'getDateList']); // Done
        Route::post('/deliveryperson/delete', [FSDeliveryPersonController::class, 'deleteDelivery']); // Done
        Route::post('/getDeliveryPersonList', [FSDeliveryPersonController::class, 'getDeliveryPersonList']); // Done
        Route::post('/getDeliveryPersonDetail', [FSDeliveryPersonController::class, 'getDeliveryPersonDetail']); // Done
        Route::post('/deliveryperson/updateonoff', [DeliveryPersonController::class, 'updateonoff']); // Done
        Route::post('/historyhours/export', [ExcelController::class, 'historyHoursExport']); // Done

        /** schedule  */
        Route::get('/schedule/add', [FSScheduleController::class, 'scheduleAdd']); // Done
        Route::get('/schedule/edit/{id}', [FSScheduleController::class, 'scheduleEdit']); // Done
        Route::get('/schedule/list', [FSScheduleController::class, 'scheduleList']); // Done
        Route::post('/schedule/add', [FSScheduleController::class, 'save']); // Done
        Route::post('/schedule/list', [FSScheduleController::class, 'getList']); // Done
        Route::post('/schedule/delete', [FSScheduleController::class, 'deleteSchedule']); // Done
        Route::post('/schedule/updateStatus', [FSScheduleController::class, 'updateStatus']); // Done
        Route::post('/schedule/getDeliveryPersonList', [FSScheduleController::class, 'getDeliveryPersonList']); // Done

        /** Hours  */
        Route::get('/hours/list', [FSHoursController::class, 'hourslist']); // Done
        Route::post('/hours/list', [FSHoursController::class, 'getList']); // Done
        Route::post('/hours/export', [ExcelController::class, 'export']); // Done

        /** Help  */
        Route::get('/help/list', [FSHelpController::class, 'helpList']); // Done
        Route::post('/help/list', [FSHelpController::class, 'getList']); // Done
        Route::post('/help/delete', [FSHelpController::class, 'deleteHelp']); // // Done
        Route::post('/help/updatestatus', [FSHelpController::class, 'updateStatus']); // // Done

        /** Order */
        Route::get('/order/list', [FSOrderController::class, 'orderList']); // Done
        Route::post('/order/list', [FSOrderController::class, 'getList']); // Done
        Route::post('/order/updatestatus', [OrderController::class, 'updateStatus']); // NEED API TEST, Done
        Route::get('/order/view/{id}', [FSOrderController::class, 'orderView']); // Done
        // Route::post('/order/delete', [FSOrderController::class, 'deleteOrder']); // Done. TODO!: Hapus ini nanti karena tidak dipakai

        /** Reporting */
        Route::get('/order/reporting', [FranchiseReportingController::class, 'reporting']); // Done
        Route::get('/order/getdate', [FranchiseReportingController::class, 'getStartAndEndDate']); // Done
        Route::get('/order/invoice', [FranchiseReportingController::class, 'invoiceReport']); // Done
        Route::post('/order/invoicelist', [FranchiseReportingController::class, 'getinvoicePdfList']); // Done
        Route::get('/order/generatereportPdf', [FranchiseReportingController::class, 'generateOrderPdf']); // Done
        Route::get('/order/generateinvoicePdf', [FranchiseReportingController::class, 'generateInvoicePdf']); // Done

        /** Stock Order */
        // TODO! Hide FSApiStockOrderController?
        // TODO! rename controller FSApiStockOrderController->FranchiseStockOrderController?
        Route::post('/stockproduct/list', [FSApiStockOrderController::class, 'processStockProductList']); //Done
        Route::get('/stockproduct/list', [FSApiStockOrderController::class, 'stockProductList']); // Done

        Route::get('/stockorder/view/{id}', [FSApiStockOrderController::class, 'stockOrderView']); // Done
        Route::get('/stockorder/list', [FSApiStockOrderController::class, 'stockOrderList']); // Done
        // Route::post('/stockorder/getProductStock', [FSApiStockOrderController::class, 'getProductStock']); // Done?
        Route::post('/stockorder/updateStock', [FSApiStockOrderController::class, 'updateStock']); // Done?
        Route::post('/stockorder/selectStock', [FSApiStockOrderController::class, 'selectStock']); // Done
        Route::post('/stockorder/sendStockOrder', [FSApiStockOrderController::class, 'sendStockOrder']); // Done?
        Route::post('/stockorder/removeStockOrder', [FSApiStockOrderController::class, 'removeStockOrder']); // Done
        Route::post('/stockorder/list', [FSApiStockOrderController::class, 'processStockOrderList']); // Done
        Route::post('/stockorder/changeorderstatus', [FSApiStockOrderController::class, 'changeOrderStatus']); // Done

        /** Stock Order */
        Route::get('/franchisestockorderfrom/list', [FSStockController::class, 'franchiseStockOrderFromList']); // Done
        Route::post('/franchisestockorderfrom/list', [FSStockController::class, 'processFranchiseStockOrderFromList']); // Done
        Route::get('/franchisestockorderfrom/FrStockOrderList', [FSStockController::class, 'FrStockOrderList']); // Done
        Route::get('/franchisestockorderfrom/view/{id}', [FSStockController::class, 'FranchiseStockOrderView']); // Done
        Route::post('/franchisestockorderfrom/placeOrderforStock', [FSStockController::class, 'placeOrderforStock']); // Done
        Route::post('/franchisestockorderfrom/FrStockOrderList', [FSStockController::class, 'processFrStockOrderList']); // Done
        Route::post('/franchisestockorderfrom/changeorderstatus', [FSStockController::class, 'changeOrderStatus']); // Done
        Route::post('/franchisestockorderfrom/changeproductstatus', [FSStockController::class, 'changeProductStatus']); // Done
    });
});

// |--------------------------------------------------------------------------
// | Guest/Coustomer Route
// |--------------------------------------------------------------------------

Route::get('locale/{locale}', function ($locale) {
    session()->put('locale', $locale);
    return redirect()->back();
})->name('locale');

Route::controller(GuestPageController::class)->group(function () {
    Route::get('/', 'home')->name('homepage');
    Route::get('contact_us', 'contactUs')->name('contact.us');
    Route::get('privacy_policy', 'privacyPolicy')->name('privacy.policy');
    Route::get('terms_condition', 'termsAndCondition')->name('terms.condition');
    Route::get('colophone', 'colophone')->name('colophone');
    Route::get('cookiestatement', 'cookieStatement')->name('cookie.statement');
    Route::get('alcohol_law', 'alcoholLaw')->name('alcohol.law');
    Route::get('technology', 'technology')->name('technology');

    Route::get('categories', 'categoryList')->name('category.list');
    Route::get('products/{category}', 'productList')->name('product.list');
    Route::get('cart', 'cart')->name('cart');
});

Route::controller(GuestAjaxController::class)->group(function () {
    Route::post('autocomplete', 'autocomplete');
    Route::post('contact_us', 'contactUs')->name('ajax.contact.us');
    Route::post('products/getProductDetail', 'productDetail');
    Route::post('order/getdeliverycharge', 'getDeliveryCharge');
    Route::post('order/guestcheckout', 'checkout');
    Route::post('customer/checkPostcode', 'checkPostCode');
});

Route::controller(CustomerCartController::class)->group(function () {
    Route::post('cart/add', 'addToCart');
    Route::post('cart/remove-item', 'removeItem');
    Route::post('cart/update-item-qty', 'updateItemQty');
    Route::post('products/customizedProduct', 'customizeProduct');
    Route::post('cart/customized-item-qty', 'customizedItemQty');
    Route::post('cart/remove_Customized_Item', 'removeCustomizedItem');
});

Route::group(['prefix' => 'customer'], function () {
    Route::middleware('guest')
        ->controller(CustomerWebAuthController::class)
        ->group(function () {
            Route::post('login', 'login')->name('customer.login');
            Route::post('register', 'register')->name('customer.register');
            Route::post('forgotPassword', 'forgotPassword')->name('customer.forgot.password');
            Route::get('resetpassword/{id}/{token}/', 'resetPassword')->name('customer.reset.password');
            Route::post('resetpassword', 'changePassword')->name('customer.change.password');
        });

    Route::middleware(['auth:customer'])->group(function () {
        Route::controller(CustomerProfileController::class)->group(function () {
            Route::post('update', 'updateProfile')->name('customer.update.profile');
            Route::post('changepassword', 'changePassword');
            Route::post('addupdateaddress', 'addUpdateFetchAddress');
            Route::get('address/{id}', 'addressDetails');
            Route::post('address/delete', 'addressDelete');
            Route::post('addmanualaddress', 'addUpdateManualAddress');
            Route::post('setdefaultaddress', 'setDefaultAddress');
            Route::get('addresses', 'addressList');
        });

        Route::post('rate-and-review', [CustomerAJAXController::class, 'rateAndReview'])->name('customer.rate.review');
        Route::get('select_address', [CustomerAJAXController::class, 'selectAddress']);
        Route::get('logout', [CustomerWebAuthController::class, 'logout'])->name('customer.logout');
    });

    Route::post('checkPromoCode', [CustomerAJAXController::class, 'checkPromoCode']);
});

Route::middleware('guest')->group(function () {
    Route::get('auth/{driver}', [CustomerSocialAuthController::class, 'redirect'])->name('social.auth');
    Route::get('auth/{driver}/callback', [CustomerSocialAuthController::class, 'callback']);
});

Route::middleware(['auth:customer'])->group(function () {
    Route::get('profile', [CustomerController::class, 'profile']);
    Route::get('favourite', [CustomerController::class, 'favourite']);
    Route::post('favourite/add', [CustomerAJAXController::class, 'favourite']);
    Route::post('order/getorderdetail', [CustomerAJAXController::class, 'getOrderDetail']);

    Route::get('paymentmethod/{id?}', [CustomerOrderController::class, 'paymentmethod']);
    Route::post('paynlPayment', [CustomerOrderController::class, 'paynlPayment']);
    Route::get('ideal-banks/{orderId}', [CustomerOrderController::class, 'idealBanks']);
    Route::get('idin-banks/{orderId}', [CustomerOrderController::class, 'idinBanks']);
    Route::get('paynlOrderStatus/{orderId}', [CustomerOrderController::class, 'checkPaynlOrderStatus']);
    Route::get('iDINpaynlOrderStatus/{orderId}', [CustomerOrderController::class, 'checkiDINpaynlOrderStatus']);
    Route::get('CheckAgeAuthentication/{orderId}', [CustomerOrderController::class, 'CheckAgeAuthentication']);
    Route::post('order/placeorderCM', [CustomerOrderController::class, 'placeorderCM']);
});
Route::get('Card-details/{orderId?}', [CustomerOrderController::class, 'cardDetails']); #solve!

Route::group(['prefix' => 'customer'], function () {
    // Route::get('/welcome-age-check', [CustomerController::class, 'ageValidation'])->name('welcome'); #!
    // Route::post('/checkAge', [CustomerController::class, 'validateAge']); #!

    Route::get('subcategory/{id}', [CustomerController::class, 'subCategoryList']); #!

    //Cm Payment
    Route::get('orderStatus/{orderId}', [CustomerOrderController::class, 'CheckorderStatus']); #!

    /** Cart */
    // TODO: add this function from routeController to CartController
    Route::get('order_status', [CustomerCartController::class, 'orderStatus']); #!
    Route::get('order_cancelled', [CustomerCartController::class, 'order_cancelled']); #!

    /** Place Order */
    Route::post('order/placeorder', [CustomerOrderController::class, 'placeOrder']); #API TOKEN!
    Route::post('order/placeorderBit', [CustomerOrderController::class, 'placeorderBit']); #Not Found!

    Route::get('/makepayment/{order_id}', [CustomerOrderController::class, 'makeOrderPayment'])->name('payment'); #API TOKEN!

    /** CM Payment */
    Route::get('makePayment', [CustomerOrderController::class, 'makePayment']); #API_TOKEN!
    Route::get('check-idin/{orderId}', [CustomerOrderController::class, 'idinverification']); #API_TOKEN!
    Route::get('checkidinstatus', [CustomerOrderController::class, 'checkidinstatus']); #API_TOKEN!
    Route::get('checkqridinstatus', [CustomerOrderController::class, 'checkqridinstatus']); #API TOKEN!
    Route::get('idinbanktransaction', [CustomerOrderController::class, 'idinbanktransaction']); #API_TOKEN!
    Route::get('idin-thankyou', [CustomerOrderController::class, 'inserttrxid']); #SOLVE!
    // Route::get('Card-details', [CustomerOrderController::class, 'cardDetails']); #SOLVE!
    // Route::get('Card-details/{orderId?}', [CustomerOrderController::class, 'cardDetails']); #solve!
    Route::post('idealPayment', [CustomerOrderController::class, 'idealPayment']); #Function Not Found!

    #PAYMENT_TOKEN!
    Route::get('paynlExchangeUrl/{orderId}', [CustomerOrderController::class, 'checkPaynlExchangeUrl']); #Function Not Found!
    // Route::get('iDINpaynlOrderStatus/{orderId}', [CustomerOrderController::class, 'checkiDINpaynlOrderStatus']); #PAYMENT_TOKEN!
    // Route::get('CheckAgeAuthentication/{orderId}', [CustomerOrderController::class, 'CheckAgeAuthentication']); #PAYMENT_TOKEN!

    Route::get('/free-password', function () {
        return Hash::make("123");
    });
});

Route::any('/order', [SyncController::class, 'syncOrder']); #!
Route::any('/syncProduct', [SyncController::class, 'syncProduct']); #!
Route::any('/getAllergenceFromDeliverect', [SyncController::class, 'getAllergenceFromDeliverect']); #!
Route::any('/syncMenu', [SyncController::class, 'syncMenu']); #!
Route::any('/getProductFromStock', [SyncProductFromStockController::class, 'syncProductFromStock']); #!
Route::any('/getProductDetailFromStock', [SyncProductFromStockController::class, 'getProductDetailFromStock']); #!
Route::any('/fetchArticleNumber', [SyncProductFromStockController::class, 'fetchArticleNumber']); #!
Route::get('contact_us_web/{id}', [AdminCmsPageController::class, 'privacyPolicy']); #!
Route::get('privacy_policy_web/{id}', [AdminCmsPageController::class, 'termsAndCondition']); #!
Route::get('technology_web/{id}', [AdminCmsPageController::class, 'coloPhone']); #!
Route::get('terms_condition_web/{id}', [AdminCmsPageController::class, 'cookieStatement']); #!
Route::get('alcohol_law_web/{id}', [AdminCmsPageController::class, 'alcoholLaw']); #!
Route::any('/OrderAssignment/{id}', [BaseController::class, 'OrderAssignment']); #!
Route::any('/OrderTest/{id}', [BaseController::class, 'OrderTest']); #!


Route::get('email-render', function () {
    if (session()->has('mail')) {
        $class = session('mail')['class'];

        if (class_exists($class)) {
            $mail = new $class(session('mail')['data']);
            return $mail->render();
        }
    }

    abort(404);
});
