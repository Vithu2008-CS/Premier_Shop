# 🛒 Premier Retail Shop - Modern E-Commerce Platform

A high-performance, mobile-first e-commerce web application built with Laravel. This platform features a premium user shopping experience alongside a powerful, dark-themed administrative dashboard with advanced tools like web-based QR code scanning for inventory management and dynamic bulk discounting.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

## ✨ Key Features

### 🛍️ Customer Frontend
* **Premium UX/UI:** Mobile-first, responsive design with a modern, glass-morphism aesthetic.
* **Live Search Auto-Suggest:** Lightning-fast product search with instant dropdown suggestions, image thumbnails, and category tags.
* **Bulk Offers System:** Dedicated offers page showcasing tiered discounts based on purchase quantity.
* **Seamless Checkout:** Full shopping cart functionality with promotional coupon code integration.
* **Distance-aware Delivery (Upcoming):** Captures user postal codes during registration for smart distance-based shipping calculations.

### 🛡️ Administrative Dashboard
* **Dark Mode Interface:** A sleek, premium dark-themed admin panel with animated metric cards and gradient UI elements.
* **Inventory Management:** Full CRUD operations for products, categories, and stock levels.
* **QR Code Integration:** Automatically generates downloadable QR codes for every product.
* **Live Web Scanner:** Integrated device-camera QR scanner allowing admins to scan physical products and instantly update stock levels right in the browser.
* **Order & Customer Tracking:** Detailed views of order histories, status updates, and customer demographics (including under-16 age verification restrictions).
* **Marketing Tools:** Create and manage flexible discount coupons (percentage or fixed amount, usage limits, and expiration dates).

## 💻 Technology Stack

* **Backend:** PHP 8.2+, Laravel 12.x
* **Frontend:** HTML5, Vanilla JavaScript, Bootstrap 5, Custom SCSS
* **Database:** MySQL / PostgreSQL / SQLite
* **Asset Compilation:** Vite
* **Key Libraries:** `endroid/qr-code` (Backend generation), `html5-qrcode` (Frontend scanning)

## 🚀 Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing.

### Prerequisites
Make sure you have the following installed on your system:
* PHP >= 8.2 (Ensure the `GD` extension is enabled in `php.ini`)
* Composer
* Node.js & npm (v18+)
* MySQL or another preferred SQL database

### Installation Process

1. **Clone the repository**
   ```bash
   git clone https://github.com/Vithu2008-CS/Premier_Shop.git
   Premier_Shop

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
