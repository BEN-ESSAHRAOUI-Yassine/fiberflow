<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@fiberflow.ma',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Ahmed Alami',
            'email' => 'engineer1@fiberflow.ma',
            'role' => 'ingenieur',
        ]);

        User::factory()->create([
            'name' => 'Sara Benali',
            'email' => 'engineer2@fiberflow.ma',
            'role' => 'ingenieur',
        ]);
    }
}
