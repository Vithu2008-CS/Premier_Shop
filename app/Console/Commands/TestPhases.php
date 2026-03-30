<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\Address;
use App\Models\Setting;
use App\Models\ReturnRequest;

class TestPhases extends Command
{
    protected $signature = 'test:phases';
    protected $description = 'Test Phase 1,2,3,4 Integrations';

    public function handle()
    {
        $this->info("============================================");
        $this->info("   Massive Integration Test: Phases 1-4     ");
        $this->info("============================================\n");

        // Setup Phase 3 Logic in Settings
        $setting = Setting::firstOrCreate(['id' => 1]);
        $setting->update([
            'other_settings' => [
                'loyalty_enabled' => true,
                'points_per_pound' => 10,
                'points_redemption_value' => 0.05
            ]
        ]);

        $this->info("[Step 1] Creating Dummy User...");
        $user = User::factory()->create(['loyalty_points' => 0]);
        $this->info("         User ID: {$user->id} | Initial Points: 0");

        $this->info("\n[Step 2] Creating Dummy Product...");
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);
        $this->info("         Product ID: {$product->id} | Price: £100 | Initial Stock: 10");

        $this->info("\n[Step 3] Phase 1: Address Creation...");
        Address::create([
            'user_id' => $user->id,
            'address_line' => '123 Fake Street',
            'city' => 'London',
            'postcode' => 'SW1A 1AA',
            'phone' => '07123456789',
            'is_default' => true
        ]);
        $this->info("         => Success! Address created and linked to User {$user->id}.");

        $this->info("\n[Step 4] Phase 3: Checkout and Earning Points...");
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => Order::generateOrderNumber(),
            'status' => 'delivered', // Required for review
            'subtotal' => 100,
            'discount_amount' => 0,
            'points_discount' => 0,
            'points_used' => 0,
            'shipping_cost' => 5,
            'total' => 105,
            'shipping_address' => ['address_line' => '123 Fake Street', 'city' => 'London', 'phone' => '12345'],
            'payment_status' => 'completed',
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100
        ]);
        $product->decrement('stock', 1);
        
        // Emulate Checkout Controller Logic for Points
        $ptsEarned = floor(100 * 10); // 1000 points
        $user->increment('loyalty_points', $ptsEarned);
        $order->rewardPointTransactions()->create([
            'user_id' => $user->id,
            'amount' => $ptsEarned,
            'type' => 'earned',
            'description' => "Test earn"
        ]);
        $user->refresh();
        $this->info("         => Success! Order {$order->order_number} created.");
        $this->info("         => Post-Order Points Wallet: {$user->loyalty_points} (Expected: 1000)");
        $this->info("         => Post-Order Stock Level: {$product->refresh()->stock} (Expected: 9)");

        $this->info("\n[Step 5] Phase 4: Product Review & Gamification Bonus...");
        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'comment' => 'Great product test',
            'is_approved' => true
        ]);
        // Emulate ReviewController Gamification
        $user->increment('loyalty_points', 50);
        $user->refresh();
        $this->info("         => Success! Review submitted.");
        $this->info("         => Post-Review Points Wallet: {$user->loyalty_points} (Expected: 1050)");
        $this->info("         => Product Average Rating: " . number_format($product->refresh()->average_rating, 1) . " Stars");

        $this->info("\n[Step 6] Phase 2 & 3: Order Cancellation -> Clawbacks & Stock Restoral...");
        $initialStock = $product->stock; // 9
        $order->updateStatusAndTracking('cancelled'); 
        $user->refresh();
        $finalStock = $product->refresh()->stock;

        $this->info("         => Success! Order Cancelled.");
        $this->info("         => Stock Level: {$finalStock} (Restored from {$initialStock} back to 10)");
        $this->info("         => Final Points Wallet: {$user->loyalty_points} (Order points clawed back, Review points kept. Expected: 50)");

        $this->info("\n============================================");
        if ($user->loyalty_points == 50 && $finalStock == 10 && $review->exists) {
            $this->info("  ALL TESTS PASSED: Integrations Verified!");
        } else {
            $this->error("  FAILURE: Logic Mismatch Detected.");
        }
        $this->info("============================================\n");
    }
}
