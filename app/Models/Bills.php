<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bills extends Model
{
    use HasFactory;

    protected $table = 'bills';
    
    public function extension(){
        return $this->hasMany(BillExtension::class, 'bills_id', 'id');
    }

    public function user(){
        return $this->belongsTo(OnlineUser::class, 'created_by', 'username');
    }

    public function tracking(){
        return $this->hasMany(BillTracking::class, 'bills_id', 'id');
    }
}
