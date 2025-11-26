<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'IDFC', 
            'email' => 'idfc@gmail.com', 
            'password' => Hash::make('idfc@123'),
            'role' => 1,
        ]);
    }
}
