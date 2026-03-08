<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ShippingSetting;
use App\Models\Promotion;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Shop Admin',
            'email' => 'admin@premiershop.com',
            'password' => bcrypt('admin123'),
            'dob' => '1990-01-01',
            'phone' => '07700000000',
            'address' => 'Premier Shop HQ, London, UK',
            'role' => 'admin',
        ]);

        // Sample customer
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'dob' => '2000-05-15',
            'phone' => '07700000001',
            'address' => '123 High Street, London',
            'role' => 'customer',
        ]);

        // Categories
        $electronics = Category::create(['name' => 'Electronics', 'description' => 'Gadgets, phones, and accessories']);
        $clothing = Category::create(['name' => 'Clothing', 'description' => 'Fashion and apparel']);
        $groceries = Category::create(['name' => 'Groceries', 'description' => 'Fresh food and daily essentials']);
        $homeGarden = Category::create(['name' => 'Home & Garden', 'description' => 'Furniture and garden supplies']);
        $beverages = Category::create(['name' => 'Beverages', 'description' => 'Drinks and refreshments']);

        // Products
        Product::create([
            'name' => 'Wireless Bluetooth Earbuds',
            'description' => 'Premium noise-cancelling wireless earbuds with 24-hour battery life. Crystal clear sound quality with deep bass.',
            'price' => 49.99,
            'stock' => 150,
            'category_id' => $electronics->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
        ]);

        Product::create([
            'name' => 'Smart Watch Pro',
            'description' => 'Feature-packed smartwatch with heart rate monitor, GPS tracking, and 7-day battery life.',
            'price' => 129.99,
            'stock' => 75,
            'category_id' => $electronics->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
        ]);

        Product::create([
            'name' => 'Organic Cotton T-Shirt',
            'description' => 'Comfortable 100% organic cotton t-shirt. Available in multiple colors.',
            'price' => 19.99,
            'stock' => 300,
            'category_id' => $clothing->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
        ]);

        Product::create([
            'name' => 'Premium Denim Jacket',
            'description' => 'Classic fit denim jacket with vintage wash. Perfect for all seasons.',
            'price' => 69.99,
            'stock' => 80,
            'category_id' => $clothing->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
        ]);

        Product::create([
            'name' => 'Fresh Fruit Basket',
            'description' => 'Assorted seasonal fresh fruits. Locally sourced and farm-fresh.',
            'price' => 15.99,
            'stock' => 50,
            'category_id' => $groceries->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
        ]);

        Product::create([
            'name' => 'Organic Extra Virgin Olive Oil',
            'description' => 'Cold-pressed organic olive oil from Mediterranean farms. 1L bottle.',
            'price' => 12.49,
            'stock' => 200,
            'category_id' => $groceries->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
        ]);

        Product::create([
            'name' => 'Premium Red Wine Collection',
            'description' => 'Curated selection of three award-winning red wines from French vineyards.',
            'price' => 45.00,
            'stock' => 40,
            'category_id' => $beverages->id,
            'product_type' => 'normal',
            'is_age_restricted' => true,
        ]);

        Product::create([
            'name' => 'Craft Beer Variety Pack',
            'description' => '12-pack of assorted craft beers from local UK breweries.',
            'price' => 24.99,
            'stock' => 60,
            'category_id' => $beverages->id,
            'product_type' => 'normal',
            'is_age_restricted' => true,
        ]);

        Product::create([
            'name' => 'Indoor Plant Collection',
            'description' => 'Set of 3 easy-care indoor plants in decorative ceramic pots.',
            'price' => 34.99,
            'stock' => 45,
            'category_id' => $homeGarden->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
        ]);

        Product::create([
            'name' => 'Bulk Rice 25kg',
            'description' => 'Premium long-grain basmati rice in 25kg bag. Perfect for wholesale buyers.',
            'price' => 39.99,
            'wholesale_price' => 32.99,
            'stock' => 100,
            'category_id' => $groceries->id,
            'product_type' => 'wholesale',
            'is_age_restricted' => false,
        ]);

        // Shipping Settings
        ShippingSetting::create([
            'free_delivery_threshold' => 50.00,
            'free_delivery_radius_miles' => 10.00,
            'surcharge_per_mile' => 1.50,
            'flat_rate_fee' => 5.99,
        ]);

        // Sample Promotion
        Promotion::create([
            'title' => 'Spring Sale — Up to 30% Off!',
            'description' => 'Enjoy massive discounts on selected items this spring season.',
            'is_active' => true,
            'start_date' => now(),
            'end_date' => now()->addMonths(2),
        ]);
    }
}
