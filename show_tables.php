<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = \DB::select('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE()');

echo "\n";
echo "╔════════════════════════════════════════╗\n";
echo "║       DATABASE TABLES                  ║\n";
echo "╚════════════════════════════════════════╝\n\n";

$count = 0;
foreach ($tables as $table) {
    $count++;
    echo sprintf("%2d. %s\n", $count, $table->TABLE_NAME);
}

echo "\n";
echo "════════════════════════════════════════\n";
echo "Total: $count tables\n";
echo "════════════════════════════════════════\n\n";
