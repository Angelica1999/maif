<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientLogs extends Model
{
    use HasFactory;

    protected $table = 'patient_logs';
    protected $primaryKey = 'id';
    protected $guarded = array();

    public function patient() {
        return $this->belongsTo(Patients::class, 'patient_id','id');
    }

}

