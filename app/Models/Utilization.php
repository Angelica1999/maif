<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Utilization extends Model
{
    use HasFactory;
    use Sortable;

    protected $table = 'utilization';
    protected $guarded = array();

    public $sortable = [
        'id',
        'facility.name', 
        'proponentdata.proponent'
    ];

    public function proponentdata(){
        return $this->belongsTo(Proponent::class, 'proponent_id', 'id');
    }
    public function dv(){
        return $this->belongsTo(Dv::class, 'div_id', 'route_no');
    }
    public function dv3(){
        return $this->belongsTo(Dv3::class, 'div_id', 'route_no');
    }
    public function newDv(){
        return $this->belongsTo(NewDV::class, 'div_id', 'route_no');
    }
    public function fundSourcedata(){
        return $this->belongsTo(FundSource::class, 'fundsource_id', 'id');
    }
    public function facilitydata() {   
        return $this->belongsTo(Facility::class, 'facility_id','id');
    }
    public function user() {   
        return $this->belongsTo(User::class, 'created_by','userid');
    }
    public function user_budget() {   
        return $this->belongsTo(User::class, 'obligated_by','userid');
    }
    public function transfer() {   
        return $this->belongsTo(Transfer::class, 'transfer_id','id');
    }
    public function infoData() {   
        return $this->belongsTo(ProponentInfo::class, 'proponentinfo_id','id');
    }
    public function saaData() {   
        return $this->belongsTo(Fundsource::class, 'fundsource_id','id');
    }
}
