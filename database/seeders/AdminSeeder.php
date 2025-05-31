<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Admin',
                'username' => 'admin_uks',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ],
            [
                'name' => 'Staff',
                'username' => 'staff_uks',
                'password' => Hash::make('password123'),
                'role' => 'staff',
            ],
            [
                'name' => 'User',
                'username' => 'user_uks',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['username' => $admin['username']], // Cari berdasarkan username, bukan email
                $admin
            );
        }
    }
}