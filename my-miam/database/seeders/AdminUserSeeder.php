<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create administrator role if it doesn't exist
        $role = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);

        // Create admin user (change email/password as needed)
        $user = User::firstOrCreate(
            ['email' => 'admin@zeducspace.com'],
            [
                'full_name' => 'Admin Restaurant',
                'password' => Hash::make('MonSuperAdmin123'),
                'phone_number' => '0000000000',
                'localisation' => 'Bureau Admin',
                'role' => 'administrator',
            ]
        );

        // Assign role
        if (! $user->hasRole('administrator')) {
            $user->assignRole($role);
        }
    }
}
