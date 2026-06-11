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
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send')->middleware('throttle:5,1');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'store'])->name('newsletter.subscribe')->middleware('throttle:5,1');

// Product catalogue — public browsing
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/suggest', [ProductController::class, 'suggest'])->name('products.suggest'); // AJAX autocomplete
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
// Rate-limited: order numbers are timestamp-derived (uniqid) and therefore partly
// guessable, so throttle public tracking to slow order-number enumeration.
Route::get('/api/orders/track/{order_number}', [OrderController::class, 'trackPublic'])->name('orders.trackPublic')->middleware('throttle:20,1');

// Privacy Policy & Terms of Service - UK Compliant
Route::view('/privacy-policy', 'privacy')->name('privacy');
Route::view('/terms-of-service', 'terms')->name('terms');

// Stripe webhook — public, signature-verified in the controller, CSRF-exempt
// (see bootstrap/app.php). Confirms payment asynchronously as a backstop.
Route::post('/stripe/webhook', [\App\Http\Controllers\StripeWebhookController::class, 'handle'])->name('stripe.webhook');


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
    Route::post('/checkout/payment-intent', [CheckoutController::class, 'createPaymentIntent'])->name('checkout.paymentIntent')->middleware('throttle:checkout'); // Stripe PaymentIntent (server-priced)
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
// audit.admin records every state-changing (non-GET) request in audit_logs.
Route::middleware(['auth', 'admin', 'audit.admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/omni-search', [DashboardController::class, 'omniSearch'])->name('omniSearch');

    // Products — full CRUD + QR management + stock update + image uploads
    // Resources are expanded to explicit routes so each action carries its RBAC permission
    // (Route::resource cannot attach per-action middleware). Route names are unchanged.
    Route::get('products', [AdminProductController::class, 'index'])->name('products.index')->middleware('permission:products.view');
    Route::get('products/suggest', [AdminProductController::class, 'suggest'])->name('products.suggest')->middleware('permission:products.view');
    Route::post('products/upload-image', [AdminProductController::class, 'uploadImage'])->name('products.uploadImage')->middleware('permission:products.create');
    Route::get('products/create', [AdminProductController::class, 'create'])->name('products.create')->middleware('permission:products.create');
    Route::post('products', [AdminProductController::class, 'store'])->name('products.store')->middleware('permission:products.create');
    Route::get('products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit')->middleware('permission:products.update');
    Route::match(['put', 'patch'], 'products/{product}', [AdminProductController::class, 'update'])->name('products.update')->middleware('permission:products.update');
    Route::delete('products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy')->middleware('permission:products.delete');
    Route::post('products/{product}/regenerate-qr', [AdminProductController::class, 'regenerateQr'])->name('products.regenerateQr')->middleware('permission:products.update');
    Route::get('scanner', [AdminProductController::class, 'scanner'])->name('scanner')->middleware('permission:products.view');          // camera QR scanner page
    Route::post('products/find-by-qr', [AdminProductController::class, 'findByQr'])->name('products.findByQr')->middleware('permission:products.view'); // AJAX: scan result lookup
    Route::post('products/{product}/update-stock', [AdminProductController::class, 'updateStock'])->name('products.updateStock')->middleware('permission:products.update');

    // Categories
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index')->middleware('permission:categories.view');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create')->middleware('permission:categories.create');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store')->middleware('permission:categories.create');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit')->middleware('permission:categories.update');
    Route::match(['put', 'patch'], 'categories/{category}', [CategoryController::class, 'update'])->name('categories.update')->middleware('permission:categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy')->middleware('permission:categories.delete');

    // Sliders & Banners (Promotion model, type='slider'|'banner')
    Route::patch('sliders/{slider}/toggle-active', [\App\Http\Controllers\Admin\SliderController::class, 'toggleActive'])->name('sliders.toggle-active')->middleware('permission:sliders.update');
    Route::get('sliders', [\App\Http\Controllers\Admin\SliderController::class, 'index'])->name('sliders.index')->middleware('permission:sliders.view');
    Route::get('sliders/create', [\App\Http\Controllers\Admin\SliderController::class, 'create'])->name('sliders.create')->middleware('permission:sliders.create');
    Route::post('sliders', [\App\Http\Controllers\Admin\SliderController::class, 'store'])->name('sliders.store')->middleware('permission:sliders.create');
    Route::get('sliders/{slider}/edit', [\App\Http\Controllers\Admin\SliderController::class, 'edit'])->name('sliders.edit')->middleware('permission:sliders.update');
    Route::match(['put', 'patch'], 'sliders/{slider}', [\App\Http\Controllers\Admin\SliderController::class, 'update'])->name('sliders.update')->middleware('permission:sliders.update');
    Route::delete('sliders/{slider}', [\App\Http\Controllers\Admin\SliderController::class, 'destroy'])->name('sliders.destroy')->middleware('permission:sliders.delete');

    // Coupons
    Route::get('coupons', [CouponController::class, 'index'])->name('coupons.index')->middleware('permission:coupons.view');
    Route::get('coupons/create', [CouponController::class, 'create'])->name('coupons.create')->middleware('permission:coupons.create');
    Route::post('coupons', [CouponController::class, 'store'])->name('coupons.store')->middleware('permission:coupons.create');
    Route::get('coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('coupons.edit')->middleware('permission:coupons.update');
    Route::match(['put', 'patch'], 'coupons/{coupon}', [CouponController::class, 'update'])->name('coupons.update')->middleware('permission:coupons.update');
    Route::delete('coupons/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy')->middleware('permission:coupons.delete');

    // Reviews — moderation (approve/reject) + admin reply
    Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index')->middleware('permission:reviews.view');
    Route::get('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'show'])->name('reviews.show')->middleware('permission:reviews.view');
    Route::put('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'update'])->name('reviews.update')->middleware('permission:reviews.update');
    Route::patch('/reviews/{review}/toggle-approval', [\App\Http\Controllers\Admin\ReviewController::class, 'toggleApproval'])->name('reviews.toggleApproval')->middleware('permission:reviews.update');
    Route::post('/reviews/{review}/reply', [\App\Http\Controllers\Admin\ReviewController::class, 'reply'])->name('reviews.reply')->middleware('permission:reviews.update');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy')->middleware('permission:reviews.delete');

    // Return Requests — view, approve/reject/refund, delete
    Route::get('/returns', [\App\Http\Controllers\Admin\ReturnRequestController::class, 'index'])->name('returns.index')->middleware('permission:returns.view');
    Route::get('/returns/{return}', [\App\Http\Controllers\Admin\ReturnRequestController::class, 'show'])->name('returns.show')->middleware('permission:returns.view');
    Route::put('/returns/{return}', [\App\Http\Controllers\Admin\ReturnRequestController::class, 'update'])->name('returns.update')->middleware('permission:returns.update');
    Route::delete('/returns/{return}', [\App\Http\Controllers\Admin\ReturnRequestController::class, 'destroy'])->name('returns.destroy')->middleware('permission:returns.update');

    // Orders — view, status update, driver assignment, print
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index')->middleware('permission:orders.view');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show')->middleware('permission:orders.view');
    Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus')->middleware('permission:orders.update');
    Route::post('orders/{order}/assign-driver', [AdminOrderController::class, 'assignDriver'])->name('orders.assignDriver')->middleware('permission:orders.update');
    Route::get('orders/{order}/print', [AdminOrderController::class, 'print'])->name('orders.print')->middleware('permission:orders.view');
    Route::delete('orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy')->middleware('permission:orders.delete');

    // Customers — view profiles, update roles, delete accounts
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index')->middleware('permission:customers.view');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show')->middleware('permission:customers.view');
    Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update')->middleware('permission:customers.update');
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy')->middleware('permission:customers.delete');
    Route::patch('customers/{customer}/role', [CustomerController::class, 'updateRole'])->name('customers.updateRole')->middleware('permission:customers.update');

    // Reports — analytics dashboard + printable version
    Route::get('reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index')->middleware('permission:reports.view');
    Route::get('reports/print', [App\Http\Controllers\Admin\ReportController::class, 'print'])->name('reports.print')->middleware('permission:reports.view');

    // Settings — shop config: shipping, loyalty rates, shop info
    Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index')->middleware('permission:settings.view');
    Route::post('settings', [App\Http\Controllers\Admin\SettingController::class, 'store'])->name('settings.store')->middleware('permission:settings.update');
    Route::get('contact-settings', [App\Http\Controllers\Admin\SettingController::class, 'contactIndex'])->name('settings.contact')->middleware('permission:settings.view');
    Route::post('contact-settings', [App\Http\Controllers\Admin\SettingController::class, 'contactStore'])->name('settings.contact.store')->middleware('permission:settings.update');

    // Shipping Rates — base, distance, and weight rates
    Route::get('shipping-rates', [App\Http\Controllers\Admin\ShippingRateController::class, 'index'])->name('shipping-rates.index')->middleware('permission:shipping_rates.view');
    Route::put('shipping-rates', [App\Http\Controllers\Admin\ShippingRateController::class, 'update'])->name('shipping-rates.update')->middleware('permission:shipping_rates.update');

    // Admin profile (reuses ProfileController with different view)
    Route::get('profile', [ProfileController::class, 'editAdmin'])->name('profile');

    // Roles & Permissions — RBAC management (admin-only in practice: only the admin
    // role holds roles.* permissions, so manager/accountant are blocked here).
    Route::get('roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index')->middleware('permission:roles.view');
    Route::get('roles/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('roles.create')->middleware('permission:roles.create');
    Route::post('roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store')->middleware('permission:roles.create');
    Route::get('roles/{role}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:roles.update');
    Route::match(['put', 'patch'], 'roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update')->middleware('permission:roles.update');
    Route::delete('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles.delete');

    // Audit Logs — read-only trail of admin actions (written by audit.admin middleware)
    Route::get('audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])
        ->name('audit-logs.index')
        ->middleware('permission:audit_logs.view');

    // Drivers — admin management of driver accounts
    Route::get('drivers/{driver}/location', [AdminDriverController::class, 'getLocation'])->name('drivers.location')->middleware('permission:drivers.view');
    Route::get('drivers', [AdminDriverController::class, 'index'])->name('drivers.index')->middleware('permission:drivers.view');
    Route::get('drivers/create', [AdminDriverController::class, 'create'])->name('drivers.create')->middleware('permission:drivers.create');
    Route::post('drivers', [AdminDriverController::class, 'store'])->name('drivers.store')->middleware('permission:drivers.create');
    Route::get('drivers/{driver}/edit', [AdminDriverController::class, 'edit'])->name('drivers.edit')->middleware('permission:drivers.update');
    Route::match(['put', 'patch'], 'drivers/{driver}', [AdminDriverController::class, 'update'])->name('drivers.update')->middleware('permission:drivers.update');
    Route::delete('drivers/{driver}', [AdminDriverController::class, 'destroy'])->name('drivers.destroy')->middleware('permission:drivers.delete');

    // Mail Centre — full inbox/sent/drafts/trash email client
    // All mail records are ContactMessage rows; folder column = inbox|sent|draft|trash
    Route::group(['prefix' => 'mail', 'as' => 'mail.'], function () {
        // Read-only views require mail.view; state-changing actions require mail.manage.
        Route::get('inbox',       [\App\Http\Controllers\Admin\MailController::class, 'inbox'])->name('inbox')->middleware('permission:mail.view');
        Route::get('sent',        [\App\Http\Controllers\Admin\MailController::class, 'sent'])->name('sent')->middleware('permission:mail.view');
        Route::get('important',   [\App\Http\Controllers\Admin\MailController::class, 'important'])->name('important')->middleware('permission:mail.view');
        Route::get('drafts',      [\App\Http\Controllers\Admin\MailController::class, 'drafts'])->name('drafts')->middleware('permission:mail.view');
        Route::get('trash',       [\App\Http\Controllers\Admin\MailController::class, 'trash'])->name('trash')->middleware('permission:mail.view');
        Route::get('tags/{tag?}', [\App\Http\Controllers\Admin\MailController::class, 'tags'])->name('tags')->middleware('permission:mail.view');

        Route::get('search',          [\App\Http\Controllers\Admin\MailController::class, 'search'])->name('search')->middleware('permission:mail.view');      // GET ?q=
        Route::get('read/{id}',       [\App\Http\Controllers\Admin\MailController::class, 'read'])->name('read')->middleware('permission:mail.view');
        Route::get('compose',         [\App\Http\Controllers\Admin\MailController::class, 'compose'])->name('compose')->middleware('permission:mail.manage');    // ?draft_id= for editing
        Route::post('send',           [\App\Http\Controllers\Admin\MailController::class, 'send'])->name('send')->middleware('permission:mail.manage');
        Route::post('star/{id}',      [\App\Http\Controllers\Admin\MailController::class, 'toggleStar'])->name('star')->middleware('permission:mail.manage');
        Route::post('mark-unread/{id}',[\App\Http\Controllers\Admin\MailController::class, 'markUnread'])->name('markUnread')->middleware('permission:mail.manage');
        Route::post('restore/{id}',   [\App\Http\Controllers\Admin\MailController::class, 'restore'])->name('restore')->middleware('permission:mail.manage');    // move out of trash
        Route::delete('delete/{id}',  [\App\Http\Controllers\Admin\MailController::class, 'destroy'])->name('destroy')->middleware('permission:mail.manage');
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
