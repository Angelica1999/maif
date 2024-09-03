<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreDV extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'pre_dv';

    public function user(){
        return $this->belongsTo(User::class, 'created_by', 'userid');
    }

    public function facility(){
        return $this->belongsTo(Facility::class, 'facility_id', 'id');
    }

    public function extension(){
        return $this->hasMany(PreDVExtension::class, 'pre_dv_id', 'id');
    }

    public function new_dv(){
        return $this->belongsTo(NewDV::class, 'id', 'predv_id');
    }

}
