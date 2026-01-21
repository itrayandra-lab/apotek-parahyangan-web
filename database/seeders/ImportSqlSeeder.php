<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportSqlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlFile = base_path('temp/apotek_parahyangan_db.sql');
        
        $this->command->info("Starting SQL import from: {$sqlFile}");
        
        if (!File::exists($sqlFile)) {
            $this->command->error("SQL file not found: {$sqlFile}");
            return;
        }
        
        $fileSize = File::size($sqlFile);
        $this->command->info("File size: " . number_format($fileSize) . " bytes");
        
        try {
            // Read SQL file
            $sql = File::get($sqlFile);
            
            if (empty($sql)) {
                $this->command->error("SQL file is empty");
                return;
            }
            
            $this->command->info("SQL file loaded successfully");
            
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Split SQL into statements
            $statements = $this->splitSqlStatements($sql);
            $totalStatements = count($statements);
            
            $this->command->info("Found {$totalStatements} SQL statements");
            
            // Execute statements
            $successCount = 0;
            $errorCount = 0;
            
            $bar = $this->command->getOutput()->createProgressBar($totalStatements);
            $bar->start();
            
            foreach ($statements as $index => $statement) {
                try {
                    if (trim($statement)) {
                        DB::unprepared($statement);
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->command->newLine();
                    $this->command->error("Error in statement " . ($index + 1) . ": " . $e->getMessage());
                    $this->command->line("Statement: " . substr($statement, 0, 100) . "...");
                }
                
                $bar->advance();
            }
            
            $bar->finish();
            $this->command->newLine(2);
            
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            // Show results
            $this->command->info("=== IMPORT COMPLETED ===");
            $this->command->info("Successful statements: {$successCount}");
            
            if ($errorCount > 0) {
                $this->command->warn("Failed statements: {$errorCount}");
            }
            
            $this->command->info("Total statements: {$totalStatements}");
            
            // Show tables
            $this->showDatabaseTables();
            
            if ($errorCount === 0) {
                $this->command->info("✅ SQL import completed successfully!");
            } else {
                $this->command->warn("⚠️  SQL import completed with {$errorCount} errors.");
            }
            
        } catch (\Exception $e) {
            $this->command->error("Import failed: " . $e->getMessage());
        }
    }
    
    /**
     * Split SQL content into individual statements
     */
    private function splitSqlStatements(string $sql): array
    {
        // Remove MySQL-specific comments
        $sql = preg_replace('/\/\*!.*?\*\/;?/s', '', $sql);
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        // Split by semicolon
        $statements = explode(';', $sql);
        
        // Filter empty statements
        return array_filter(
            array_map('trim', $statements),
            function($stmt) {
                return !empty($stmt) && strlen($stmt) > 10 && !preg_match('/^(SET|\/\*!)/', $stmt);
            }
        );
    }
    
    /**
     * Show database tables with record counts
     */
    private function showDatabaseTables(): void
    {
        try {
            $this->command->newLine();
            $this->command->info("=== TABLES IN DATABASE ===");
            
            $tables = DB::select('SHOW TABLES');
            $databaseName = config('database.connections.mysql.database');
            $tableColumn = "Tables_in_{$databaseName}";
            
            foreach ($tables as $table) {
                $tableName = $table->$tableColumn;
                
                try {
                    $count = DB::table($tableName)->count();
                    $this->command->line("- {$tableName}: {$count} records");
                } catch (\Exception $e) {
                    $this->command->line("- {$tableName}: Error counting records");
                }
            }
            
        } catch (\Exception $e) {
            $this->command->warn("Could not show tables: " . $e->getMessage());
        }
    }
}