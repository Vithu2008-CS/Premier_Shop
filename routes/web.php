<?php

/**
 * Web Routes
 * ==========
 * Route groups and their middleware:
 *
 *  Public          — no middleware (unauthenticated visitors allowed)
 *  auth            — must be logged in (any role)
 *  auth + admin    — must be staff (is_staff=true on role); prefix: /admin
 *  auth + driver   — must have driver role; prefix: /driver
 *
 * Rate limiters (defined in AppServiceProvider):
 *  throttle:login    — 5 req/min per IP (auth actions)
 *  throttle:checkout — 10 req/min per user (coupon, shipping, process)
 */

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DriverController as AdminDriverController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ── PUBLIC ROUTES ────────────────────────────────────────────────────────────
// No authentication required — visible to all visitors.

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/offers', [HomeController::class, 'offers'])->name('offers');
Route::get('/categories', [HomeController::class, 'categories'])->name('categories');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'store'])->name('newsletter.subscribe');

// Product catalogue — public browsing
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/suggest', [ProductController::class, 'suggest'])->name('products.suggest'); // AJAX autocomplete
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/api/orders/track/{order_number}', [OrderController::class, 'trackPublic'])->name('orders.trackPublic');

// Privacy Policy & Terms of Service - UK Compliant
Route::view('/privacy-policy', 'privacy')->name('privacy');
Route::view('/terms-of-service', 'terms')->name('terms');


// ── AUTHENTICATED CUSTOMER ROUTES ────────────────────────────────────────────
// Requires login. Covers cart, checkout, orders, profile, reviews, wishlist,
// addresses, notifications, and returns.
Route::middleware('auth')->group(function () {

    // ── Cart ─────────────────────────────────────────────────────────────────
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/api/cart/items', [CartController::class, 'itemsJson'])->name('cart.itemsJson');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/buy-now', [CartController::class, 'buyNow'])->name('cart.buyNow'); // skip cart, go direct to checkout
    Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');

    // Shipping settings mini-API — exposes non-sensitive delivery tier config
    // to the checkout JS without needing an admin token.
    Route::get('/api/shipping-settings', function () {
        $s = \App\Models\Setting::first();
        if (! $s) {
            return response()->json(['flat_rate_fee' => 5.99, 'free_delivery_threshold' => 50, 'free_delivery_radius_miles' => 0]);
        }

        return response()->json([
            'flat_rate_fee'               => $s->flat_rate_fee,
            'free_delivery_threshold'     => $s->free_delivery_threshold,
            'free_delivery_radius_miles'  => $s->free_delivery_radius_miles,
            'surcharge_per_mile'          => $s->surcharge_per_mile,
        ]);
    });

    // ── Checkout ─────────────────────────────────────────────────────────────
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.applyCoupon')->middleware('throttle:checkout');
    Route::delete('/checkout/coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.removeCoupon');
    Route::post('/checkout/shipping', [CheckoutController::class, 'calculateShipping'])->name('checkout.calculateShipping')->middleware('throttle:checkout'); // AJAX preview
    Route::post('/checkout/calculate-shipping-dynamic', [\App\Http\Controllers\ShippingCalculationController::class, 'calculate'])->name('checkout.calculateShippingDynamic')->middleware('throttle:checkout');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process')->middleware('throttle:checkout'); // place order

    // ── Orders ───────────────────────────────────────────────────────────────
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // ── Profile ──────────────────────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/rewards', [ProfileController::class, 'rewards'])->name('profile.rewards'); // loyalty points history
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Reviews ──────────────────────────────────────────────────────────────
    // Purchase verification is enforced inside ReviewController::store()
    Route::post('/products/{product}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

    // ── Wishlist ─────────────────────────────────────────────────────────────
    Route::get('/wishlists', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlists.index');
    Route::post('/wishlists/{product}/toggle', [\App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlists.toggle'); // add or remove

    // ── Saved Addresses ───────────────────────────────────────────────────────
    // Resource minus create/edit/show — forms are inline; show handled in profile.
    Route::resource('addresses', \App\Http\Controllers\AddressController::class)->except(['create', 'edit', 'show']);
    Route::patch('/addresses/{address}/set-default', [\App\Http\Controllers\AddressController::class, 'setDefault'])->name('addresses.setDefault');

    // ── Notifications ─────────────────────────────────────────────────────────
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/count', [\App\Http\Controllers\NotificationController::class, 'count'])->name('notifications.count');   // polled every 30s
    Route::get('/notifications/latest', [\App\Http\Controllers\NotificationController::class, 'latest'])->name('notifications.latest'); // dropdown HTML partial
    Route::get('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // ── Returns ──────────────────────────────────────────────────────────────
    // Returns are only allowed on delivered orders with no existing return request.
    Route::get('/orders/{order}/returns/create', [\App\Http\Controllers\ReturnRequestController::class, 'create'])->name('returns.create');
    Route::post('/orders/{order}/returns', [\App\Http\Controllers\ReturnRequestController::class, 'store'])->name('returns.store');
    Route::get('/returns/{return}', [\App\Http\Controllers\ReturnRequestController::class, 'show'])->name('returns.show');
});

// ── ADMIN ROUTES ─────────────────────────────────────────────────────────────
// Requires: auth + admin middleware (is_staff=true).
// Prefix: /admin  |  Named: admin.*
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/omni-search', [DashboardController::class, 'omniSearch'])->name('omniSearch');

    // Products — full CRUD + QR management + stock update + image uploads
    Route::post('products/upload-image', [AdminProductController::class, 'uploadImage'])->name('products.uploadImage');
    Route::get('products/suggest', [AdminProductController::class, 'suggest'])->name('products.suggest');
    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::post('products/{product}/regenerate-qr', [AdminProductController::class, 'regenerateQr'])->name('products.regenerateQr');
    Route::get('scanner', [AdminProductController::class, 'scanner'])->name('scanner');          // camera QR scanner page
    Route::post('products/find-by-qr', [AdminProductController::class, 'findByQr'])->name('products.findByQr'); // AJAX: scan result lookup
    Route::post('products/{product}/update-stock', [AdminProductController::class, 'updateStock'])->name('products.updateStock');

    // Categories
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Sliders & Banners (Promotion model, type='slider'|'banner')
    Route::patch('sliders/{slider}/toggle-active', [\App\Http\Controllers\Admin\SliderController::class, 'toggleActive'])->name('sliders.toggle-active');
    Route::resource('sliders', \App\Http\Controllers\Admin\SliderController::class)->except(['show']);

    // Coupons
    Route::resource('coupons', CouponController::class)->except(['show']);

    // Reviews — moderation (approve/reject) + admin reply
    Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'show'])->name('reviews.show');
    Route::put('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'update'])->name('reviews.update');
    Route::patch('/reviews/{review}/toggle-approval', [\App\Http\Controllers\Admin\ReviewController::class, 'toggleApproval'])->name('reviews.toggleApproval');
    Route::post('/reviews/{review}/reply', [\App\Http\Controllers\Admin\ReviewController::class, 'reply'])->name('reviews.reply');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Return Requests — view, approve/reject/refund, delete
    Route::get('/returns', [\App\Http\Controllers\Admin\ReturnRequestController::class, 'index'])->name('returns.index');
    Route::get('/returns/{return}', [\App\Http\Controllers\Admin\ReturnRequestController::class, 'show'])->name('returns.show');
    Route::put('/returns/{return}', [\App\Http\Controllers\Admin\ReturnRequestController::class, 'update'])->name('returns.update');
    Route::delete('/returns/{return}', [\App\Http\Controllers\Admin\ReturnRequestController::class, 'destroy'])->name('returns.destroy');

    // Orders — view, status update, driver assignment, print
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('orders/{order}/assign-driver', [AdminOrderController::class, 'assignDriver'])->name('orders.assignDriver');
    Route::get('orders/{order}/print', [AdminOrderController::class, 'print'])->name('orders.print');
    Route::delete('orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');

    // Customers — view profiles, update roles, delete accounts
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::patch('customers/{customer}/role', [CustomerController::class, 'updateRole'])->name('customers.updateRole');

    // Reports — analytics dashboard + printable version
    Route::get('reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/print', [App\Http\Controllers\Admin\ReportController::class, 'print'])->name('reports.print');

    // Settings — shop config: shipping, loyalty rates, shop info
    Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\Admin\SettingController::class, 'store'])->name('settings.store');
    Route::get('contact-settings', [App\Http\Controllers\Admin\SettingController::class, 'contactIndex'])->name('settings.contact');
    Route::post('contact-settings', [App\Http\Controllers\Admin\SettingController::class, 'contactStore'])->name('settings.contact.store');

    // Shipping Rates — base, distance, and weight rates
    Route::get('shipping-rates', [App\Http\Controllers\Admin\ShippingRateController::class, 'index'])->name('shipping-rates.index');
    Route::put('shipping-rates', [App\Http\Controllers\Admin\ShippingRateController::class, 'update'])->name('shipping-rates.update');

    // Admin profile (reuses ProfileController with different view)
    Route::get('profile', [ProfileController::class, 'editAdmin'])->name('profile');

    // Roles & Permissions — RBAC management
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->except(['show']);

    // Drivers — admin management of driver accounts
    Route::get('drivers/{driver}/location', [AdminDriverController::class, 'getLocation'])->name('drivers.location');
    Route::resource('drivers', AdminDriverController::class)->except(['show']);

    // Mail Centre — full inbox/sent/drafts/trash email client
    // All mail records are ContactMessage rows; folder column = inbox|sent|draft|trash
    Route::group(['prefix' => 'mail', 'as' => 'mail.'], function () {
        Route::get('inbox',       [\App\Http\Controllers\Admin\MailController::class, 'inbox'])->name('inbox');
        Route::get('sent',        [\App\Http\Controllers\Admin\MailController::class, 'sent'])->name('sent');
        Route::get('important',   [\App\Http\Controllers\Admin\MailController::class, 'important'])->name('important');
        Route::get('drafts',      [\App\Http\Controllers\Admin\MailController::class, 'drafts'])->name('drafts');
        Route::get('trash',       [\App\Http\Controllers\Admin\MailController::class, 'trash'])->name('trash');
        Route::get('tags/{tag?}', [\App\Http\Controllers\Admin\MailController::class, 'tags'])->name('tags');

        Route::get('search',          [\App\Http\Controllers\Admin\MailController::class, 'search'])->name('search');      // GET ?q=
        Route::get('read/{id}',       [\App\Http\Controllers\Admin\MailController::class, 'read'])->name('read');
        Route::get('compose',         [\App\Http\Controllers\Admin\MailController::class, 'compose'])->name('compose');    // ?draft_id= for editing
        Route::post('send',           [\App\Http\Controllers\Admin\MailController::class, 'send'])->name('send');
        Route::post('star/{id}',      [\App\Http\Controllers\Admin\MailController::class, 'toggleStar'])->name('star');
        Route::post('mark-unread/{id}',[\App\Http\Controllers\Admin\MailController::class, 'markUnread'])->name('markUnread');
        Route::post('restore/{id}',   [\App\Http\Controllers\Admin\MailController::class, 'restore'])->name('restore');    // move out of trash
        Route::delete('delete/{id}',  [\App\Http\Controllers\Admin\MailController::class, 'destroy'])->name('destroy');
    });
});

// ── DRIVER ROUTES ─────────────────────────────────────────────────────────────
// Requires: auth + driver middleware (role->name === 'driver').
// Prefix: /driver  |  Named: driver.*
Route::middleware(['auth', 'driver'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/', [DriverController::class, 'dashboard'])->name('dashboard');
    Route::post('/toggle-duty', [DriverController::class, 'toggleDuty'])->name('toggleDuty');           // on/off duty toggle
    Route::post('/location', [DriverController::class, 'updateLocation'])->name('location.update')->middleware('throttle:20,1');
    Route::get('/orders/{order}', [DriverController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{order}/complete', [DriverController::class, 'completeDelivery'])->name('orders.complete'); // upload proof photo
});

require __DIR__.'/auth.php';
