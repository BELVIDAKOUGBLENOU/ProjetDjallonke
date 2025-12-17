<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public $bypassRoleForNonSuperAdmin = ['Super-admin'];
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $user = auth()->user();

            if (!($user->hasRole('Super-admin'))) {
                abort(403, 'Unauthorized action.');
            }

            $roleId = $request->route('role');

            if ($roleId) {
                $role = ($roleId instanceof Role) ? $roleId : Role::findOrFail($roleId);
                if (is_null($role->entreprise_id) && !$user->hasRole('Super-admin')) {
                    abort(403, 'Only Super-admin can manage global roles.');
                } elseif (!is_null($role->entreprise_id) && $user->entreprise_id != $role->entreprise_id && !$user->hasRole('Super-admin')) {
                    abort(403, 'Ceci ne vous concerne pas.');
                }

            }

            return $next($request);
        });
    }
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Role::orderBy('id', 'DESC');

        if (!$user->hasRole('Super-admin')) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('entreprise_id')
                    ->orWhere('entreprise_id', $user->entreprise_id);
            })->whereNotIn('name', $this->bypassRoleForNonSuperAdmin);
        }

        $roles = $query->paginate(10);
        return view('role.index', compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function create(): View
    {
        return view('role.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $this->validate($request, [
            'name' => [
                'required',
                'unique:roles,name',
            ],
        ]);

        $data = ['name' => $request->input('name')];

        if (!$user->hasRole('Super-admin')) {
            $data['entreprise_id'] = $user->entreprise_id;
        } else {
            $data['entreprise_id'] = null;
        }

        Role::create($data);

        return redirect()->route('roles.index')
            ->with('success', 'Rôle créé avec succès');
    }

    public function show($id): View
    {
        $role = Role::find($id);
        //permissions du role Administrateur
        $permissions = Role::findByName('Administrateur')->permissions;
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('role.show', compact('role', 'permissions', 'rolePermissions'));
    }

    public function edit($id): View
    {
        $role = Role::find($id);
        return view('role.edit', compact('role'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $role = Role::find($id);

        $this->validate($request, [
            'name' => [
                'required',
                "unique:roles,name," . $role->id,
            ],
        ]);

        $role->name = $request->input('name');
        $role->save();

        return redirect()->route('roles.index')
            ->with('success', 'Rôle mis à jour avec succès');
    }

    public function destroy($id): RedirectResponse
    {
        $role = Role::find($id);
        if (in_array($role->name, ['Super-admin', 'Administrateur'])) {
            return redirect()->route('roles.index')
                ->with('error', 'Ce rôle ne peut pas être supprimé.');
        }
        DB::table("roles")->where('id', $id)->delete();
        return redirect()->route('roles.index')
            ->with('success', 'Rôle supprimé avec succès');
    }

    public function updatePermissions(Request $request, $id): RedirectResponse
    {
        $role = Role::find($id);
        // vérifier que les permissions existent et sont du role Administrateur
        // $permissions = ;
        // $permissionIds = $permissions->pluck('id')->toArray();
        if (auth()->user()->hasRole('Super-admin')) {
            request()->validate([
                'permissions.*' => [
                    'nullable',
                    'exists:permissions,name',
                ],
            ]);
        } elseif (isAdmin()) {
            request()->validate([
                'permissions.*' => [
                    'nullable',
                    Rule::in(Role::findByName('Administrateur')->permissions->pluck('name')->toArray()),
                ],
            ]);
        } else {
            abort(403, 'Unauthorized action.');
        }
        $role->syncPermissions($request->input('permissions') ?? []);

        return redirect()->route('roles.show', $id)
            ->with('success', 'Permissions mises à jour avec succès');
    }
}
