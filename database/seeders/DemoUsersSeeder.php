<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Admin', 'password' => '123456', 'role' => 'admin'],
        );

        User::updateOrCreate(
            ['email' => 'vuongcongtuan2005@gmail.com'],
            ['name' => 'Vuong Cong Tuan', 'password' => '123456', 'role' => 'customer'],
        );
    }
}
