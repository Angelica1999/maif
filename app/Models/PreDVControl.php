<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreDVControl extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'pre_dv_control';

}
