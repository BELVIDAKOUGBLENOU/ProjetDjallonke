<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetCommunityContextFrontend;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleRemoteController extends Controller
{
    public $bypassRoleForNonSuperAdmin = ['Super-admin'];

    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(SetCommunityContextFrontend::class);
        $this->middleware('permission:list roles')->only('index');
        $this->middleware('permission:create roles')->only('store');
        $this->middleware('permission:view roles')->only('show');
        $this->middleware('permission:update roles')->only('update');
        $this->middleware('permission:delete roles')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $q = $request->string('q')->toString();
        $query = Role::orderBy('id', 'DESC');

        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }

        $roles = $query->paginate(10)->appends(['q' => $q]);

        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        $data = [
            'name' => $request->input('name'),
            'guard_name' => 'web' // Force web guard for all new roles
        ];
        $origin = getPermissionsTeamId();
        setPermissionsTeamId(null); // Ensure global context for role creation
        $role = Role::create($data);
        setPermissionsTeamId($origin); // Restore original context

        return new RoleResource($role);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $roleId)
    {
        $role = Role::findOrFail($roleId);

        // Permissions logic from original controller
        try {
            $adminRole = Role::findByName('Administrateur');
            $allPermissions = $adminRole ? $adminRole->permissions : \Spatie\Permission\Models\Permission::all();
        } catch (\Exception $e) {
            $allPermissions = \Spatie\Permission\Models\Permission::all();
        }

        $rolePermissions = DB::table("role_has_permissions")
            ->where("role_has_permissions.role_id", $role->id)
            ->pluck('role_has_permissions.permission_id')
            ->map(function ($id) {
                return (string) $id;
            }) // ensure string for JS comparison if needed
            ->all();

        return response()->json([
            'data' => new RoleResource($role),
            'permissions' => $allPermissions->map(function ($p) {
                return ['id' => (string) $p->id, 'name' => $p->name];
            }),
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function updatePermissions(Request $request, string $roleId)
    {
        $role = Role::findOrFail($roleId);

        // Ensure the role uses the 'web' guard, fixing any existing roles created via API (sanctum guard)
        if ($role->guard_name !== 'web') {
            $role->guard_name = 'web';
            $role->save();
        }

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        // Explicitly use the 'web' guard when fetching permissions
        $permissions = \Spatie\Permission\Models\Permission::whereIn('name', $request->input('permissions', []))
            ->where('guard_name', 'web')
            ->get();

        $role->syncPermissions($permissions);

        return response()->json([
            'message' => 'Permissions updated successfully.',
            'role' => new RoleResource($role)
        ]);
    }

    public function update(Request $request, string $roleId)
    {
        $role = Role::findOrFail($roleId);

        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
        ]);

        $role->update(['name' => $request->input('name')]);

        // Handle permissions update if passed? Usually handled in separate endpoint or here.
        // For now, mirroring basic CRUD.

        return new RoleResource($role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $roleId)
    {
        $role = Role::findOrFail($roleId);

        // Authorization check logic copied from RoleController middleware roughly
        $user = auth()->user();
        if (in_array($role->name, ['Super-admin', 'Administrateur'])) {
            return response()->json(['message' => 'Cannot delete this role.'], 403);
        }

        // Additional ownership checks if strict replication needed

        $role->delete();

        return response()->noContent();
    }
}
