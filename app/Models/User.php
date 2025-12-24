<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'person_id',
        'fcm_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
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
            // Example: 'name', 'title', 'slug'
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
    public function createdCommunities()
    {
        return $this->hasMany(Community::class, 'created_by');
    }

    public function communityMemberships()
    {
        return $this->hasMany(CommunityMembership::class);
    }

    public function communities()
    {
        return $this->belongsToMany(Community::class, 'community_memberships')
            ->withPivot('role', 'added_at')
            ->withTimestamps();
    }
    function mobileAppCommunities()
    {
        $communities = $this->communities()
            ->whereIn('role', ['FARMER', 'TECHNICIAN']);
        return $communities;
    }

    public function createdPremises()
    {
        return $this->hasMany(Premise::class, 'created_by');
    }
    function communityPermissions($community_id)
    {
        setPermissionsTeamId($community_id);
        $permissions = $this->getAllPermissions();
        setPermissionsTeamId(null);
        return $permissions;
    }

    public function createdAnimals()
    {
        return $this->hasMany(Animal::class, 'created_by');
    }

    public function createdEvents()
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function confirmedEvents()
    {
        return $this->hasMany(Event::class, 'confirmed_by');
    }

    public function createdPerformanceRecords()
    {
        return $this->hasMany(PerformanceRecord::class, 'created_by');
    }
}
