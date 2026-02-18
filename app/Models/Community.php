<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\NewMemberCredentials;

class Community extends Model
{
    /** @use HasFactory<\Database\Factories\CommunityFactory> */
    use HasFactory;

    protected $fillable = ['name', 'creation_date', 'created_by', 'country_id'];

    public static function getTableName()
    {
        return (new self)->getTable();
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'community_memberships');
    }
    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }
        $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $term) . '%';
        // Adjust searchable columns after generation if necessary
        $columns = array_filter([
            'name'
        ]);
        if (empty($columns)) {
            return $query; // No columns defined; user will customize.
        }
        return $query->where(function ($q) use ($columns, $like) {
            foreach ($columns as $idx => $col) {
                $method = $idx === 0 ? 'where' : 'orWhere';
                $q->$method($col, 'like', $like);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function memberships()
    {
        return $this->hasMany(CommunityMembership::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'community_memberships')
            ->withPivot('role', 'added_at')
            ->withTimestamps();
    }

    public function premises()
    {
        return $this->hasMany(Premise::class);
    }

    public static function addMember(int $communityId, array $data)
    {
        return DB::transaction(function () use ($communityId, $data) {
            $role = $data['role'];
            $name = $data['name'];
            $email = $data['email'];

            $person = null;

            // Handle Farmer Logic
            if ($role === 'FARMER') {
                $phone = $data['phone'] ?? null;
                $nationalId = $data['nationalId'] ?? null;

                if ($phone || $nationalId) {
                    $query = Person::query();
                    if ($phone) {
                        $query->where('phone', $phone);
                    }
                    if ($nationalId) {
                        $query->orWhere('nationalId', $nationalId);
                    }
                    $person = $query->first();
                }

                if (!$person) {
                    $person = Person::create([
                        'name' => $name,
                        'phone' => $phone,
                        'nationalId' => $nationalId,
                    ]);
                }
            }

            // Handle User Logic
            $user = User::where('email', $email)->first();
            $password = null;
            $isNewUser = false;

            if (!$user) {
                $password = Str::random(8);
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'person_id' => $person ? $person->id : null,
                ]);
                $isNewUser = true;
            } else {
                // If user exists and we found/created a person (Farmer), link them if not linked
                if ($person && !$user->person_id) {
                    $user->update(['person_id' => $person->id]);
                }
            }

            // Create Membership
            $exists = CommunityMembership::where('community_id', $communityId)
                ->where('user_id', $user->id)
                ->exists();

            if (!$exists) {
                CommunityMembership::create([
                    'community_id' => $communityId,
                    'user_id' => $user->id,
                    'role' => $role,
                    'added_at' => now(),
                ]);
            }
            // Assign Role with Team Context
            setPermissionsTeamId($communityId);
            //vérifier s'il a déjà un role dans cette communauté
            // dump("role actuelle", $user->getRoleNames());
            if (count($user->getRoleNames()) != 0) {
                foreach ($user->getRoleNames() as $existingRole) {
                    // dump("suppression de  : " . $existingRole);
                    $user->removeRole($existingRole);
                }
            }
            // dump("role actuelle", $user->getRoleNames());
            // dd($role);
            $user->assignRole($role);



            // Send Email if new user
            if ($isNewUser && $password) {
                Mail::to($user->email)->send(new NewMemberCredentials($user, $password, Community::find($communityId)));
                $user->notifyNow(new \App\Notifications\PasswordChangeNotification());
            }


            return $user;
        });
    }

    public static function removeMember(int $communityId, int $userId)
    {
        $memberShip = CommunityMembership::where('community_id', $communityId)
            ->where('user_id', $userId)
            ->firstOrFail();
        setPermissionsTeamId($communityId);
        $memberShip->user->removeRole($memberShip->role);
        setPermissionsTeamId(null);
        return $memberShip->delete();
    }
}
