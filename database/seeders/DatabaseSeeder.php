<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * Required environment variables:
     * - ADMIN_NAME: The name of the super admin user
     * - ADMIN_EMAIL: The email address of the super admin user
     * - ADMIN_PASSWORD: The password for the super admin user
     */
    public function run(): void
    {
        // 1. Roles and Permissions
        $this->call(RolesAndPermissionsSeeder::class);

        // 2. Create Users and Assign Roles
        $this->command->info('Creating default users...');
        $this->createUsers();

        // 3. Seed Content (pages first)
        $this->command->info('Seeding content...');
        $this->call([
            PageSeeder::class,
        ]);

        // 4. Sync Settings to create groups (after pages exist)
        $this->command->info('Syncing application settings...');
        Artisan::call('settings:sync');

        // 5. Sync Translations
        $this->command->info('Syncing translations from files...');
        Artisan::call('translations:sync-from-files');
        $this->command->info('Syncing complete.');
    }

    protected function createUsers(): void
    {
        // Get admin credentials from environment variables
        $adminName = env('ADMIN_NAME');
        $adminEmail = env('ADMIN_EMAIL');
        $adminPassword = env('ADMIN_PASSWORD');

        // Validate that all required environment variables are set
        if (!$adminName || !$adminEmail || !$adminPassword) {
            throw new \Exception(
                'Missing required environment variables for admin user. ' .
                'Please set ADMIN_NAME, ADMIN_EMAIL, and ADMIN_PASSWORD in your .env file.'
            );
        }

        // Create Super Admin user
        User::create([
            'name' => $adminName,
            'email' => $adminEmail,
            'password' => bcrypt($adminPassword),
            'email_verified_at' => now(),
        ])->assignRole('Super Admin');

        $this->command->info("Super Admin user created: {$adminEmail}");
    }
}
