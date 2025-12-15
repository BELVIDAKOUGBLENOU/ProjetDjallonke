<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /** @use HasFactory<\Database\Factories\CountryFactory> */
    use HasFactory;

    protected $fillable = ['name', 'code_iso'];

    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function communities()
    {
        return $this->hasMany(Community::class);
    }
}
