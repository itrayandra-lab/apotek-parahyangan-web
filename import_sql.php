<?php

require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Database configuration
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$database = $_ENV['DB_DATABASE'] ?? 'apotek_parahyangan_db';
$username = $_ENV['DB_USERNAME'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';

// SQL file path
$sqlFile = __DIR__ . '/temp/apotek_parahyangan_db.sql';

echo "Starting SQL import...\n";
echo "Database: {$database}\n";
echo "Host: {$host}:{$port}\n";
echo "SQL File: {$sqlFile}\n\n";

// Check if SQL file exists
if (!file_exists($sqlFile)) {
    echo "ERROR: SQL file not found at {$sqlFile}\n";
    exit(1);
}

try {
    // Create PDO connection
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "Connected to MySQL server successfully.\n";

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '{$database}' created/verified.\n";

    // Use the database
    $pdo->exec("USE `{$database}`");
    echo "Using database '{$database}'.\n\n";

    // Read SQL file
    $sql = file_get_contents($sqlFile);
    
    if ($sql === false) {
        throw new Exception("Failed to read SQL file");
    }

    echo "SQL file loaded. Size: " . number_format(strlen($sql)) . " bytes\n";

    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^\s*--/', $stmt);
        }
    );

    echo "Found " . count($statements) . " SQL statements to execute.\n\n";

    // Execute each statement
    $successCount = 0;
    $errorCount = 0;

    foreach ($statements as $index => $statement) {
        try {
            if (trim($statement)) {
                $pdo->exec($statement);
                $successCount++;
                
                // Show progress every 10 statements
                if (($index + 1) % 10 === 0) {
                    echo "Executed " . ($index + 1) . " statements...\n";
                }
            }
        } catch (PDOException $e) {
            $errorCount++;
            echo "ERROR in statement " . ($index + 1) . ": " . $e->getMessage() . "\n";
            echo "Statement: " . substr($statement, 0, 100) . "...\n\n";
            
            // Continue with next statement instead of stopping
            continue;
        }
    }

    echo "\n=== IMPORT COMPLETED ===\n";
    echo "Successful statements: {$successCount}\n";
    echo "Failed statements: {$errorCount}\n";
    echo "Total statements: " . count($statements) . "\n";

    if ($errorCount === 0) {
        echo "\n✅ SQL import completed successfully!\n";
    } else {
        echo "\n⚠️  SQL import completed with {$errorCount} errors.\n";
    }

    // Show tables in database
    echo "\n=== TABLES IN DATABASE ===\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
        echo "- {$table}: {$count} records\n";
    }

} catch (PDOException $e) {
    echo "DATABASE ERROR: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nDone.\n";