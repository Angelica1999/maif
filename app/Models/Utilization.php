<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utilization extends Model
{
    use HasFactory;

    protected $table = 'utilization';
    protected $guarded = array();

    public function fundsource(){
        return $this->belongsTo(FundSource::class, 'fundsource_id');
    }

    public function proponentInfo(){
        return $this->belongsTo(ProponentInfo::class, 'proponentinfo_id');

    }

}
