<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Promotion;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // First run RolePermissionSeeder to ensure roles exist
        $this->call(RolePermissionSeeder::class);

        // Get Role IDs
        $adminRole = Role::where('name', 'admin')->first();
        $customerRole = Role::where('name', 'customer')->first();

        // Admin user
        User::create([
            'name' => 'Shop Admin',
            'email' => 'tharunyathangavel@gmail.com',
            'password' => bcrypt('admin123'),
            'dob' => '1990-01-01',
            'phone' => '07700000000',
            'address' => 'Premier Shop HQ, London, UK',
            'role_id' => $adminRole->id,
        ]);

        // Sample customer
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'dob' => '2000-05-15',
            'phone' => '07700000001',
            'address' => '123 High Street, London',
            'role_id' => $customerRole->id,
        ]);

        // Categories
        $categoriesNames = [
            "Beer, Cider & Alcoholic RTD's",
            "Biscuits",
            "Bread & Cakes",
            "Confectionery",
            "Crisps, Snacks & Dips",
            "Food & Drink Disposables",
            "Fresh Food",
            "Frozen Food",
            "Greengrocery",
            "Grocery - Retail",
            "Grocery - Catering",
            "Health, Beauty & Baby",
            "Hot Drinks",
            "Household, Cleaning & Paper",
            "Meat, Fish & Poultry",
            "Non-Food",
            "Pet Food",
            "Seasonal",
            "Soft Drinks",
            "Spirits & Liqueurs",
            "Tobacco & Cigarettes",
            "Wine"
        ];

        $categories = [];
        foreach ($categoriesNames as $name) {
            $categories[$name] = Category::create([
                'name' => $name,
                'description' => 'Quality ' . $name . ' products at Premier Shop.'
            ]);
        }

        // Products
        Product::create([
            'name' => 'Premium Lager 4x440ml',
            'description' => 'Refreshing premium lager with a crisp taste.',
            'price' => 5.50,
            'stock' => 100,
            'category_id' => $categories["Beer, Cider & Alcoholic RTD's"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => true,
            'images' => ['https://images.unsplash.com/photo-1518176259641-0f3399a8e0f6?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'Digestive Biscuits 400g',
            'description' => 'Classic sweet-meal biscuits, perfect with tea.',
            'price' => 1.20,
            'stock' => 200,
            'category_id' => $categories["Biscuits"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
            'images' => ['https://images.unsplash.com/photo-1558961363-fa8fdf82db35?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'White Sliced Bread 800g',
            'description' => 'Freshly baked soft white sliced bread.',
            'price' => 1.10,
            'stock' => 50,
            'category_id' => $categories["Bread & Cakes"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
            'images' => ['https://images.unsplash.com/photo-1590127883220-41315573431d?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'Milk Chocolate Bar 100g',
            'description' => 'Smooth and creamy milk chocolate.',
            'price' => 1.00,
            'stock' => 300,
            'category_id' => $categories["Confectionery"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
            'images' => ['https://images.unsplash.com/photo-1511381939415-e44015466834?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'Potato Crisps Ready Salted 6pk',
            'description' => 'Crunchy potato crisps with a touch of salt.',
            'price' => 1.85,
            'stock' => 150,
            'category_id' => $categories["Crisps, Snacks & Dips"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
            'images' => ['https://images.unsplash.com/photo-1566478989037-eec170784d0b?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'Disposable Paper Plates 50pk',
            'description' => 'Eco-friendly sturdy paper plates for parties.',
            'price' => 3.50,
            'stock' => 80,
            'category_id' => $categories["Food & Drink Disposables"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
            'images' => ['https://images.unsplash.com/photo-1517094857443-3957597f8040?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'Whole Milk 2L',
            'description' => 'Fresh farm-assured whole milk.',
            'price' => 1.65,
            'stock' => 60,
            'category_id' => $categories["Fresh Food"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
            'images' => ['https://images.unsplash.com/photo-1563636619-e910bd29339a?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'Frozen Garden Peas 1kg',
            'description' => 'Sweet and tender garden peas, frozen as soon as picked.',
            'price' => 1.40,
            'stock' => 100,
            'category_id' => $categories["Frozen Food"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
            'images' => ['https://images.unsplash.com/photo-1587049633312-d628ae50a8ae?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'Fresh Bananas 5pk',
            'description' => 'Sweet and ripe premium bananas.',
            'price' => 1.00,
            'stock' => 40,
            'category_id' => $categories["Greengrocery"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
            'images' => ['https://images.unsplash.com/photo-1603833665858-e61d17a86224?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'Baked Beans in Tomato Sauce 415g',
            'description' => 'High protein baked beans in a delicious tomato sauce.',
            'price' => 0.50,
            'stock' => 400,
            'category_id' => $categories["Grocery - Retail"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
            'images' => ['https://images.unsplash.com/photo-1516711696515-992e1069f20e?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'Bulk Long Grain Rice 5kg',
            'description' => 'High quality long grain rice for catering.',
            'price' => 8.99,
            'stock' => 30,
            'category_id' => $categories["Grocery - Catering"]->id,
            'product_type' => 'wholesale',
            'is_age_restricted' => false,
            'images' => ['https://images.unsplash.com/photo-1586201375761-83865001e31c?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'Instant Coffee 200g',
            'description' => 'Rich and smooth freeze-dried instant coffee.',
            'price' => 4.50,
            'stock' => 120,
            'category_id' => $categories["Hot Drinks"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
            'images' => ['https://images.unsplash.com/photo-1559056199-641a0ac8b55e?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'Toilet Tissue 9pk',
            'description' => 'Soft and strong 2-ply toilet tissue.',
            'price' => 3.99,
            'stock' => 90,
            'category_id' => $categories["Household, Cleaning & Paper"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
            'images' => ['https://images.unsplash.com/photo-1584622650111-993a426fbf0a?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'Fresh Chicken Breast 1kg',
            'description' => 'Lean and tender fresh chicken breast fillets.',
            'price' => 7.50,
            'stock' => 45,
            'category_id' => $categories["Meat, Fish & Poultry"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
            'images' => ['https://images.unsplash.com/photo-1604503468506-a8da13d82791?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'Dry Dog Food 2kg',
            'description' => 'Complete and balanced nutrition for adult dogs.',
            'price' => 5.99,
            'stock' => 70,
            'category_id' => $categories["Pet Food"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
            'images' => ['https://images.unsplash.com/photo-1585846416120-3a73c3945937?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'Cola 2L',
            'description' => 'Classic sparkling cola drink.',
            'price' => 1.80,
            'stock' => 140,
            'category_id' => $categories["Soft Drinks"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => false,
            'images' => ['https://images.unsplash.com/photo-1622483767028-3f66f32aef97?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'London Dry Gin 70cl',
            'description' => 'Classic dry gin with juniper notes.',
            'price' => 16.00,
            'stock' => 25,
            'category_id' => $categories["Spirits & Liqueurs"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => true,
            'images' => ['https://images.unsplash.com/photo-1551538597-27153a1d6249?q=80&w=600&auto=format&fit=crop'],
        ]);

        Product::create([
            'name' => 'Merlot Red Wine 75cl',
            'description' => 'Smooth and fruity merlot from Chile.',
            'price' => 7.00,
            'stock' => 40,
            'category_id' => $categories["Wine"]->id,
            'product_type' => 'normal',
            'is_age_restricted' => true,
            'images' => ['https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?q=80&w=600&auto=format&fit=crop'],
        ]);

        // Settings
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'shop_name' => 'Premier Shop',
                'origin_address' => 'GQ6Q+WH London, United Kingdom',
                'free_delivery_threshold' => 100.00,
                'free_delivery_radius_miles' => 20.00,
                'surcharge_per_mile' => 1.50,
                'flat_rate_fee' => 5.99,
            ]
        );

        // Sample Promotion (Slider)
        Promotion::create([
            'title' => 'Spring Sale — Up to 30% Off!',
            'subtitle' => 'Limited Time Offer',
            'description' => 'Enjoy massive discounts on selected items this spring season.',
            'image_path' => 'https://images.unsplash.com/photo-1540959733332-e94e7bf9b102?q=80&w=1080&auto=format&fit=crop',
            'type' => 'slider',
            'is_active' => true,
            'start_date' => now(),
            'end_date' => now()->addMonths(2),
        ]);

        // Sample Promotion (Banner)
        Promotion::create([
            'title' => 'Free Local Delivery',
            'description' => 'On orders over £100 within 20 miles.',
            'image_path' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=600&auto=format&fit=crop',
            'type' => 'banner',
            'is_active' => true,
        ]);
    }
}
