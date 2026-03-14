<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetCommunityContextFrontend;
use App\Http\Resources\UserRemoteResource;
use App\Models\User;
use App\Services\IamM2M;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserRemoteController extends Controller
{
    public function __construct()
    {

        $this->middleware(SetCommunityContextFrontend::class);
        // Permissions
        $this->middleware('permission:list users')->only('index');
        $this->middleware('permission:create users')->only('store');
        $this->middleware('permission:view users')->only('show');
        $this->middleware('permission:update users')->only('update');
        $this->middleware('permission:delete users')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $users = User::query();

        if (!auth()->user()->hasRole('Super-admin')) {
            $users = $users->where('entreprise_id', auth()->user()->entreprise_id);
        }

        $users = $users
            ->with(['roles', 'roles.permissions', 'rolesCustom']) // Basic eager load
            // Implement search scope if User model has it, or manual search
            ->when($q, function ($query, $term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends(['q' => $q]);

        return UserRemoteResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        // --- Étape 1 : Synchronisation avec le service IAM ---
        $uid = User::where('email', $validated['email'])->value('uid') ?? (string) Str::uuid();

        $iamResponse = IamM2M::addNewUser([
            'uid'   => $uid,
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (! $iamResponse) {
            return response()->json(['message' => 'Impossible de créer l\'utilisateur dans le service IAM.'], 500);
        }

        // --- Étape 2 : Upsert de l'utilisateur local par uid ---
        $user = User::updateOrCreate(
            ['uid' => $iamResponse['uid']],
            [
                'name'     => $iamResponse['name'],
                'email'    => $iamResponse['email'],
                'phone'    => $validated['phone'] ?? null,
                'password' => Hash::make(Str::random(32)),
            ]
        );

        // --- Étape 3 : Assignation des rôles du créateur ---
        DB::transaction(function () use ($user) {
            $creatorRoleNames = auth()->user()->getRoleNames();
            foreach ($creatorRoleNames as $roleName) {
                if ($roleName) {
                    $user->assignRole($roleName);
                }
            }
        });

        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'user'    => new UserRemoteResource($user),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::where('id', $id)->with(['rolesCustom'])->first();
        Log::info('User id: ' . $user->id . ' received id : ' . $id . ' has roles: ', ['roles' => $user->rolesCustom->pluck('name')]);

        return new UserRemoteResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::where('id', $id)->first();

        if (!auth()->user()->hasRole('Super-admin')) {
            if ($user->entreprise_id !== auth()->user()->entreprise_id) {
                abort(403, 'Unauthorized scope access.');
            }
        }

        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 403);
        }

        try {
            $user->delete();

            return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression.'], 500);
        }
    }
}
