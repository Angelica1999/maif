<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewDV extends Model
{
    use HasFactory;

    protected $table = 'new_dv';

    public function user_created(){
        return $this->belongsTo(User::class, 'created_by', 'userid');
    }
    public function user_obligated(){
        return $this->belongsTo(User::class, 'obligated_by', 'userid');
    }
    public function user_paid(){
        return $this->belongsTo(User::class, 'paid_by', 'userid');
    }
    public function dts(){
        return $this->belongsTo(TrackingMaster::class, 'route_no', 'route_no');
    }
    public function details(){
        return $this->hasMany(TrackingDetails::class, 'route_no', 'route_no');
    }
}
