<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dv2 extends Model
{
    use HasFactory;

    protected $table = 'dv2';
    protected $pimaryKey = 'id';

    public function user(){
        return $this->belongsTo(User::class,'created_by', 'userid');
    }
}
