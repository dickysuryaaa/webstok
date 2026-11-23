<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'pemilik@toko.test'],
            ['name' => 'Pemilik Toko', 'password' => 'password', 'role' => User::ROLE_PEMILIK, 'email_verified_at' => now()],
        );

        User::updateOrCreate(
            ['email' => 'pekerja@toko.test'],
            ['name' => 'Pekerja Toko', 'password' => 'password', 'role' => User::ROLE_PEKERJA, 'email_verified_at' => now()],
        );
    }
}
