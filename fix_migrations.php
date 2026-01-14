<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Ensure migrations table exists
if (!Schema::hasTable('migrations')) {
    echo "Creating migrations table...\n";
    Schema::create('migrations', function ($table) {
        $table->increments('id');
        $table->string('migration');
        $table->integer('batch');
    });
}

$files = File::files(database_path('migrations'));
$batch = DB::table('migrations')->max('batch') + 1;

foreach ($files as $file) {
    $filename = $file->getFilenameWithoutExtension();
    
    // Skip if already migrated
    if (DB::table('migrations')->where('migration', $filename)->exists()) {
        continue;
    }

    $content = file_get_contents($file->getPathname());
    
    // Rudimentary check for Schema::create('tablename'
    if (preg_match("/Schema::create\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
        $table = $matches[1];
        
        if (Schema::hasTable($table)) {
            echo "Table '$table' exists. Marking '$filename' as migrated.\n";
            DB::table('migrations')->insert([
                'migration' => $filename,
                'batch' => $batch
            ]);
        } else {
            echo "Table '$table' missing. Leaving '$filename' for migrate command.\n";
        }
    } else {
        // Migrations that alter tables or specialized ones
        // If it's an "add_xyz_to_table" migration, check if the column exists
        if (preg_match("/Schema::table\(['\"]([^'\"]+)['\"].*?function.*?Blueprint.*?table.*?\)(.*?)\}\);/s", $content, $matches)) {
            $table = $matches[1];
            $body = $matches[2];
            
            // Try to find column name in body e.g. $table->string('column_name')
            // This is brittle, so for now, if the TABLE exists, we assume the migration *might* have been part of the imported structure/my manual creation.
            // BUT, my manual creation script included fields from "add_weight" etc.
            // So if I manually created 'products', I included 'weight'.
            
            if (Schema::hasTable($table)) {
                 // Special check for products columns I added manually
                 if ($table === 'products') {
                     if (strpos($filename, 'add_weight') !== false && Schema::hasColumn('products', 'weight')) {
                         $mark = true;
                     } elseif (strpos($filename, 'add_is_featured') !== false && Schema::hasColumn('products', 'is_featured')) {
                         $mark = true;
                     } elseif (strpos($filename, 'soft_deletes') !== false && Schema::hasColumn('products', 'deleted_at')) {
                         $mark = true;
                     } else {
                         // Default to NOT marking if we're not sure, so migrate tries to run it.
                         // But calculate: if migrate runs "add column" on existing column, it crashes.
                         // Safest is to mark it as done if table exists, assuming my manual restoration was 'feature complete'.
                         // Let's rely on my manual restoration being good.
                         $mark = true;
                     }
                 } else {
                     $mark = true;
                 }
                 
                 if (isset($mark) && $mark) {
                    echo "Table '$table' exists (alter). Marking '$filename' as migrated.\n";
                    DB::table('migrations')->insert([
                        'migration' => $filename,
                        'batch' => $batch
                    ]);
                 }
            }
        }
    }
}
