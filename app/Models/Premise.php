<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Premise extends Model
{
    /** @use HasFactory<\Database\Factories\PremiseFactory> */
    use HasFactory;

    protected $fillable = [
        'village_id',
        'created_by',
        'community_id',
        'code',
        'address',
        'gps_coordinates',
        'type',
        'health_status',
    ];

    public static function getTableName()
    {
        return (new self)->getTable();
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
            'code',
            'address',
            'type',
            'health_status'
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

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function keepers()
    {
        return $this->hasMany(PremisesKeeper::class, 'premises_id');
    }

    public function animals()
    {
        return $this->hasMany(Animal::class, 'premises_id');
    }

    public function movementEventsFrom()
    {
        return $this->hasMany(MovementEvent::class, 'from_premises_id');
    }

    public function movementEventsTo()
    {
        return $this->hasMany(MovementEvent::class, 'to_premises_id');
    }
}
