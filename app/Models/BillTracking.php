<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillTracking extends Model
{
    use HasFactory;

    protected $table = 'bills_tracking';

    public function user(){
        return $this->belongsTo(OnlineUser::class, 'released_by', 'username');
    }

    public function dtr_user(){
        return $this->belongsTo(User::class, 'released_by', 'userid');
    }

    public function accepted_dtr(){
        return $this->belongsTo(User::class, 'accepted_by', 'userid');
    }

    public function accepted_gl(){
        return $this->belongsTo(OnlineUser::class, 'accepted_by', 'username');
    }
}
