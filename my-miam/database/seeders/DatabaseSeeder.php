<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AdminUserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Default test user
        User::factory()->create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create administrator role and admin user
        $this->call([AdminUserSeeder::class]);
        $this->call([RolesAndPermissionsSeeder::class]);
    }
}
