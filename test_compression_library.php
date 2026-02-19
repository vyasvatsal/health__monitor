<?php

require __DIR__ . '/vendor/autoload.php';

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

try {
    $manager = new ImageManager(new Driver());

    // Create a blank image
    $image = $manager->create(100, 100)->fill('ff0000');

    // Encode as JPEG
    $encoded = $image->toJpeg(50);

    echo "Success: Image created and compressed. Size: " . strlen((string) $encoded) . " bytes\n";

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
