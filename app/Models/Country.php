<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Country
 *
 * @property $id
 * @property $name
 * @property $code_iso
 * @property $created_at
 * @property $updated_at
 * @property Community[] $communities
 * @property District[] $districts
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Country extends Model
{
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'code_iso', 'emoji', 'is_active'];

    /**
     * Scope a query to filter by a generic search term across string columns.
     * Replace fields as needed after generation.
     */
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

    public static function getTableName()
    {
        return (new self)->getTable();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function communities()
    {
        return $this->hasMany(\App\Models\Community::class, 'country_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function districts()
    {
        return $this->hasMany(\App\Models\District::class, 'country_id', 'id');
    }


}
