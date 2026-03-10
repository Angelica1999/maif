<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'user_id',
        'email',
        'type',
        'recommendation',
        'status',
        'remarks',
        'evaluated_by',
    ];

    /** The original submission this reply belongs to. */
    public function parent()
    {
        return $this->belongsTo(Recommendation::class, 'parent_id');
    }

    /** All user replies to this submission. */
    public function replies()
    {
        return $this->hasMany(Recommendation::class, 'parent_id')->oldest();
    }

    /** The user who submitted this. */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'userid');
    }

    /** Scope: only original submissions (not replies). */
    public function scopeOriginal($query)
    {
        return $query->whereNull('parent_id');
    }
}