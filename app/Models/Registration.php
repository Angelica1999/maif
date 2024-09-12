<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'user_registration';
    protected $guarded = array();

    public function facility(){
        return $this->belongsTo(Facility::class, 'identity_type', 'id');
    }

    public function proponent(){
      return $this->belongsTo(Proponent::class, 'identity_type', 'id');
    }
    
}

