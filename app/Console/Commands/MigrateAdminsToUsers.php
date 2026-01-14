<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateAdminsToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:admins-to-users {--force : Force migration without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all admin records to users table with role=admin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $admins = Admin::all();

        if ($admins->isEmpty()) {
            $this->info('No admins found to migrate.');

            return self::SUCCESS;
        }

        $this->info("Found {$admins->count()} admin(s) to migrate.");

        if (! $this->option('force') && ! $this->confirm('Do you want to proceed with migration?')) {
            $this->info('Migration cancelled.');

            return self::FAILURE;
        }

        $bar = $this->output->createProgressBar($admins->count());
        $bar->start();

        $migratedCount = 0;
        $skippedCount = 0;

        foreach ($admins as $admin) {
            // Check if user with same ID already exists
            if (User::where('id', $admin->id)->exists()) {
                $this->newLine();
                $this->warn("User with ID {$admin->id} already exists. Skipping.");
                $skippedCount++;
                $bar->advance();

                continue;
            }

            // Insert admin into users table using raw query to preserve UUID and password_hash
            DB::table('users')->insert([
                'id' => $admin->id,
                'name' => $admin->username,
                'username' => $admin->username,
                'email' => $admin->username.'@beautylatory.local', // Generate email from username
                'password' => $admin->password_hash, // Use existing hashed password
                'role' => 'admin',
                'created_at' => $admin->created_at,
                'updated_at' => $admin->updated_at,
            ]);

            $migratedCount++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('Migration completed successfully!');
        $this->info("Migrated: {$migratedCount}");
        $this->info("Skipped: {$skippedCount}");

        return self::SUCCESS;
    }
}
