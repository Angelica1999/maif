<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProponentInfo extends Model
{
    use HasFactory;

    protected $table = 'proponent_info';
    protected $guarded = array();

    public function facility() {
        return $this->belongsTo(Facility::class, 'facility_id','id');
    }
    public function proponent() {
        return $this->belongsTo(Proponent::class, 'proponent_id','id');
    }
    public function main_pro() {
        return $this->belongsTo(Proponent::class, 'main_proponent','id');
    }
    public function addfacilityinfo()
    {
        return $this->belongsTo(AddFacilityInfo::class);
    }
    public function fundsource() {
        return $this->belongsTo(Fundsource::class,'fundsource_id','id');
    }
    public function extension()
    {
        return $this->belongsTo(Extension::class);
    }
    public function dv3_funds(){
        return $this->hasMany(Dv3Fundsource::class, 'info_id', 'id');
    }
}
