<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransmittalDetails extends Model
{
    use HasFactory;

    protected $table = "transmittal_details";

    public function patients(){
        return $this->hasMany(TransmittalPatients::class, 'transmittal_details', 'id');
    }
}
