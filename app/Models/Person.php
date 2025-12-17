<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    /** @use HasFactory<\Database\Factories\PersonFactory> */
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = ['name', 'address', 'phone', 'nationalId'];

    public static function getTableName()
    {
        return (new self)->getTable();
    }
    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }
        $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $term) . '%';
        // Adjust searchable columns after generation if necessary
        $columns = array_filter([
            'name',
            'address',
            'phone',
            'nationalId'
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

    public function premisesKeepers()
    {
        return $this->hasMany(PremisesKeeper::class);
    }

    public function personRoles()
    {
        return $this->hasMany(PersonRole::class);
    }

    public function transactionPurchases()
    {
        return $this->hasMany(TransactionEvent::class, 'buyer_id');
    }

    public function transactionSales()
    {
        return $this->hasMany(TransactionEvent::class, 'seller_id');
    }
}
