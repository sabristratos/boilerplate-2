<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User permissions
            'view users', 'create users', 'edit users', 'delete users',
            // Content permissions
            'view content', 'create content', 'edit content', 'delete content', 'publish content',
            // Settings permissions
            'view settings', 'edit settings', 'settings.general.manage', 'settings.appearance.manage',
            'settings.email.manage', 'settings.security.manage', 'settings.social.manage',
            'settings.advanced.manage', 'settings.contact.manage'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        // User role
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->givePermissionTo('view content');

        // Editor role
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->givePermissionTo([
            'view content',
            'create content',
            'edit content',
            'delete content',
            'publish content',
            'view settings'
        ]);

        // Admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view content',
            'create content',
            'edit content',
            'delete content',
            'publish content',
            'view settings'
        ]);

        // Super Admin role
        // Super Admin has all permissions via Gate::before rule in AppServiceProvider
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());
    }
}
