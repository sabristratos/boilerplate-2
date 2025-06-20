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
     */
    public function run(): void
    {
        // Seed roles and permissions
        $this->call(RolesAndPermissionsSeeder::class);

        // Create a test user with Super Admin role
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $user->assignRole('Super Admin');

        // Create additional test users with different roles
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $adminUser->assignRole('admin');

        $editorUser = User::factory()->create([
            'name' => 'Editor User',
            'email' => 'editor@example.com',
        ]);
        $editorUser->assignRole('editor');

        $regularUser = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
        ]);
        $regularUser->assignRole('user');

        // Sync settings
        Artisan::call('settings:sync');
    }
}
