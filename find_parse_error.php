<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$files = glob(storage_path('framework/views/*.php'));
foreach ($files as $file) {
    try {
        require_once $file;
    } catch (\ParseError $e) {
        $orig = '#unknown';
        $content = file_get_contents($file);
        if (preg_match('|/\*\s+(.*\.blade\.php)\s+\*/|', $content, $m)) {
            $orig = $m[1];
        }
        echo "ParseError in $file (Original: $orig)\n";
        echo $e->getMessage() . "\n";
    } catch (\Throwable $e) {
        // runtime errors are fine, we only want ParseError
    }
}
echo "Done.\n";
