<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    /** @use HasFactory<\Database\Factories\VillageFactory> */
    use HasFactory;

    protected $fillable = ['name', 'local_code', 'sub_district_id'];

    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }
        $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $term) . '%';
        // Adjust searchable columns after generation if necessary
        $columns = array_filter([
            // Example: 'name', 'title', 'slug'
        ]);
        if (empty($columns)) {
            return $query; // No columns defined; user will customize.
        }
        return $query->where(function ($q) use ($columns, $like) {
            foreach ($columns as $idx => $col) {
                $method = $idx === 0 ? 'where' : 'orWhere';
                $q->$method($col, 'like', $like);
            }
        });
    }
    public function subDistrict()
    {
        return $this->belongsTo(SubDistrict::class);
    }

    public static function getTableName()
    {
        return (new self)->getTable();
    }

    public function premises()
    {
        return $this->hasMany(Premise::class);
    }
}
