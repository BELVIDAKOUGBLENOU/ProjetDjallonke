<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonRole extends Model
{
    /** @use HasFactory<\Database\Factories\PersonRoleFactory> */
    use HasFactory;

    protected $fillable = ['person_id', 'animal_id', 'role_type'];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }
}
