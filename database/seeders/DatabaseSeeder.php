<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Person;
use App\Models\Premise;
use App\Models\User;
use App\Notifications\PasswordChangeNotification;
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
        $admin = User::updateOrCreate([

            'email' => 'admin@admin.com',
        ], [
            'name' => 'Admin',
            'password' => bcrypt('p@ssw0rd'),
        ]);
        setPermissionsTeamId(0);
        $admin->assignRole('Super-admin');
        $admin->notifyNow(new PasswordChangeNotification());
        setPermissionsTeamId(null);

    }
}
