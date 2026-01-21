<?php

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "=== SQL IMPORT SCRIPT ===\n";
echo "Starting import process...\n\n";

$sqlFile = __DIR__ . '/temp/apotek_parahyangan_db.sql';

echo "SQL File: {$sqlFile}\n";

// Check if file exists
if (!file_exists($sqlFile)) {
    echo "ERROR: SQL file not found!\n";
    exit(1);
}

$fileSize = filesize($sqlFile);
echo "File size: " . number_format($fileSize) . " bytes\n\n";

try {
    // Test database connection
    echo "Testing database connection...\n";
    $pdo = DB::connection()->getPdo();
    echo "✅ Database connection successful\n\n";
    
    // Read SQL file
    echo "Reading SQL file...\n";
    $sql = file_get_contents($sqlFile);
    
    if (empty($sql)) {
        echo "ERROR: SQL file is empty\n";
        exit(1);
    }
    
    echo "✅ SQL file loaded successfully\n";
    echo "Content length: " . number_format(strlen($sql)) . " characters\n\n";
    
    // Disable foreign key checks
    echo "Disabling foreign key checks...\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    
    // Split SQL into statements
    echo "Parsing SQL statements...\n";
    
    // Remove MySQL-specific comments and settings
    $sql = preg_replace('/\/\*!.*?\*\/;?\s*/s', '', $sql);
    $sql = preg_replace('/--.*$/m', '', $sql);
    
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
    echo "Found {$totalStatements} valid SQL statements\n\n";
    
    if ($totalStatements === 0) {
        echo "No valid statements found!\n";
        exit(1);
    }
    
    // Execute statements
    echo "Executing SQL statements...\n";
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($validStatements as $index => $statement) {
        try {
            DB::unprepared($statement);
            $successCount++;
            
            // Show progress every 10 statements
            if (($index + 1) % 10 === 0) {
                echo "Executed " . ($index + 1) . "/{$totalStatements} statements...\n";
            }
            
        } catch (Exception $e) {
            $errorCount++;
            echo "ERROR in statement " . ($index + 1) . ": " . $e->getMessage() . "\n";
            echo "Statement: " . substr($statement, 0, 100) . "...\n\n";
            
            // Continue with next statement
            continue;
        }
    }
    
    // Re-enable foreign key checks
    echo "\nRe-enabling foreign key checks...\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    
    echo "\n=== IMPORT RESULTS ===\n";
    echo "Total statements: {$totalStatements}\n";
    echo "Successful: {$successCount}\n";
    echo "Failed: {$errorCount}\n";
    
    if ($errorCount === 0) {
        echo "\n✅ Import completed successfully!\n";
    } else {
        echo "\n⚠️  Import completed with {$errorCount} errors\n";
    }
    
    // Show tables
    echo "\n=== DATABASE TABLES ===\n";
    try {
        $tables = DB::select('SHOW TABLES');
        $databaseName = config('database.connections.mysql.database');
        $tableColumn = "Tables_in_{$databaseName}";
        
        foreach ($tables as $table) {
            $tableName = $table->$tableColumn;
            try {
                $count = DB::table($tableName)->count();
                echo "- {$tableName}: {$count} records\n";
            } catch (Exception $e) {
                echo "- {$tableName}: Error counting\n";
            }
        }
        
    } catch (Exception $e) {
        echo "Could not show tables: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 Import process completed!\n";
    
} catch (Exception $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}