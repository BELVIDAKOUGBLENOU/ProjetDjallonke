<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $user = auth()->user();

            if (!($user->hasRole('Super-admin'))) {
                abort(403, 'Unauthorized action.');
            }

            $userId = $request->route('user');

            if ($userId) {
                $userRoute = ($userId instanceof User) ? $userId : User::findOrFail($userId);


                if (is_null($userRoute->entreprise_id) && !$user->hasRole('Super-admin')) {
                    abort(403, 'Only Super-admin can manage global roles.');
                } elseif (!is_null($userRoute->entreprise_id) && $user->entreprise_id != $userRoute->entreprise_id && !$user->hasRole('Super-admin')) {
                    abort(403, 'Ceci ne vous concerne pas.');
                }

            }

            return $next($request);
        })->except('stopImpersonate');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        $users = User::query();
        if (!auth()->user()->hasRole('Super-admin')) {
            $users = $users->where('entreprise_id', auth()->user()->entreprise_id);
        }

        $users = $users
            ->with('roles')
            ->search($q)
            ->orderByDesc('created_at')
            ->paginate()
            ->appends(['q' => $q]);

        return view('user.index', compact('users', 'q'))
            ->with('i', ($request->input('page', 1) - 1) * $users->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = new User();
        $roles = Role::whereIn("name", ['Super-admin'])->get();
        $permissions = Permission::all();
        if (!auth()->user()->hasRole('Super-admin')) {
            $roles = Role::whereNotIn("name", ['Super-admin'])->get();
            $permissions = Role::findByName('Administrateur', 'web')->permissions;
        }

        return view('user.create', compact('user', 'roles', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Générer un mot de passe aléatoire de 8 caractères
        $plainPassword = str()->random(8);
        $data['password'] = Hash::make($plainPassword);
        DB::beginTransaction();
        try {
            //code...
            if (User::where('email', $data['email'])->exists()) {
                $user = User::where('email', $data['email'])->first();
            } else {
                $user = User::create($data);
            }

            if ($request->has('roles')) {
                setPermissionsTeamId(0);
                if (!(auth()->user()->getRoleNames()->count() > 0)) {
                    setPermissionsTeamId(null);
                }
                $user->syncRoles($request->roles);
            }

            if ($request->has('permissions')) {
                $user->syncPermissions($request->permissions);
            }

            // Envoyer le mot de passe par email
            Mail::to($user->email)->send(new \App\Mail\NewMemberCredentials($user, $plainPassword));

            // Envoyer une notification in-app pour recommander le changement de mot de passe
            $user->notifyNow(new \App\Notifications\PasswordChangeNotification());
            DB::commit();
            return Redirect::route('users.index')
                ->with('success', 'Utilisateur créé avec succès. Un mot de passe temporaire a été envoyé par email.');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return Redirect::route('users.index')
                ->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $user = User::find($id);

        return view('user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $user = User::find($id);
        $roles = Role::all();
        $permissions = Permission::all();

        return view('user.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        } else {
            $user->syncPermissions([]);
        }

        return Redirect::route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès');
    }

    public function destroy($id): RedirectResponse
    {
        User::find($id)->delete();

        return Redirect::route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès');
    }

    public function impersonate($id)
    {
        if (!auth()->user()->hasRole('Super-admin')) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas vous imiter vous-même.');
        }

        session()->put('impersonate', auth()->id());
        auth()->login($user);

        return redirect()->route('home')->with('success', "Vous êtes maintenant connecté en tant que {$user->name}");
    }

    public function stopImpersonate()
    {
        if (session()->has('impersonate')) {
            auth()->loginUsingId(session('impersonate'));
            session()->forget('impersonate');
            return redirect()->route('users.index')->with('success', 'Retour au compte administrateur');
        }
        return redirect()->route('home');
    }
}
