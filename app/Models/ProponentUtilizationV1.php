<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProponentUtilizationV1 extends Model
{
    use HasFactory;

    protected $table = 'pro_utilization_v1';

    public function patient(){
        return $this->belongsTo(Patients::class, 'patient_id', 'id');
    }

    public function proponent(){
        return $this->belongsTo(Proponent::class, 'proponent_id', 'id');
    }

}
