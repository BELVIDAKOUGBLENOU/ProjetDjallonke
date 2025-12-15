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
