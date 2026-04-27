<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransmittalPatients extends Model
{
    use HasFactory;

    protected $table = 'transmittal_patients';

    public function patient(){
        return $this->belongsTo(Patients::class,'patient_id', 'id');
    }
}
