<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityMembership extends Model
{
    /** @use HasFactory<\Database\Factories\CommunityMembershipFactory> */
    use HasFactory;

    protected $fillable = ['community_id', 'user_id', 'role', 'added_at'];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
