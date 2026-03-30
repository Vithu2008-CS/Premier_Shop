<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RecentlyViewed;
use Illuminate\Support\Facades\DB;

DB::enableQueryLog();
try {
    $oldest = RecentlyViewed::where('user_id', 1)
        ->orderByDesc('viewed_at')
        ->skip(10)
        ->take(100)
        ->pluck('id');
    echo "Query successful\n";
} catch (\Exception $e) {
    echo "Query failed: " . $e->getMessage() . "\n";
}
print_r(DB::getQueryLog());
