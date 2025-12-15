<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\ErrorSolutions\Support\Laravel\Composer\ComposerClassMap;
use Spatie\Permission\Models\Permission;

class PermissionGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate permissions for each Eloquent model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //

        $actions = ['list', 'view', 'create', 'update', 'delete'];
        $classMap = new ComposerClassMap;
        $list = $classMap->listClasses();
        // drop all permissions
        // Permission::query()->delete();
        $byPass = [

        ];

        foreach ($list as $class => $path) {

            if (Str::startsWith($class, 'App\Models\\')) {
                if (in_array($class, $byPass)) {
                    $this->info($class);

                    continue;
                }
                $model = (new $class)->getTable();

                foreach ($actions as $action) {
                    Permission::firstOrCreate(['name' => "$action $model"]);
                }
                // $role = Role::firstOrCreate(['name' => "$model admin"]);
                // $role->syncPermissions(Permission::where('name', 'like', "$model %")->pluck('id'));

            }
        }
        $this->info('Permissions générées.');
    }
}
