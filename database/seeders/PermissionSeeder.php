<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            'books.view',
            'books.create',
            'books.edit',
            'books.delete',
            
            'posts.view',
            'posts.create',
            'posts.edit',
            'posts.delete',
            
            'comments.view',
            'comments.create',
            'comments.edit',
            'comments.delete',
            
            'admin.access',
            'admin.dashboard',
            'admin.users',
            'admin.books',
            'admin.posts',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}