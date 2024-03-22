<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fundsource_Files extends Model
{
    use HasFactory;

    protected $table = 'fundsource_files';
    protected $primaryKey = 'id';
    protected $guarded = array();

}
