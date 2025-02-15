<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\BookFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

         \App\Models\User::factory()->create([
             'username' => 'admin',
             'name' => 'admin',
             'email' => 'admin@test.com',
         ]);

         $this->call([
            BookSeeder::class,
            BadgeSeeder::class,
            PostSeeder::class,
         ]);
    }
}
