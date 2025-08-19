<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $moderatorRole = Role::create(['name' => 'moderator']);
        $userRole = Role::create(['name' => 'user']);

        $adminPermissions = [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'books.view', 'books.create', 'books.edit', 'books.delete',
            'posts.view', 'posts.create', 'posts.edit', 'posts.delete',
            'comments.view', 'comments.create', 'comments.edit', 'comments.delete',
            'admin.access', 'admin.dashboard', 'admin.users', 'admin.books', 'admin.posts',
        ];

        $moderatorPermissions = [
            'users.view',
            'books.view', 'books.create', 'books.edit',
            'posts.view', 'posts.edit', 'posts.delete',
            'comments.view', 'comments.edit', 'comments.delete',
            'admin.access', 'admin.dashboard', 'admin.books', 'admin.posts',
        ];

        $userPermissions = [
            'books.view',
            'posts.view', 'posts.create', 'posts.edit',
            'comments.view', 'comments.create', 'comments.edit',
        ];

        $adminRole->givePermissionTo($adminPermissions);
        $moderatorRole->givePermissionTo($moderatorPermissions);
        $userRole->givePermissionTo($userPermissions);

        $adminUser = \App\Models\User::where('email', 'admin@test.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('admin');
        }
    }
}
