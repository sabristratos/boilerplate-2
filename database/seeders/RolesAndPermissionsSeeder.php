<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
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
            'dashboard.view',

            'pages.view',
            'pages.edit',
            'pages.create',
            'pages.delete',

            // User permissions
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Role permissions
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Settings permissions
            'settings.general.manage',
            'settings.appearance.manage',
            'settings.contact.manage',
            'settings.email.manage',
            'settings.security.manage',
            'settings.social.manage',
            'settings.advanced.manage',
            'settings.content.manage',
            'settings.navigation.manage',
            'settings.seo.manage',

            // Media permissions
            'media.view',
            'media.create',
            'media.edit',
            'media.delete',

            // Testimonial permissions
            'testimonials.view',
            'testimonials.create',
            'testimonials.edit',
            'testimonials.reorder',
            'testimonials.delete',

            // Form permissions
            'forms.view',
            'forms.create',
            'forms.edit',
            'forms.delete',
            'forms.submissions.view',

            // Backup permissions
            'backup.view',
            'backup.create',
            'backup.download',
            'backup.delete',

            // Content permissions
            'edit content',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to the admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo($permissions);

        // Editor Role
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->givePermissionTo([
            'dashboard.view',
            'pages.view',
            'pages.edit',
            'pages.create',
            'pages.delete',
            'edit content',
            'media.view',
            'media.create',
            'media.edit',
            'media.delete',
            'testimonials.view',
            'testimonials.create',
            'testimonials.edit',
            'testimonials.reorder',
            'testimonials.delete',
            'backup.view',
        ]);

        // User role
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->givePermissionTo('pages.view');

        // Super Admin role
        // Super Admin has all permissions via Gate::before rule in AppServiceProvider
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());
    }
}
