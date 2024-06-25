<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dv3 extends Model{
    use HasFactory;

    protected $table = 'dv3';
    protected $primaryKey = 'id';

    public function extension(){
        return $this->hasMany(Dv3Fundsource::class, 'route_no', 'route_no');
    }

    public function facility(){
        return $this->belongsTo(Facility::class, 'facility_id', 'id');
    }

    public function proponent_info(){
        return $this->hasMany(ProponentInfo::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'created_by', 'userid');
    }
    public function master(){
        return $this->belongsTo(TrackingMaster::class, 'route_no','route_no');
    }
}