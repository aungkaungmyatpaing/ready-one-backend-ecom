<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\AuthContoller;
use App\Http\Controllers\Backend\OrderController;
use App\Http\Controllers\Backend\BannerController;
use App\Http\Controllers\Backend\RegionController;
use App\Http\Controllers\Backend\PaymentController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Other\ApplicationController;
use App\Http\Controllers\Backend\DeliveryFeeController;
use App\Http\Controllers\OrderSuccessMessageController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\Backend\SubCategoryController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('home');

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacyPolicy');

//Auth
Route::get('/bg-admin-login', [AuthContoller::class, 'login'])->name('login');
Route::post('/bg-admin-login', [AuthContoller::class, 'postLogin'])->name('postLogin');

Route::get('/logout', [AuthContoller::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //noti
    Route::get('/noti/{id}', [DashboardController::class, 'deleteNoti'])->name('noti.delete');

    //profile
    Route::get('/edit-profile', [AuthContoller::class, 'editProfile'])->name('profile.edit');
    Route::post('/edit-profile', [AuthContoller::class, 'updateProfile'])->name('profile.update');

    //auth
    Route::get('/edit-password', [AuthContoller::class, 'editPassword'])->name('editPassword');
    Route::post('/edit-password', [AuthContoller::class, 'updatePassword'])->name('updatePassword');

    //Products
    Route::get('/products', [ProductController::class, 'listing'])->name('product');
    Route::get('/products/datatable/ssd', [ProductController::class, 'serverSide']);

    Route::get('/products/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('/products', [ProductController::class, 'store'])->name('product.store');
    Route::get('/products/{product}', [ProductController::class, 'detail'])->name('product.detail');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::put('/products/{product}/update', [ProductController::class, 'update'])->name('product.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('product.destroy');

    Route::get('product-images/{product}', [ProductController::class, 'images']); // get images from edit

    //Main Category
    Route::get('/categories', [CategoryController::class, 'index'])->name('category');
    Route::get('/categories/datatable/ssd', [CategoryController::class, 'serverSide']);
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/categories/{category}/update', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
    //sub Category
    // Categories
    Route::get('/sub/categories', [SubCategoryController::class, 'index'])->name('sub.category');
    Route::get('/sub/categories/datatable/ssd', [SubCategoryController::class, 'serverSide']);
    Route::get('/sub/categories/create', [SubCategoryController::class, 'create'])->name('sub.category.create');
    Route::post('/sub/categories', [SubCategoryController::class, 'store'])->name('sub.category.store');
    Route::get('/sub/categories/{category}/edit', [SubCategoryController::class, 'edit'])->name('sub.category.edit');
    Route::put('/sub/categories/{category}/update', [SubCategoryController::class, 'update'])->name('sub.category.update');
    Route::delete('/sub/categories/{category}', [SubCategoryController::class, 'destroy'])->name('sub.category.destroy');
    Route::get('/sub/categories/server/{category}', [SubCategoryController::class, 'sub_category_by_category']);

    //banners
    Route::get('/banners', [BannerController::class, 'index'])->name('banner');
    Route::get('/banners/datatable/ssd', [BannerController::class, 'serverSide']);

    Route::get('/banners/create', [BannerController::class, 'create'])->name('banner.create');
    Route::post('/banners/create', [BannerController::class, 'store'])->name('banner.store');
    Route::get('/banners/edit/{banner}', [BannerController::class, 'edit'])->name('banner.edit');
    Route::post('/banners/edit/{banner}', [BannerController::class, 'update'])->name('banner.update');
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banner.destroy');

    //payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('payment');
    Route::get('/payments/datatable/ssd', [PaymentController::class, 'serverSide']);

    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('/payments/create', [PaymentController::class, 'store'])->name('payment.store');
    Route::get('/payments/edit/{payment}', [PaymentController::class, 'edit'])->name('payment.edit');
    Route::post('/payments/edit/{payment}', [PaymentController::class, 'update'])->name('payment.update');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payment.destroy');

    //customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customer');
    Route::get('/customers/detail/{customer}/{notiId?}', [CustomerController::class, 'detail'])->name('customer.detail');
    Route::post('/customers/accept/{customer}',[CustomerController::class,'accept'])->name('customer.accept');

    Route::get('/customers/edit/{customer}', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::put('/customers/edit/{customer}', [CustomerController::class, 'update'])->name('customer.update');
    Route::put('/customers/update-password/{customer}', [CustomerController::class, 'updatePassword'])->name('customer.updatePassword');
    Route::post('/customers/ban/{customer}', [CustomerController::class, 'banCustomer'])->name('customer.ban');

    Route::get('/customers/datatable/ssd', [CustomerController::class, 'serverSide']);

    //regions (cash on delivery)
    Route::get('/regions', [RegionController::class, 'index'])->name('region');
    Route::get('/regions/datatable/ssd', [RegionController::class, 'serverSide']);

    Route::get('/regions/create', [RegionController::class, 'create'])->name('region.create');
    Route::post('/regions/create', [RegionController::class, 'store'])->name('region.store');
    Route::get('/regions/edit/{region}', [RegionController::class, 'edit'])->name('region.edit');
    Route::post('/regions/edit/{region}', [RegionController::class, 'update'])->name('region.update');
    Route::delete('/regions/{region}', [RegionController::class, 'destroy'])->name('region.destroy');

    //delivery fee
    Route::get('/delivery-fees', [DeliveryFeeController::class, 'index'])->name('deliveryfee');
    Route::get('/delivery-fees/datatable/ssd', [DeliveryFeeController::class, 'serverSide']);
    Route::get('/delivery-fees/create', [DeliveryFeeController::class, 'create'])->name('deliveryfee.create');
    Route::post('/delivery-fees/create', [DeliveryFeeController::class, 'store'])->name('deliveryfee.store');
    Route::get('/delivery-fees/edit/{delivery_fee}', [DeliveryFeeController::class, 'edit'])->name('deliveryfee.edit');
    Route::post('/delivery-fees/edit/{delivery_fee}', [DeliveryFeeController::class, 'update'])->name('deliveryfee.update');
    Route::delete('/delivery-fees/{delivery_fee}', [DeliveryFeeController::class, 'destroy'])->name('deliveryfee.destroy');

    //orders
    Route::get('/orders', [OrderController::class, 'index'])->name('order');
    Route::get('/orders/status/{status}', [OrderController::class, 'orderByStatus'])->name('orderByStatus');

    Route::post('/orders/{order}', [OrderController::class, 'updateStatus'])->name('order.updateStatus');
    Route::get('/orders/cancel/{order}', [OrderController::class, 'cancelOrder'])->name('order.cancel');
    Route::post('/orders/cancel/{order}', [OrderController::class, 'saveCancelOrder'])->name('order.saveCancel');

    Route::get('/orders/refund/all', [OrderController::class, 'refundOrderList'])->name('order.refund.list');
    Route::get('/orders/refund/{order}', [OrderController::class, 'refundOrder'])->name('order.refund');
    Route::post('/orders/refund/{order}', [OrderController::class, 'saveRefundOrder'])->name('order.saveRefund');

    Route::get('/orders/deliver/{order}', [OrderController::class, 'deliverOrder'])->name('order.deliver');
    Route::post('/orders/deliver/{order}', [OrderController::class, 'saveDeliverOrder'])->name('order.saveDeliver');

    Route::get('/orders/{order}/{notiId?}', [OrderController::class, 'detail'])->name('order.detail');

    Route::get('/all-orders/datatable/ssd', [OrderController::class, 'getAllOrder']);
    Route::get('/refund-orders/datatable/ssd', [OrderController::class, 'getRefundList']);
    Route::get('/orders/{status}/datatable/ssd', [OrderController::class, 'getOrderByStatus']);

    //order success message
    Route::get('/order-success-messages', [OrderSuccessMessageController::class, 'index'])->name('orderSuccessMessage');
    Route::get('/order-success-messages/datatable/ssd', [OrderSuccessMessageController::class, 'serverSide']);

    Route::get('/order-success-messages/create', [OrderSuccessMessageController::class, 'create'])->name('orderSuccessMessage.create');
    Route::post('/order-success-messages/create', [OrderSuccessMessageController::class, 'store'])->name('orderSuccessMessage.store');
    Route::get('/order-success-messages/edit/{order_success_message}', [OrderSuccessMessageController::class, 'edit'])->name('orderSuccessMessage.edit');
    Route::post('/order-success-messages/edit/{order_success_message}', [OrderSuccessMessageController::class, 'update'])->name('orderSuccessMessage.update');
    Route::delete('/order-success-messages/{order_success_message}', [OrderSuccessMessageController::class, 'destroy'])->name('orderSuccessMessage.destroy');
});

Route::get('image/{filename}', [ApplicationController::class, 'image'])->where('filename', '.*');
