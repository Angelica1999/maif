<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dv extends Model
{
    protected $table = 'Dv'; // Adjust the table name as needed
    protected $fillable = [
        'date',
        'payee',
        'address',
        'month_year_from',
        'month_year_to',
        'saa_number',
        'amount1',
        'amount2',
        'amount3',
        'total_amount',
        'deduction1',
        'deduction2',
        'deduction_amount1',
        'deduction_amount2',
        'total_deduction_amount',
        'overall_total_amount',
    ];

    protected $casts = [
        'date' => 'datetime',
        'month_year_from' => 'datetime',
        'month_year_to' => 'datetime',
    ];

    public function facility() {   
        return $this->belongsTo(Facility::class, 'facility_id','id');
    }

    
    public function proponents() {   
        return $this->hasMany(Proponent::class);
    }

    public function addFacilityInfo() {      
        return $this->belongsTo(AddFacilityInfo::class, 'id', 'facility_id');
    }
    public function fundsource() {
        return $this->belongsTo(Fundsource::class, 'saa_number', 'id'); # payee is the foreign Id of fundsource it would be fundsource_id
    }
}
