<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Facility extends Model
{
    use HasFactory;
    use Sortable;

    // protected $connection = 'cloud_mysql';
    protected $table = 'facility';
    protected $guarded = array();
    public $sortable = ['name'];

    public function addFacilityInfo() {      
        return $this->belongsTo(AddFacilityInfo::class, 'id', 'facility_id');
    }
    public function dv(){
        return $this->hasmany(Dv::class, 'id', 'facility_id');
    }

}
