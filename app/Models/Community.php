<?php

namespace App\Models;

use App\Services\IamM2M;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
            'name',
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
        $role = $data['role'];
        $name = $data['name'];
        $email = $data['email'];
        $phone = $data['phone'] ?? null;
        $nationalId = $data['nationalId'] ?? null;

        // --- Étape 1 : Synchronisation avec le service IAM ---
        // Récupère l'uid existant ou en génère un nouveau pour cet email
        $uid = User::where('email', $email)->value('uid') ?? (string) Str::uuid();

        $iamResponse = IamM2M::addNewUser([
            'uid' => $uid,
            'name' => $name,
            'email' => $email,
        ]);

        if (!$iamResponse) {
            throw new \Exception('Failed to register user in IAM service.');
        }
        Log::info('response ', ["iam-resp" => $iamResponse, 'uid' => $iamResponse]);
        // --- Étape 2 : Upsert de l'utilisateur local par uid ---
        $user = User::updateOrCreate(
            ['uid' => $iamResponse['uid']],
            ['name' => $iamResponse['name'], 'email' => $iamResponse['email'], 'password' => Hash::make(Str::random(32))]
        );

        // --- Étape 3 : Transaction locale (person, membership, rôles) ---
        return DB::transaction(function () use ($communityId, $role, $name, $phone, $nationalId, $user) {

            // Handle Farmer Logic
            if ($role === 'FARMER') {
                $person = null;

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

                if ($person && !$user->person_id) {
                    $user->update(['person_id' => $person->id]);
                }
            }

            // Vérifier que ce n'est pas un super-admin global
            $temp = getPermissionsTeamId();
            setPermissionsTeamId(0);
            $isSuperAdmin = count($user->getRoleNames()) > 0;
            setPermissionsTeamId($temp);

            if ($isSuperAdmin) {
                throw new \Exception('Super-admin cannot be added as a member of a community.');
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
            foreach ($user->getRoleNames() as $existingRole) {
                $user->removeRole($existingRole);
            }
            $user->assignRole($role);

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
