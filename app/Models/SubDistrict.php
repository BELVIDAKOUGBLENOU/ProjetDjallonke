<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubDistrict extends Model
{
    /** @use HasFactory<\Database\Factories\SubDistrictFactory> */
    use HasFactory;

    protected $fillable = ['name', 'district_id'];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public static function getTableName()
    {
        return (new self)->getTable();
    }

    public function villages()
    {
        return $this->hasMany(Village::class);
    }
}
