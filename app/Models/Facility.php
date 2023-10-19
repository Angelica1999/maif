<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $connection = 'cloud_mysql';
    protected $table = 'facility';
    protected $guarded = array();

    public function AddFacilityInfo() {      
        return $this->belongsTo(AddFacilityInfo::class, 'facility_id', 'id');
    }
}
