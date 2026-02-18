<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnimalIdentifier extends Model
{
    /** @use HasFactory<\Database\Factories\AnimalIdentifierFactory> */
    use HasFactory;
    use SoftDeletes;

    const TYPES = ['VISUAL', 'BRAND', 'TATTOO', 'RFID_EAR_TAG', 'RFID_INJECTABLE', 'RFID_BOLUS'];

    protected $fillable = ['animal_id', 'type', 'code', 'active', 'uid', 'version'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }
}
