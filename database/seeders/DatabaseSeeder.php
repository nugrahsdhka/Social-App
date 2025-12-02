<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create(
            [
            'name' => 'Dhika Setia',
            'email' => 'dhikasetia06@gmail.com',
            'username' => 'nugrahsdhka', // <--- WAJIB DITAMBAHKAN
            'password' => '#@Dsn280406', // Password default biar gampang login
            ],
        );
    }
}
