<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonRole extends Model
{
    /** @use HasFactory<\Database\Factories\PersonRoleFactory> */
    use HasFactory;
    use SoftDeletes;

    const ROLE_TYPES = ['OWNER', 'DEALER', 'TRANSPORTER'];

    protected $fillable = [
        'person_id',
        'animal_id',
        'role_type',
        'uid',
        'version'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }
}
