<?php

// Direct MySQL import without Laravel dependencies
echo "=== DIRECT SQL IMPORT ===\n";

// Database configuration
$host = '127.0.0.1';
$port = '3306';
$database = 'apotek_parahyangan_db';
$username = 'root';
$password = '';

// SQL file path
$sqlFile = __DIR__ . '/temp/apotek_parahyangan_db.sql';

echo "Database: {$database}\n";
echo "Host: {$host}:{$port}\n";
echo "SQL File: {$sqlFile}\n\n";

// Check if SQL file exists
if (!file_exists($sqlFile)) {
    echo "ERROR: SQL file not found at {$sqlFile}\n";
    exit(1);
}

$fileSize = filesize($sqlFile);
echo "File size: " . number_format($fileSize) . " bytes\n";

try {
    // Create PDO connection
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "✅ Connected to MySQL server successfully.\n";

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database '{$database}' created/verified.\n";

    // Use the database
    $pdo->exec("USE `{$database}`");
    echo "✅ Using database '{$database}'.\n\n";

    // Read SQL file
    echo "Reading SQL file...\n";
    $sql = file_get_contents($sqlFile);
    
    if ($sql === false) {
        throw new Exception("Failed to read SQL file");
    }

    echo "✅ SQL file loaded. Size: " . number_format(strlen($sql)) . " bytes\n";

    // Disable foreign key checks
    echo "Disabling foreign key checks...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

    // Clean and split SQL into individual statements
    echo "Parsing SQL statements...\n";
    
    // Remove MySQL-specific comments and settings
    $sql = preg_replace('/\/\*!.*?\*\/;?\s*/s', '', $sql);
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
    
    // Split by semicolon
    $statements = explode(';', $sql);
    
    // Filter valid statements
    $validStatements = [];
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && strlen($statement) > 10) {
            // Skip SET statements and comments
            if (!preg_match('/^(SET|\/\*|#)/', $statement)) {
                $validStatements[] = $statement;
            }
        }
    }
    
    $totalStatements = count($validStatements);
    echo "✅ Found {$totalStatements} valid SQL statements\n\n";

    if ($totalStatements === 0) {
        echo "❌ No valid statements found!\n";
        exit(1);
    }

    // Execute statements
    echo "Executing SQL statements...\n";
    $successCount = 0;
    $errorCount = 0;

    foreach ($validStatements as $index => $statement) {
        try {
            $pdo->exec($statement);
            $successCount++;
            
            // Show progress every 50 statements
            if (($index + 1) % 50 === 0) {
                echo "Executed " . ($index + 1) . "/{$totalStatements} statements...\n";
            }
            
        } catch (PDOException $e) {
            $errorCount++;
            echo "❌ ERROR in statement " . ($index + 1) . ": " . $e->getMessage() . "\n";
            echo "Statement: " . substr($statement, 0, 100) . "...\n\n";
            
            // Continue with next statement
            continue;
        }
    }

    // Re-enable foreign key checks
    echo "\nRe-enabling foreign key checks...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

    echo "\n=== IMPORT RESULTS ===\n";
    echo "Total statements: {$totalStatements}\n";
    echo "Successful: {$successCount}\n";
    echo "Failed: {$errorCount}\n";

    if ($errorCount === 0) {
        echo "\n✅ Import completed successfully!\n";
    } else {
        echo "\n⚠️  Import completed with {$errorCount} errors\n";
    }

    // Show tables with record counts
    echo "\n=== DATABASE TABLES ===\n";
    try {
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            echo "No tables found in database.\n";
        } else {
            foreach ($tables as $table) {
                try {
                    $count = $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
                    echo "- {$table}: " . number_format($count) . " records\n";
                } catch (PDOException $e) {
                    echo "- {$table}: Error counting records\n";
                }
            }
        }
        
    } catch (PDOException $e) {
        echo "Could not show tables: " . $e->getMessage() . "\n";
    }

    echo "\n🎉 Import process completed!\n";

} catch (PDOException $e) {
    echo "❌ DATABASE ERROR: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

?>