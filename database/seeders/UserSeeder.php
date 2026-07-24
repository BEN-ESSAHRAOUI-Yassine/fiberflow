<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@fiberflow.ma',
        ]);

        User::factory()->engineer()->create([
            'name' => 'Ahmed Alami',
            'email' => 'engineer1@fiberflow.ma',
        ]);

        User::factory()->engineer()->create([
            'name' => 'Sara Benali',
            'email' => 'engineer2@fiberflow.ma',
        ]);
    }
}
