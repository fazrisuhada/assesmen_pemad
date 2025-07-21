<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['user_name' => 'fazri', 'password' => 'password'],
            ['user_name' => 'reza', 'password' => 'password'],
            ['user_name' => 'budi', 'password' => 'password'],
            ['user_name' => 'andi', 'password' => 'password'],
            ['user_name' => 'lisa', 'password' => 'password'],
            ['user_name' => 'sari', 'password' => 'password'],
            ['user_name' => 'dian', 'password' => 'password'],
            ['user_name' => 'bayu', 'password' => 'password'],
            ['user_name' => 'indra', 'password' => 'password'],
            ['user_name' => 'tari', 'password' => 'password'],
        ];

        foreach ($users as $user) {
            User::create([
                'user_name' => $user['user_name'],
                'password' => Hash::make($user['password']),
            ]);
        }
    }
}
