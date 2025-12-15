<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    /** @use HasFactory<\Database\Factories\VillageFactory> */
    use HasFactory;

    protected $fillable = ['name', 'local_code', 'sub_district_id'];

    public function subDistrict()
    {
        return $this->belongsTo(SubDistrict::class);
    }

    public function premises()
    {
        return $this->hasMany(Premise::class);
    }
}
