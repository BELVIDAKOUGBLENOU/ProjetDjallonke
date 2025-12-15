<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimalIdentifier extends Model
{
    /** @use HasFactory<\Database\Factories\AnimalIdentifierFactory> */
    use HasFactory;

    protected $fillable = ['uid', 'animal_id', 'type', 'code', 'active'];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }
}
