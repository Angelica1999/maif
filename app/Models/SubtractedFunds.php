<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubtractedFunds extends Model
{
    use HasFactory;

    protected $table='subtracted_funds';

    public function user(){
        return $this->belongsTo(User::class, 'subtracted_by', 'userid');
    }
}
