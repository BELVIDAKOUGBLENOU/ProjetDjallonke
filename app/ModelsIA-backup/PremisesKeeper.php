<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PremisesKeeper extends Model
{
    /** @use HasFactory<\Database\Factories\PremisesKeeperFactory> */
    use HasFactory;

    protected $fillable = ['premises_id', 'person_id', 'start_date', 'end_date'];

    public function premise()
    {
        return $this->belongsTo(Premise::class, 'premises_id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
