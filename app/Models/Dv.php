<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dv extends Model
{
    protected $connection = 'mysql';
    protected $table = 'Dv'; // Adjust the table name as needed
    protected $fillable = [
        'date',
        'facility_id',
        'address',
        'month_year_from',
        'month_year_to',
        'fundsource_id',
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
    public function proponent() {
        return $this->belongsTo(Proponent::class, 'proponent_id','id');
    }
    public function addFacilityInfo() {      
        return $this->belongsTo(AddFacilityInfo::class, 'id', 'facility_id');
    }
    public function fundsource() {
        return $this->belongsTo(Fundsource::class, 'fundsource_id', 'id'); 
    }
    public function user() {
        return $this->belongsTo(User::class, 'created_by', 'userid'); 
    }
    public function master(){
        return $this->belongsTo(TrackingMaster::class, 'route_no','route_no');
    }
    public function release(){
        return $this->belongsTo(Tracking_Releasev2::class, 'route_no','route_no');
    }
}
