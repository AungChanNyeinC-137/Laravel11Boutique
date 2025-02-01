<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishListController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Surfsidemedia\Shoppingcart\Facades\Cart;

Auth::routes();

//Home
Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop',[ShopController::class,'index'])->name('shop.index');
Route::get('/shop/{product_slug}',[ShopController::class,'productDetails'])->name('shop.product.details');
Route::get('/contact-us',[HomeController::class,'contact'])->name('home.contact');
//Cart
Route::get('/cart',[CartController::class,'index'])->name('cart.index');
Route::post('/cart/add',[CartController::class,'addToCart'])->name('cart.add');
Route::put('/cart/increase-quantity/{rowID}',[CartController::class,'increaseCartQuantity'])->name('cart.qty.increase');
Route::put('/cart/decrease-quantity/{rowID}',[CartController::class,'decreaseCartQuantity'])->name('cart.qty.decrease');
Route::delete('/cart/item/remove/{row_id}',[CartController::class,'removeFromCart'])->name('cart.item.remove');
Route::delete('/cart/item/clear',[CartController::class,'emptyCart'])->name('cart.item.clear');
Route::post('/cart/order.place',[CartController::class,'placeOrder'])->name('cart.order.place');
Route::get('/cart/order_confirmation',[CartController::class,'confirmOrder'])->name('cart.order.confirm');

//Wishlist
Route::get('/wishlist',[WishListController::class,'index'])->name('wishlist.index');
Route::post('/wishlist/add',[WishListController::class,'addToWishList'])->name('wishlist.add');
Route::delete('/wishlist/item/remove/{row_id}',[WishListController::class,'removeFromWishList'])->name('wishlist.item.remove');
Route::delete('/wishlist/clear',[WishListController::class,'emptyWishLList'])->name('wishlist.item.clear');
Route::post('/wishlist/move-to-cart/{row_id}',[WishListController::class,'moveFromWishlistToCart'])->name('wishlist.move.to.cart');

Route::get('/checkout',[CartController::class,'checkOut'])->name('cart.checkout');

//User
Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
    Route::get('/account-orders',[UserController::class,'orders'])->name('user.orders');
    Route::get('/account-order-details/{order_id}',[UserController::class,'orderDetails'])->name('user.order.details');
});

//Admin
Route::middleware(['auth',AuthAdmin::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    //Brands
    Route::get('/admin/brands', [AdminController::class, 'brands'])->name('admin.brands');
    Route::get('/admin/brand/add', [AdminController::class, 'brandAdd'])->name('admin.brand.add');
    Route::post('/admin/brand/store',[AdminController::class,'brandStore'])->name('admin.brand.store');
    Route::get('/admin/brand/{id}/edit', [AdminController::class, 'brandEdit'])->name('admin.brand.edit');
    Route::put('/admin/brand/update',[AdminController::class,'brandUpdate'])->name('admin.brand.update');
    Route::delete('/admin/brands/{id}/delete',[AdminController::class,'brandDelete'])->name('admin.brand.delete');

    //Categories
    Route::get('/admin/categories',[AdminController::class,'categories'])->name('admin.categories');
    Route::get('/admin/category/add',[AdminController::class,'categoryAdd'])->name('admin.category.add');
    Route::post('/admin/category/store',[AdminController::class,'categoryStore'])->name('admin.category.store');
    Route::get('/admin/category/{id}/edit',[AdminController::class,'categoryEdit'])->name(('admin.category.edit'));
    Route::put('/admin/category/update',[AdminController::class,'categoryUpdate'])->name(('admin.category.update'));
    Route::delete('/admin/categories/{id}/delete',[AdminController::class,'categoryDelete'])->name('admin.category.delete');

    //Products
    Route::get('/admin/products',[AdminController::class,'products'])->name('admin.products');
    Route::get('/admin/product/add',[AdminController::class,'productAdd'])->name('admin.product.add');
    Route::post('/admin/product/store',[AdminController::class,'productStore'])->name('admin.product.store');
    Route::get('/admin/product/{id}/edit',[AdminController::class,'productEdit'])->name('admin.product.edit');
    Route::put('/admin/product/update',[AdminController::class,'prodcutUpdate'])->name('admin.product.update');
    Route::delete('/admin/products/{id}/delete',[AdminController::class,'productDelete'])->name('admin.product.delete');

    //Coupons
    Route::get('/admin/coupons',[AdminController::class,'coupons'])->name('admin.coupons');
    Route::get('/admin/coupon/add',[AdminController::class,'addCoupon'])->name('admin.coupon.add');
    Route::post('/admin/coupon/store',[AdminController::class,'couponStore'])->name('admin.coupon.store');
    Route::get('/admin/coupon/{id}/edit',[AdminController::class,'couponEdit'])->name('admin.coupon.edit');
    Route::put('/admin/coupon/{id}/update',[AdminController::class,'couponUpdate'])->name('admin.coupon.update');
    Route::delete('/admin/coupons/remove',[AdminController::class,'couponRemove'])->name('admin.coupon.remove');
    Route::post('/admin/coupon/apply',[AdminController::class,'applyCouponCode'])->name('admin.coupon.apply');
    Route::delete('/admin/coupons/{id}/delete',[AdminController::class,'couponDelete'])->name('admin.coupon.delete');

    //Orders
    Route::get('/admin/orders',[AdminController::class,'orders'])->name('admin.orders');
    Route::get('/admin/order/{order_id}/details',[AdminController::class,'orderDetails'])->name('admin.order.details');
    Route::put('/admin/order/update-status',[AdminController::class,'orderUpdateStatus'])->name('admin.order.status.update');

    //Slides
    Route::get('/admin/slides',[AdminController::class,'slides'])->name('admin.slides');
    Route::get('/admin/slide/add',[AdminController::class,'slideAdd'])->name('admin.slide.add');
    Route::post('admin/slide/store',[AdminController::class,'slideStore'])->name('admin.slide.store');
    Route::get('/admin/slide/{id}/edit', [AdminController::class, 'slideEdit'])->name('admin.slide.edit');
    Route::put('/admin/slide/{id}/update',[AdminController::class,'slideUpdate'])->name('admin.slide.update');
    Route::delete('/admin/slide/{id}/delete',[AdminController::class,'slideDelete'])->name('admin.slide.delete');
});