<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreDVSAA extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'pre_dv_saa';

    public function saa(){
        return $this->belongsTo(Fundsource::class, 'fundsource_id', 'id');
    }
}
