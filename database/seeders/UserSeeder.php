<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Ihab Salah',
                'email' => 'ihab@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Alaa Ahmed',
                'email' => 'alaa@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Mohammed Ali',
                'email' => 'mohammed@example.com',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
