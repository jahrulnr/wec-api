<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ApiCriteriaSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'test@example.com',
            'password' => Hash::make('test'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);


        // Seed API criteria for the API switcher
        $this->call([
            ApiCriteriaSeeder::class,
            RolesAndPermissionsSeeder::class,
        ]);
    }
}
