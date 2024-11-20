<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplementalFunds extends Model
{
    use HasFactory;

    protected $table='supplemental_funds';

    public function user(){
        return $this->belongsTo(User::class, 'added_by', 'userid');
    }
}
