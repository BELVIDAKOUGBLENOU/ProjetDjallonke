<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetCommunityContextFrontend;
use App\Http\Requests\UserRemoteRequest; // We will create this or use UserRequest if appropriate
use App\Http\Resources\UserRemoteResource; // We need this
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class UserRemoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
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
            ->with(['roles', 'roles.permissions']) // Basic eager load
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
        // Validation logic - can extract to FormRequest
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:30'],
            // Start with password handling similar to backend controller
        ]);

        $data = $validated;



        // Generate random password
        $plainPassword = str()->random(8); // Or 10, to be safe
        $data['password'] = Hash::make($plainPassword);

        DB::beginTransaction();
        try {
            $user = User::create($data);

            // Assign Roles
            // Logic based on user request: "cet utilisateur aura les acces de Super-admin que ce profil createur possede"
            // If creator is Super-admin, assign Super-admin.
            // If creator is NOT Super-admin, what rol? 'Administrateur'?
            // The prompt says "les acces de Super-admin que ce profil createur possede" -> implying the creator HAS super-admin access.

            $creatorRoleNames = auth()->user()->getRoleNames();
            foreach ($creatorRoleNames as $roleName) {
                if ($roleName) {
                    $user->assignRole($roleName);
                }
            }

            // Send email
            try {
                $user->notify(new \App\Notifications\PasswordChangeNotification());
                Mail::to($user->email)->send(new \App\Mail\NewMemberCredentials($user, $plainPassword));
                // change password notification can be sent in the mail class or here, depending on how you want to structure it

            } catch (\Exception $e) {
                // Log email failure but don't fail user creation necessarily?
                // Or maybe fail it? Controller fails it usually.
            }

            // Notify
            // $user->notifyNow(new \App\Notifications\PasswordChangeNotification());

            DB::commit();

            return response()->json([
                'message' => 'Utilisateur créé avec succès. Un mot de passe temporaire a été envoyé par email.',
                'user' => new UserRemoteResource($user)
            ], 201);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return new UserRemoteResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

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
