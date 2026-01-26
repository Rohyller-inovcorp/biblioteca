<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Admin Biblioteca',
            'email' => 'admin@biblioteca.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);
        User::create([
            'name' => 'User Biblioteca',
            'email' => 'user@biblioteca.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_USER,            
        ]);
    }
}
