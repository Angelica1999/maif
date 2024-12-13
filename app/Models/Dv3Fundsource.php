<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dv3Fundsource extends Model{

    use HasFactory;

    protected $table = 'dv3_fundsources';
    protected $primaryKey = 'id';

    public function proponentInfo(){
        return $this->belongsTo(ProponentInfo::class, 'info_id', 'id');
    }
    
    public function dv3(){
        return $this->belongsTo(Dv3::class, 'route_no', 'route_no');
    }

    public function fundsource(){
        return $this->belongsTo(Fundsource::class, 'fundsource_id', 'id');
    }
}