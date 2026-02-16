<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnexB extends Model
{
    use HasFactory;

    protected $table = "annex_b";
    protected $primaryKey = 'patient_id';
    public $incrementing = false;
    
    protected $fillable = [
        'patient_id',
        'opd',
        'senior',
        'phic',
        'pcso',
        'dswd',
        'o_amount',
        'others',
    ];

    protected $casts = [
        'opd' => 'boolean',
        'senior' => 'decimal:2',
        'phic' => 'decimal:2',
        'pcso' => 'decimal:2',
        'dswd' => 'decimal:2',
        'o_amount' => 'decimal:2',
    ];
    
    public function patient(){
        return $this->belongsTo(Patients::class, 'patient_id', 'id');
    }

    public function trans(){
        return $this->belongsTo(TransmittalPatients::class, 'patient_id', 'patient_id');
    }

    public function facility(){
        return $this->belongsTo(Facility::class, 'facility_id', 'id');
    }
}
