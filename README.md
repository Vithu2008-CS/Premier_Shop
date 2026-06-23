<div align="center">

# Premier Shop

### A full-stack retail e-commerce platform built for the modern web

<p>
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/Vite-5.x-646CFF?style=for-the-badge&logo=vite&logoColor=white" />
  <img src="https://img.shields.io/badge/MariaDB-10.x-003545?style=for-the-badge&logo=mariadb&logoColor=white" />
  <img src="https://img.shields.io/badge/Bootstrap-5.3-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white" />
</p>

<p>
  <img src="https://img.shields.io/badge/Status-Production%20Ready-27ae60?style=flat-square" />
  <img src="https://img.shields.io/badge/License-MIT-3498db?style=flat-square" />
  <img src="https://img.shields.io/badge/Tests-Passing-27ae60?style=flat-square" />
  <img src="https://img.shields.io/badge/Code%20Lines-31%2C800%2B-e67e22?style=flat-square" />
</p>

</div>

---

## Overview

**Premier Shop** is a production-grade, full-stack retail e-commerce platform built on **Laravel 12** and **PHP 8.3**. It delivers a premium glassmorphism storefront paired with a feature-rich Noble UI admin panel — covering the complete retail workflow from product discovery through checkout, fulfilment, delivery, and post-purchase returns.

The system supports **multiple staff roles** (Admin, Manager, Accountant, Driver), **OTP-based registration**, **loyalty rewards**, **distance-aware shipping** via Google Maps, **QR-code inventory scanning**, and a built-in **folder-based mail centre** — all within a single, well-structured Laravel application.

---

## Feature Highlights

### Storefront (Customer-Facing)

| Feature | Details |
|---|---|
| **Product Catalogue** | Search, category filter, price range, rating filter, sort; age-restricted items hidden from under-16 accounts |
| **Product Detail** | Image gallery, bulk-offer pricing tiers, stock indicator, real-time recently-viewed tracking |
| **Shopping Cart** | AJAX add/remove/update, line totals auto-calculate with offer pricing |
| **Checkout** | Coupon validation, loyalty point redemption, distance-based shipping calculation, atomic DB transaction |
| **Order Tracking** | Full status timeline (Pending → Processing → Shipped → Delivered), QR code per order |
| **Wishlist** | One-click toggle, persistent across sessions |
| **Returns** | Self-service return requests with item-level quantity selection and photo upload |
| **Loyalty Rewards** | Points earned on purchase, redeemed at checkout, audited transaction ledger |
| **Profile & Addresses** | Address book with default selection, order history, reward balance |
| **Reviews** | Star ratings, photo attachments, admin reply support |

### Admin Panel (Noble UI)

| Feature | Details |
|---|---|
| **Dashboard** | Live KPIs — revenue, orders by status, low-stock alerts, recent activity |
| **Product Management** | Create/edit with multi-image upload, barcode/SKU, bulk-offer pricing, QR scanner for stock updates |
| **Order Management** | Status updates with date tracking, driver assignment, delivery proof viewer, PDF receipt print |
| **Customer Management** | Profile view, order history, reward transaction log |
| **Driver Monitoring** | Active driver list with assigned order counts, on-duty toggle |
| **Coupon Engine** | Percentage and fixed-amount codes, usage limits, minimum order, expiry |
| **Promotions** | Slider and banner management with date-range scheduling and priority ordering |
| **Returns** | Approve/reject workflow with automatic stock restoration |
| **Reviews Moderation** | Approve/hide, admin reply, bulk actions |
| **Mail Centre** | Inbox / Sent / Drafts / Starred / Trash; SimpleMDE markdown composer; Select2 recipient picker |
| **Sales Reports** | Date-range revenue charts, top products, top categories; printable standalone HTML report |
| **Roles & Permissions** | Granular RBAC — create custom roles with permission groups; admin bypasses all checks |
| **System Settings** | Shop info, shipping config, loyalty rates, Google Maps API key, maintenance mode |

### Driver App

| Feature | Details |
|---|---|
| **Delivery Dashboard** | Active and completed deliveries, on-duty toggle |
| **Order Detail** | Customer info, shipping address, items list, QR verification |
| **Complete Delivery** | Camera-captured proof-of-delivery photo upload, automatic status update + customer email |

---

## Architecture

```
Premier Shop
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # 14 admin controllers
│   │   │   ├── Auth/           # OTP registration, login, password reset
│   │   │   └── ...             # 8 frontend controllers
│   │   ├── Middleware/         # AdminMiddleware, DriverMiddleware, PermissionMiddleware,
│   │   │                       # SecurityHeadersMiddleware (camera=(self) for QR scanner)
│   │   └── Requests/           # Form request validation classes
│   ├── Mail/                   # 5 Mailable classes (OTP, welcome, receipt, status, custom)
│   ├── Models/                 # 20 Eloquent models
│   ├── Providers/              # AppServiceProvider — pagination, composers, password policy,
│   │                           # rate limiters (api/login/uploads/checkout)
│   └── Services/
│       └── ShippingService.php # Google Maps Distance Matrix API integration
├── database/
│   ├── migrations/             # 25 versioned migrations
│   ├── seeders/                # DatabaseSeeder + RolePermissionSeeder
│   └── factories/              # Category, Product, User factories
├── resources/
│   ├── views/                  # 70+ Blade templates
│   │   ├── layouts/            # app, admin_noble, auth_modern, driver
│   │   ├── admin/              # Full admin panel views
│   │   ├── emails/             # Transactional email templates (table-based HTML)
│   │   └── ...                 # Storefront, auth, driver views
│   ├── js/app.js               # Wishlist AJAX, cart badge sync, ajax-form, toast, QR scanner
│   └── sass/app.scss           # Custom design system (glassmorphism, 3D cards, CSS variables)
├── routes/
│   ├── web.php                 # Public, customer-auth, admin, driver route groups
│   └── auth.php                # Breeze auth routes with throttle:login
└── tests/
    └── Feature/                # 9 feature test classes (auth flows, profile, shipping)
```

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Framework** | Laravel 12.x |
| **Language** | PHP 8.3 |
| **Database** | MariaDB 10.x (MySQL compatible) |
| **Frontend Build** | Vite 5.x |
| **CSS** | Custom SCSS + Bootstrap 5.3 |
| **Admin UI** | Noble UI (Bootstrap-based) |
| **JavaScript** | Vanilla JS + Bootstrap JS + html5-qrcode |
| **QR Codes** | `endroid/qr-code` (generation) + `html5-qrcode` (browser scanner) |
| **Email** | Laravel Mail + Blade HTML email templates |
| **Maps / Shipping** | Google Maps Distance Matrix API |
| **Authentication** | Laravel Breeze (extended with OTP email verification) |
| **Testing** | PHPUnit + Laravel HTTP tests |

---

## Security

- **Password policy**: minimum 12 characters, checked against Have I Been Pwned (uncompromised) via `Password::defaults()`
- **Rate limiting**: API (60/min), login (5 attempts), file uploads (20/min), checkout (10/min)
- **Security headers**: `Permissions-Policy: camera=(self)` — allows QR scanner camera access without granting it globally
- **CSRF**: enforced on all state-changing requests
- **Age restriction**: products flagged `is_age_restricted` hidden from customers with `dob` < 16 years
- **Ownership checks**: all address, order, and return actions verify `user_id === auth()->id()` before proceeding
- **RBAC**: `PermissionMiddleware` + `AdminMiddleware` + `DriverMiddleware` guard every admin and driver route

---

## Installation

### Requirements

- PHP 8.3+
- Composer 2.x
- Node.js 18+ & npm
- MariaDB 10.x or MySQL 8.x

### Steps

**1. Clone and install dependencies**
```bash
git clone https://github.com/Vithu2008-CS/Premier_Shop.git
cd Premier_Shop
composer install
npm install
```

**2. Configure environment**
```bash
cp .env.example .env
php artisan key:generate
php artisan storage:link
```

Edit `.env` with your database credentials, mail settings, and Google Maps API key:
```env
DB_DATABASE=premier_shop
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525

GOOGLE_MAPS_API_KEY=your_browser_key_here
GOOGLE_MAPS_SERVER_API_KEY=your_server_key_here
```

> **Google Maps keys:** Use two keys. `GOOGLE_MAPS_API_KEY` is exposed in the
> browser (driver tracking, contact/settings maps) — restrict it by **HTTP
> referrer** to the Maps JavaScript + Geocoding APIs. `GOOGLE_MAPS_SERVER_API_KEY`
> is used only by the server-side shipping Distance Matrix call — restrict it by
> **IP address** to the Distance Matrix API. If the server key is omitted, the
> browser key is used as a fallback.

**3. Database and assets**
```bash
php artisan migrate --seed
npm run build
php artisan serve
```

Visit `http://localhost:8000`

> **Development hot-reload**: run `npm run dev` in a separate terminal instead of `npm run build`.

---

## Key Design Decisions

**Single-table cart + wishlist** — `user_items` uses a `type` column (`cart` / `wishlist`) to avoid two near-identical tables. The `getLineTotalAttribute` accessor applies bulk-offer pricing at the model layer.

**Folder-based mail centre** — `contact_messages` uses a `folder` column (`inbox` / `sent` / `draft` / `trash`) rather than separate tables, mirroring how Gmail stores mail. Admin compose writes to `sent`; contact form writes to `inbox`.

**Checkout as a DB transaction** — the entire checkout (stock decrement, order creation, coupon usage increment, loyalty ledger entries) runs inside a single `DB::transaction()`. Any failure rolls back everything.

**Signed-amount reward ledger** — `reward_point_transactions.amount` is signed (`+` for earned/refunded, `-` for redeemed). Cancellation clawback creates a new negative `refunded` row rather than deleting the original — preserving audit history.

**Google Maps null contract** — `ShippingService::calculate()` returns `null` on any API failure. The caller (CheckoutController) falls back to the configured flat rate, so shipping never blocks a checkout.

---

## Running Tests

```bash
php artisan test
```

```
Tests:    9 passed (41 assertions)
Duration: ~2s
```

Test coverage includes: authentication flows (login/logout/register/OTP), email verification, password reset and update, profile management, shipping calculation logic.

---

## Project Stats

| Metric | Value |
|---|---|
| Source files | 220 |
| Lines of code | 31,800+ |
| Blade templates | 70+ |
| Database migrations | 25 |
| Eloquent models | 20 |
| Controllers | 22 |
| Test assertions | 41 |
| Modules / feature areas | 27 |

---

## License

MIT — free to use, modify, and distribute.

---

<div align="center">

Built with ❤️ using **Laravel 12** · **PHP 8.3** · **Noble UI** · **Vite**

</div>
