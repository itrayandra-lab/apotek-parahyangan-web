<?php

// Simple SQL import script
$sqlFile = 'temp/apotek_parahyangan_db.sql';

echo "Checking SQL file...\n";

if (!file_exists($sqlFile)) {
    echo "ERROR: File not found: $sqlFile\n";
    exit(1);
}

$fileSize = filesize($sqlFile);
echo "SQL file found. Size: " . number_format($fileSize) . " bytes\n";

// Read first few lines to check content
$handle = fopen($sqlFile, 'r');
$firstLines = [];
for ($i = 0; $i < 10 && !feof($handle); $i++) {
    $firstLines[] = trim(fgets($handle));
}
fclose($handle);

echo "\nFirst 10 lines of SQL file:\n";
foreach ($firstLines as $i => $line) {
    echo ($i + 1) . ": " . substr($line, 0, 80) . "\n";
}

echo "\nTo import this SQL file manually, use one of these commands:\n";
echo "1. MySQL command line:\n";
echo "   mysql -u root -h 127.0.0.1 apotek_parahyangan_db < temp/apotek_parahyangan_db.sql\n\n";
echo "2. Laravel Artisan:\n";
echo "   php artisan db:wipe\n";
echo "   php artisan migrate:fresh\n";
echo "   Then import the SQL file\n\n";
echo "3. phpMyAdmin:\n";
echo "   - Open phpMyAdmin\n";
echo "   - Select apotek_parahyangan_db database\n";
echo "   - Go to Import tab\n";
echo "   - Choose the SQL file\n";
echo "   - Click Go\n";

?>