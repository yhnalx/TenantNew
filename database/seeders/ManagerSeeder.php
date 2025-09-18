<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ManagerSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'manager@example.com'], // change email if you want
            [
                'name' => 'Tenant Manager',
                'password' => Hash::make('password123'), // set secure password
                'role' => 'manager',
                'status' => 'approved'
            ]
        );
    }
}
