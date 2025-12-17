<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Person;
use App\Models\Premise;
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
        $this->call(RoleSeeder::class);
        $this->call(CountrieSeeder::class);
        $admin = User::firstOrCreate([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
        ], [
            'password' => bcrypt('p@ssw0rd'),
        ]);
        $admin->assignRole('Super-admin');

    }
}
