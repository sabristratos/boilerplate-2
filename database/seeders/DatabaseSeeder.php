<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles and Permissions
        $this->call(RolesAndPermissionsSeeder::class);

        // 2. Create Users and Assign Roles
        $this->command->info('Creating default users...');
        $this->createUsers();

        // 3. Sync Settings to create groups
        $this->command->info('Syncing application settings...');
        Artisan::call('settings:sync');

        // 4. Seed Content
        $this->command->info('Seeding content...');
        $this->call([
            PageSeeder::class,
            FormSeeder::class,
        ]);
        Testimonial::factory(10)->create();

        // 5. Sync Translations
        $this->command->info('Syncing translations from files...');
        Artisan::call('translations:sync-from-files');
        $this->command->info('Syncing complete.');
    }

    protected function createUsers(): void
    {
        // Super Admin
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
        ])->assignRole('Super Admin');

        // Test User (also a Super Admin)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ])->assignRole('Super Admin');

        // Editor
        User::factory()->create([
            'name' => 'Editor User',
            'email' => 'editor@example.com',
        ])->assignRole('editor');

        // Regular User
        User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
        ])->assignRole('user');
    }
}
