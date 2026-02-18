<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Animal extends Model
{
    /** @use HasFactory<\Database\Factories\AnimalFactory> */
    use HasFactory;
    use SoftDeletes;

    const SPECIES = ['OVINE', 'CAPRINE'];
    const SEXES = ['M', 'F'];
    const LIFE_STATUSES = ['ALIVE', 'DEAD', 'SOLD'];

    protected $fillable = [

        'created_by',
        'premises_id',
        'species',
        'sex',
        'birth_date',
        'life_status',
        'uid',
        'version'
    ];
    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }
        $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $term) . '%';

        return $query->where(function ($q) use ($like) {
            $q->where('uid', 'like', $like)
                ->orWhere('birth_date', 'like', $like)
                ->orWhereHas('identifiers', function ($query) use ($like) {
                    $query->where('code', 'like', $like);
                });
        });
    }
    public static function getTableName()
    {
        return (new self)->getTable();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function premise()
    {
        return $this->belongsTo(Premise::class, 'premises_id');
    }

    public function identifiers()
    {
        return $this->hasMany(AnimalIdentifier::class);
    }

    public function personRoles()
    {
        return $this->hasMany(PersonRole::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function motherReproductionEvents()
    {
        return $this->hasMany(ReproductionEvent::class, 'mother_id');
    }

    public function fatherReproductionEvents()
    {
        return $this->hasMany(ReproductionEvent::class, 'father_id');
    }

    public function motherBirthEvents()
    {
        return $this->hasMany(BirthEvent::class, 'mother_id');
    }

    public function fatherBirthEvents()
    {
        return $this->hasMany(BirthEvent::class, 'father_id');
    }

    public function performanceRecords()
    {
        return $this->hasMany(PerformanceRecord::class);
    }
}
