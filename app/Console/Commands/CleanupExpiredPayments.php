<?php

namespace App\Console\Commands;

use App\Jobs\HandleExpiredPayments;
use Illuminate\Console\Command;

class CleanupExpiredPayments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'payments:cleanup-expired';

    /**
     * The console command description.
     */
    protected $description = 'Cleanup expired payments and restore cart items';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting expired payments cleanup...');
        
        HandleExpiredPayments::dispatch();
        
        $this->info('Expired payments cleanup job dispatched successfully.');
        
        return Command::SUCCESS;
    }
}