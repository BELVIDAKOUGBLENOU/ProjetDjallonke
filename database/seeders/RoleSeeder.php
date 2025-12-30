<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Community;
use App\Models\Country;
use App\Models\District;
use App\Models\Person;
use App\Models\Premise;
use App\Models\SubDistrict;
use App\Models\User;
use App\Models\Village;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('permission:generate');
        $adminRole = \Spatie\Permission\Models\Role::updateOrCreate(['name' => 'Super-admin'], [
            'name' => 'Super-admin',
            'guard_name' => 'web',
        ]);
        $adminRole->syncPermissions(Permission::pluck('id')->toArray());
        $permissions = config('permissions', []);
        foreach ($permissions as $value) {
            Permission::updateOrCreate(['name' => $value], [
                'name' => $value,
                'guard_name' => 'web',
            ]);
        }

        $roles = [
            'Administrateur' => [
                User::class => ['list', 'view', 'create', 'update', 'delete'],
                Country::class => ['list', 'view', 'create', 'update', 'delete'],
                District::class => ['list', 'view', 'create', 'update', 'delete'],
                SubDistrict::class => ['list', 'view', 'create', 'update', 'delete'],
                Village::class => ['list', 'view', 'create', 'update', 'delete'],
                Person::class => ['list', 'view', 'create', 'update', 'delete'],
                Community::class => ['list', 'view', 'create', 'update', 'delete', 'add member', 'remove member', 'update member role'],
                Premise::class => ['list', 'view', 'create', 'update', 'delete'],
                Person::class => ['list', 'view', 'create', 'update', 'delete'],
                Animal::class => ['list', 'view', 'create', 'update', 'delete'],
            ],
            'COMMUNITY_ADMIN' => [
                Community::class => ['view', 'add member', 'remove member', 'update member role'],
                Premise::class => ['list', 'view', 'create', 'update', 'delete'],
                Country::class => ['list', 'view'],
                District::class => ['list', 'view'],
                SubDistrict::class => ['list', 'view'],
                Village::class => ['list', 'view'],
                Person::class => ['list', 'view', 'create', 'update'],
                Animal::class => ['list', 'view', 'create', 'update', 'delete'],
            ],
            'FARMER' => [
                Community::class => ['list', 'view',],
                Country::class => ['list', 'view'],
                District::class => ['list', 'view'],
                SubDistrict::class => ['list', 'view'],
                Village::class => ['list', 'view'],
                Person::class => ['list', 'view', 'create', 'update'],
            ],
            'TECHNICIAN' => [
                Community::class => ['list', 'view',],
                Country::class => ['list', 'view'],
                District::class => ['list', 'view'],
                SubDistrict::class => ['list', 'view'],
                Village::class => ['list', 'view'],
                Person::class => ['list', 'view', 'create', 'update'],
                Animal::class => ['list', 'view', 'create', 'update', 'delete'],
                Premise::class => ['list', 'view', 'create', 'update',],

                //evenements
            ],
            'VET' => [
                // Community::class => ['list', 'view',],
                // Country::class => ['list', 'view'],
                // District::class => ['list', 'view'],
                // SubDistrict::class => ['list', 'view'],
                // Village::class => ['list', 'view'],
                // Person::class => ['list', 'view', 'create', 'update'],
            ],
            'RESEARCHER' => [

            ]
        ];

        foreach ($roles as $role => $permArray) {
            $role = \Spatie\Permission\Models\Role::updateOrCreate(['name' => $role], [
                'name' => $role,
                'guard_name' => 'web',
            ]);
            foreach ($permArray as $class => $permissionsList) {
                $table = (new $class)->getTable();
                foreach ($permissionsList as $p) {
                    $pname = $p . ' ' . $table;
                    Permission::updateOrCreate(['name' => $pname], [
                        'name' => $pname,
                        'guard_name' => 'web',
                    ]);
                    $role->givePermissionTo($pname);
                }
            }
        }
    }
}
