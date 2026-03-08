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
