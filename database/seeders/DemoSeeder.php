<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * DemoSeeder — additive demo data for client presentations.
 *
 * Creates extra customers, coupons, a spread of orders across the last ~60 days
 * (mixed statuses + payment methods), and approved product reviews so the admin
 * dashboard, sales reports and charts render with realistic data.
 *
 * Safe to run on top of DatabaseSeeder:  php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $customerRole = Role::where('name', 'customer')->first();
        $products = Product::all();
        if ($products->isEmpty()) {
            $this->command->warn('No products found — run DatabaseSeeder first.');

            return;
        }

        // ── Extra demo customers ──────────────────────────────────────────────
        $names = [
            'Emily Carter', 'Mohammed Khan', 'Sophie Walsh',
            'Daniel Owusu', 'Priya Sharma', 'James Bennett',
        ];
        $customers = collect();
        // include the existing seeded John Doe if present
        if ($existing = User::where('email', 'john@example.com')->first()) {
            $customers->push($existing);
        }
        foreach ($names as $i => $name) {
            $customers->push(User::firstOrCreate(
                ['email' => 'customer'.($i + 1).'@example.com'],
                [
                    'name' => $name,
                    'password' => bcrypt('password'),
                    'dob' => '1990-01-01',
                    'phone' => '0770000'.str_pad((string) (1000 + $i), 4, '0', STR_PAD_LEFT),
                    'address' => ($i + 10).' Market Street, London',
                    'role_id' => $customerRole?->id,
                    'loyalty_points' => rand(0, 450),
                ]
            ));
        }

        // ── Coupons ───────────────────────────────────────────────────────────
        $coupons = [
            ['code' => 'WELCOME10', 'discount_type' => 'percentage', 'discount_value' => 10, 'min_order_amount' => 20, 'usage_limit' => 100, 'times_used' => 14],
            ['code' => 'SAVE5',     'discount_type' => 'fixed',      'discount_value' => 5,  'min_order_amount' => 30, 'usage_limit' => 50,  'times_used' => 8],
            ['code' => 'SPRING20',  'discount_type' => 'percentage', 'discount_value' => 20, 'min_order_amount' => 50, 'usage_limit' => 40,  'times_used' => 5],
            ['code' => 'FREESHIP',  'discount_type' => 'fixed',      'discount_value' => 5.99, 'min_order_amount' => 25, 'usage_limit' => null, 'times_used' => 22],
        ];
        foreach ($coupons as $c) {
            Coupon::firstOrCreate(['code' => $c['code']], array_merge($c, [
                'valid_from' => now()->subMonth(),
                'valid_until' => now()->addMonths(2),
                'is_active' => true,
            ]));
        }
        $welcome = Coupon::where('code', 'WELCOME10')->first();

        // ── Orders spread over the last 60 days ───────────────────────────────
        $statuses = ['delivered', 'delivered', 'delivered', 'shipped', 'processing', 'pending', 'cancelled'];
        $methods = ['Debit/Credit Card', 'Debit/Credit Card', 'Bank Transfer'];
        $driver = User::where('email', 'driver@example.com')->first();

        $orderCount = 26;
        for ($n = 0; $n < $orderCount; $n++) {
            $customer = $customers->random();
            $createdAt = Carbon::now()->subDays(rand(0, 60))->subHours(rand(0, 23));
            $status = $statuses[array_rand($statuses)];

            // 1–4 distinct line items
            $lineProducts = $products->random(rand(1, min(4, $products->count())));
            $subtotal = 0;
            $items = [];
            foreach ($lineProducts as $p) {
                $qty = rand(1, 5);
                $subtotal += $p->price * $qty;
                $items[] = ['product_id' => $p->id, 'quantity' => $qty, 'price' => $p->price];
            }
            $subtotal = round($subtotal, 2);

            // Apply WELCOME10 to roughly 1 in 4 qualifying orders
            $discount = 0;
            $couponCode = null;
            if ($welcome && $subtotal >= 20 && rand(1, 4) === 1) {
                $discount = $welcome->calculateDiscount($subtotal);
                $couponCode = $welcome->code;
            }

            $shipping = $subtotal >= 100 ? 0 : 5.99;
            $total = round($subtotal - $discount + $shipping, 2);

            $order = Order::create([
                'user_id' => $customer->id,
                'order_number' => Order::generateOrderNumber(),
                'status' => $status,
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'coupon_code' => $couponCode,
                'points_discount' => 0,
                'points_used' => 0,
                'shipping_cost' => $shipping,
                'total' => $total,
                'shipping_address' => [
                    'name' => $customer->name,
                    'address' => $customer->address ?? '1 High Street',
                    'city' => 'London',
                    'postcode' => 'SW1A 1AA',
                    'phone' => $customer->phone ?? '07700000000',
                ],
                'payment_status' => $status === 'cancelled' ? 'pending' : 'completed',
                'payment_method' => $methods[array_rand($methods)],
                'distance' => rand(1, 18),
                'driver_id' => in_array($status, ['shipped', 'delivered']) ? $driver?->id : null,
            ]);

            foreach ($items as $it) {
                OrderItem::create(array_merge(['order_id' => $order->id], $it));
            }

            // tracking dates by status
            $dates = ['created_at' => $createdAt, 'updated_at' => $createdAt];
            if (in_array($status, ['processing', 'shipped', 'delivered'])) {
                $dates['processing_date'] = (clone $createdAt)->addHours(2);
            }
            if (in_array($status, ['shipped', 'delivered'])) {
                $dates['shipped_date'] = (clone $createdAt)->addDay();
            }
            if ($status === 'delivered') {
                $dates['delivered_date'] = (clone $createdAt)->addDays(2);
            }
            $order->forceFill($dates)->save();
        }

        // ── Approved reviews on distinct products ─────────────────────────────
        $comments = [
            ['Excellent quality', 'Exactly as described, fast delivery.'],
            ['Great value', 'Will definitely buy again.'],
            ['Very happy', 'Good price and well packaged.'],
            ['Recommended', 'Top product, no complaints.'],
            ['Fresh and tasty', 'Arrived in perfect condition.'],
            ['Good service', 'Smooth ordering and quick dispatch.'],
        ];
        $reviewProducts = $products->random(min(12, $products->count()));
        $custArr = $customers->values();
        foreach ($reviewProducts as $idx => $p) {
            $u = $custArr[$idx % $custArr->count()];
            // avoid duplicate (user, product) pairs
            if (Review::where('user_id', $u->id)->where('product_id', $p->id)->exists()) {
                continue;
            }
            [$title, $body] = $comments[array_rand($comments)];
            Review::create([
                'user_id' => $u->id,
                'product_id' => $p->id,
                'rating' => rand(3, 5),
                'title' => $title,
                'comment' => $body,
                'is_approved' => true,
            ]);
        }

        $this->command->info('DemoSeeder complete: '.Order::count().' orders, '.Coupon::count().' coupons, '.Review::count().' reviews, '.User::count().' users.');
    }
}
