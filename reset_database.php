<?php

// Reset database to original state
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

echo "=== RESET DATABASE TO ORIGINAL STATE ===\n";
echo "This will:\n";
echo "1. Drop all tables\n";
echo "2. Run fresh migrations\n";
echo "3. Run seeders\n\n";

try {
    // Test database connection
    echo "Testing database connection...\n";
    $pdo = DB::connection()->getPdo();
    echo "✅ Database connection successful\n\n";
    
    // Get current database name
    $database = config('database.connections.mysql.database');
    echo "Database: {$database}\n\n";
    
    // Show current tables
    echo "Current tables in database:\n";
    try {
        $tables = DB::select('SHOW TABLES');
        $tableColumn = "Tables_in_{$database}";
        
        if (empty($tables)) {
            echo "- No tables found\n";
        } else {
            foreach ($tables as $table) {
                $tableName = $table->$tableColumn;
                echo "- {$tableName}\n";
            }
        }
    } catch (Exception $e) {
        echo "Could not show tables: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    // Reset database
    echo "Resetting database...\n";
    
    // Drop all tables (fresh start)
    echo "1. Dropping all tables...\n";
    Artisan::call('db:wipe', ['--force' => true]);
    echo "✅ All tables dropped\n";
    
    // Run fresh migrations
    echo "2. Running fresh migrations...\n";
    Artisan::call('migrate:fresh', ['--force' => true]);
    echo "✅ Fresh migrations completed\n";
    
    // Run seeders
    echo "3. Running seeders...\n";
    Artisan::call('db:seed', ['--force' => true]);
    echo "✅ Seeders completed\n";
    
    echo "\n=== RESET COMPLETED ===\n";
    
    // Show new tables
    echo "New tables after reset:\n";
    try {
        $tables = DB::select('SHOW TABLES');
        
        if (empty($tables)) {
            echo "- No tables found\n";
        } else {
            foreach ($tables as $table) {
                $tableName = $table->$tableColumn;
                try {
                    $count = DB::table($tableName)->count();
                    echo "- {$tableName}: {$count} records\n";
                } catch (Exception $e) {
                    echo "- {$tableName}: Error counting\n";
                }
            }
        }
    } catch (Exception $e) {
        echo "Could not show new tables: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 Database reset to original Laravel state!\n";
    echo "\nNext steps:\n";
    echo "1. Clear Laravel cache: php artisan config:clear\n";
    echo "2. Test application: php artisan serve\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}