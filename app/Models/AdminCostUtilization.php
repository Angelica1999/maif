<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminCostUtilization extends Model
{
    use HasFactory;

    protected $table = 'admin_cost_util';

    public function fundSourcedata(){
        return $this->belongsTo(FundSource::class, 'fundsource_id', 'id');
    }
}
