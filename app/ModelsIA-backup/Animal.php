<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    /** @use HasFactory<\Database\Factories\AnimalFactory> */
    use HasFactory;

    protected $fillable = [
        'uid',
        'created_by',
        'premises_id',
        'species',
        'sex',
        'birth_date',
        'life_status',
    ];

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
