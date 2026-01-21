<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportSqlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:import-sql {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import SQL file to database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file');
        
        $this->info("Starting SQL import...");
        $this->info("File: {$filePath}");
        
        // Check if file exists
        if (!File::exists($filePath)) {
            $this->error("SQL file not found: {$filePath}");
            return 1;
        }
        
        $fileSize = File::size($filePath);
        $this->info("File size: " . number_format($fileSize) . " bytes");
        
        try {
            // Read SQL file
            $sql = File::get($filePath);
            
            if (empty($sql)) {
                $this->error("SQL file is empty");
                return 1;
            }
            
            $this->info("SQL file loaded successfully");
            
            // Split SQL into statements
            $statements = $this->splitSqlStatements($sql);
            $totalStatements = count($statements);
            
            $this->info("Found {$totalStatements} SQL statements");
            
            if ($totalStatements === 0) {
                $this->warn("No SQL statements found in file");
                return 0;
            }
            
            // Confirm before proceeding
            if (!$this->confirm("Do you want to execute {$totalStatements} SQL statements?")) {
                $this->info("Import cancelled");
                return 0;
            }
            
            // Execute statements
            $successCount = 0;
            $errorCount = 0;
            
            $progressBar = $this->output->createProgressBar($totalStatements);
            $progressBar->start();
            
            foreach ($statements as $index => $statement) {
                try {
                    if (trim($statement)) {
                        DB::unprepared($statement);
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->newLine();
                    $this->error("Error in statement " . ($index + 1) . ": " . $e->getMessage());
                    $this->line("Statement: " . substr($statement, 0, 100) . "...");
                }
                
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine(2);
            
            // Show results
            $this->info("=== IMPORT COMPLETED ===");
            $this->info("Successful statements: {$successCount}");
            
            if ($errorCount > 0) {
                $this->warn("Failed statements: {$errorCount}");
            }
            
            $this->info("Total statements: {$totalStatements}");
            
            // Show tables
            $this->showDatabaseTables();
            
            if ($errorCount === 0) {
                $this->info("✅ SQL import completed successfully!");
                return 0;
            } else {
                $this->warn("⚠️  SQL import completed with {$errorCount} errors.");
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Split SQL content into individual statements
     */
    private function splitSqlStatements(string $sql): array
    {
        // Remove comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        // Split by semicolon
        $statements = explode(';', $sql);
        
        // Filter empty statements
        return array_filter(
            array_map('trim', $statements),
            function($stmt) {
                return !empty($stmt) && strlen($stmt) > 5;
            }
        );
    }
    
    /**
     * Show database tables with record counts
     */
    private function showDatabaseTables(): void
    {
        try {
            $this->newLine();
            $this->info("=== TABLES IN DATABASE ===");
            
            $tables = DB::select('SHOW TABLES');
            $databaseName = config('database.connections.mysql.database');
            $tableColumn = "Tables_in_{$databaseName}";
            
            foreach ($tables as $table) {
                $tableName = $table->$tableColumn;
                
                try {
                    $count = DB::table($tableName)->count();
                    $this->line("- {$tableName}: {$count} records");
                } catch (\Exception $e) {
                    $this->line("- {$tableName}: Error counting records");
                }
            }
            
        } catch (\Exception $e) {
            $this->warn("Could not show tables: " . $e->getMessage());
        }
    }
}