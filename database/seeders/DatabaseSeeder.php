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
        User::create([
            'name' => 'Bernard',
            'email' => env('ADMIN_EMAIL', 'admin@sinfat.com'),
            'password' => bcrypt(env('ADMIN_PASSWORD', 'password')),
            'email_verified_at' => now(),
        ]);
    }
}
