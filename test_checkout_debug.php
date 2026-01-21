<?php

// Simple test to debug checkout issues
echo "=== Checkout Debug Test ===\n";

// Test 1: Check if migrations ran
echo "1. Testing database structure...\n";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=apotek_parahyangan_db', 'root', '');
    
    // Check if invoice_number column exists
    $stmt = $pdo->query("DESCRIBE orders");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (in_array('invoice_number', $columns)) {
        echo "   ✅ invoice_number column exists\n";
    } else {
        echo "   ❌ invoice_number column missing\n";
    }
    
    if (in_array('metadata', $columns)) {
        echo "   ✅ metadata column exists\n";
    } else {
        echo "   ❌ metadata column missing\n";
    }
    
    // Check if activities table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'activities'");
    if ($stmt->rowCount() > 0) {
        echo "   ✅ activities table exists\n";
    } else {
        echo "   ❌ activities table missing\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
}

// Test 2: Check environment variables
echo "\n2. Testing Midtrans configuration...\n";
$envFile = '.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    
    if (strpos($envContent, 'MIDTRANS_SERVER_KEY') !== false) {
        echo "   ✅ MIDTRANS_SERVER_KEY found in .env\n";
    } else {
        echo "   ❌ MIDTRANS_SERVER_KEY missing in .env\n";
    }
    
    if (strpos($envContent, 'MIDTRANS_CLIENT_KEY') !== false) {
        echo "   ✅ MIDTRANS_CLIENT_KEY found in .env\n";
    } else {
        echo "   ❌ MIDTRANS_CLIENT_KEY missing in .env\n";
    }
} else {
    echo "   ❌ .env file not found\n";
}

echo "\n=== Debug Complete ===\n";
echo "If you see any ❌ errors above, those need to be fixed first.\n";
echo "Check storage/logs/laravel.log for detailed error messages.\n";