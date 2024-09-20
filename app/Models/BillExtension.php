<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillExtension extends Model
{
    use HasFactory;

    protected $table = 'bills_extension';

    public function proponent(){
        return $this->belongsTo(Proponent::class, 'proponent_id', 'id');
    }
}
