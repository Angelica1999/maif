<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utilization extends Model
{
    use HasFactory;

    protected $table = 'utilization';
    protected $guarded = array();

    public function proponentdata()
    {
        return $this->belongsTo(Proponent::class, 'proponentinfo_id', 'id');
    }

    public function fundSourcedata()
    {
        return $this->belongsTo(FundSource::class, 'fundsource_id', 'id');
    }
 

}
