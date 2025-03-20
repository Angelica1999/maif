<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Registration extends Model
{
    use HasFactory;
    use Sortable;

    protected $connection = 'mysql';
    protected $table = 'user_registration';
    protected $guarded = array();
    public $sortable = [
        'id',
        'lname', 
        'fname', 
        'facility.name', 
        'proponent.proponent',
        'birthdate',
        'user_type',
        'gender',
        'email',
        'contact_no',
        'facility_id'
    ];

    public function facility(){
        return $this->belongsTo(Facility::class, 'identity_type', 'id');
    }

    public function proponent(){
      return $this->belongsTo(Proponent::class, 'identity_type', 'id');
    }
    
}

