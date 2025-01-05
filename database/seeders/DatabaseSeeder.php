<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'account_type' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin1234'),
        ]);

        User::factory()->create([
            'name' => 'Test',
            'account_type' => 'user',
            'email' => 'test@test.com',
            'password' => bcrypt('test1234'),
        ]);
    }
}
