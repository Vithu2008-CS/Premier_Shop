<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DriverController as AdminDriverController;
use App\Http\Controllers\DriverController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/offers', [HomeController::class, 'offers'])->name('offers');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/suggest', [ProductController::class, 'suggest'])->name('products.suggest');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Authenticated customer routes
Route::middleware('auth')->group(function () {
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buyNow');
    Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');

    // Shipping Settings API
    Route::get('/api/shipping-settings', function() {
        return \App\Models\ShippingSetting::first();
    });

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.applyCoupon');
    Route::delete('/checkout/coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.removeCoupon');
    Route::post('/checkout/shipping', [CheckoutController::class, 'calculateShipping'])->name('checkout.calculateShipping');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reviews
    Route::post('/products/{product}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

    // Wishlist
    Route::get('/wishlists', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlists.index');
    Route::post('/wishlists/{product}/toggle', [\App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlists.toggle');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', AdminProductController::class);
    Route::post('products/{product}/regenerate-qr', [AdminProductController::class, 'regenerateQr'])->name('products.regenerateQr');
    Route::get('scanner', [AdminProductController::class, 'scanner'])->name('scanner');
    Route::post('products/find-by-qr', [AdminProductController::class, 'findByQr'])->name('products.findByQr');
    Route::post('products/{product}/update-stock', [AdminProductController::class, 'updateStock'])->name('products.updateStock');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Sliders
    Route::resource('sliders', \App\Http\Controllers\Admin\SliderController::class);

    // Coupons
    Route::resource('coupons', CouponController::class);

    // Orders
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('orders/{order}/assign-driver', [AdminOrderController::class, 'assignDriver'])->name('orders.assignDriver');

    // Customers
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::patch('customers/{customer}/role', [CustomerController::class, 'updateRole'])->name('customers.updateRole');

    // Reports
    Route::get('reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/print', [App\Http\Controllers\Admin\ReportController::class, 'print'])->name('reports.print');

    // Settings
    Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\Admin\SettingController::class, 'store'])->name('settings.store');

    // Roles & Permissions
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);

    // Order Print
    Route::get('orders/{order}/print', [AdminOrderController::class, 'print'])->name('orders.print');

    // Drivers Monitoring & Management
    Route::resource('drivers', AdminDriverController::class);
});

// Driver Routes
Route::middleware(['auth', 'driver'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/', [DriverController::class, 'dashboard'])->name('dashboard');
    Route::post('/toggle-duty', [DriverController::class, 'toggleDuty'])->name('toggleDuty');
    Route::get('/orders/{order}', [DriverController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{order}/complete', [DriverController::class, 'completeDelivery'])->name('orders.complete');
});

require __DIR__ . '/auth.php';
