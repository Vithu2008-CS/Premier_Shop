# <p align="center">🛒 Premier Retail Shop</p>
<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white" />
  <img src="https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white" />
</p>

---

**Premier Retail Shop** is a high-performance, mobile-first e-commerce platform designed with a premium 3D aesthetic. It features a stunning user interface powered by glassmorphism and advanced administrative tools for seamless inventory management.

## 🌟 Premium UX/UI Experience

Our platform isn't just a shop; it's a visual experience.
- **✨ 3D Floating Navigation**: A futuristic, elevated menu bar with glassmorphism and interactive 3D hover transitions.
- **🌫️ Glassmorphism Aesthetic**: Deep blurs and semi-transparent layers across the entire frontend.
- **📱 Mobile-First Design**: Optimized for a flawless experience on every device.
- **⚡ Interactive Dashboard**: An admin panel with clickable 3D metric cards and real-time insights.

---

## 📸 Visual Showcase

<p align="center">
  <img src="C:/Users/vithu/.gemini/antigravity/brain/d7fcd99c-31ab-420e-83b2-71c01cd4ae3e/home_page_full_1773563419853.png" width="800" alt="Premier Shop Homepage">
</p>

---

## 🛠️ Key Features

### 🛍️ For Customers
* **Live Search**: Lightning-fast suggestions with thumbnails and real-time results.
* **Bulk Offers**: Dynamic tiered discounts based on purchase volume.
* **Smart Wishlist**: One-click toggling for favorite items.
* **Integrated Checkout**: Secure flow with dynamic coupon validation.

### 🛡️ For Administrators
* **Live QR Scanner**: Browser-based camera integration to scan products and update stock instantly.
* **Marketing Suite**: Powerful tools for bulk discounts and scheduled promotions.
* **Responsive Management**: Full control over products, categories, and age-restricted items.
* **Driver Logistics**: Dedicated dashboard for delivery management and proof of delivery.

---

## 🚀 Tech Stack

- **Backend**: Laravel 12.x & PHP 8.2+
- **Frontend**: Custom SCSS, Vanilla JS, Bootstrap 5
- **Assets**: Vite for rapid compilation
- **Database**: Optimized MySQL/PostgreSQL schema
- **Tools**: endroid/qr-code, html5-qrcode

---

## ⚙️ Quick Installation

1. **Clone & Install**
   ```bash
   git clone https://github.com/Vithu2008-CS/Premier_Shop.git
   cd Premier_Shop
   composer install
   npm install
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan storage:link
   ```

3. **Database & Assets**
   ```bash
   php artisan migrate --seed
   npm run build
   php artisan serve
   ```

---

## 🔑 Demo Credentials

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `vithu@example.com` | `12345678` |
| **Driver** | `driver@example.com` | `12345678` |

---

