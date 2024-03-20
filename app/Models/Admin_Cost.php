<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin_Cost extends Model
{
    use HasFactory;

    protected $table = 'admincost_usage';
    protected $guarded = array();

}
