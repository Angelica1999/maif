<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreDVExtension extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'pre_dv_extension';

    public function controls(){
        return $this->hasMany(PreDVControl::class, 'predv_extension_id', 'id');
    }

    public function saas(){
        return $this->hasMany(PreDVSAA::class, 'predv_extension_id', 'id');
    }

    public function proponent(){
        return $this->belongsTo(Proponent::class, 'proponent_id', 'id');
    }
}
