<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transmittal extends Model
{
    use HasFactory;

    protected $table ='transmittal';

    public function details(){
        return $this->hasMany(TransmittalDetails::class, 'transmittal_id', 'id');
    }

    public function user(){
        return $this->belongsTo(OnlineUser::class, 'created_by', 'username');
    }

    public function facility(){
        return $this->belongsTo(Facility::class, 'facility_id', 'id');
    }

    public function tracking(){
        return $this->hasMany(TransmittalTracking::class, 'transmittal_id', 'id');
    }
}
