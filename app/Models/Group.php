<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = 'group';
    protected $primaryKey = 'id';
    protected $guarded = array();

    public function facility() {
        return $this->belongsTo(Facility::class, 'facility_id','id');
    }

    public function patient() {
        return $this->hasMany(Patients::class, 'group_id', 'id');
    }
    public function proponent() {
        return $this->belongsTo(Proponent::class, 'proponent_id','id');
    }
    public function user() {
        return $this->belongsTo(User::class, 'grouped_by','userid');
    }

}

