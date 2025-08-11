<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

// Update PWA settings
try {
    // Using Spatie Laravel Settings
    $settings = [
        'pwa.pwa_app_name' => 'Play',
        'pwa.pwa_short_name' => 'Play',
        'pwa.pwa_start_url' => '/admin',
        'pwa.pwa_background_color' => '#ffffff',
        'pwa.pwa_theme_color' => '#000000',
        'pwa.pwa_display' => 'standalone',
        'pwa.pwa_orientation' => 'any',
        'pwa.pwa_status_bar' => '#000000',
    ];

    foreach ($settings as $key => $value) {
        try {
            \Spatie\LaravelSettings\Settings::fake([
                $key => $value
            ]);
            echo "Updated: $key = $value\n";
        } catch (Exception $e) {
            echo "Failed to update $key: " . $e->getMessage() . "\n";
        }
    }

    echo "PWA settings updated successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
