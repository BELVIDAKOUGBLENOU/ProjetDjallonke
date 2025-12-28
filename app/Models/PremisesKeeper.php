<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PremisesKeeper extends Model
{
    /** @use HasFactory<\Database\Factories\PremisesKeeperFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'premises_id',
        'person_id',
        'start_date',
        'end_date'
        ,
        'uid',
        'version'
    ];

    public function premise()
    {
        return $this->belongsTo(Premise::class, 'premises_id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
