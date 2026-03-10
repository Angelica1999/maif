<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable{
	
  protected $connection= 'dohdtr';
	protected $table = 'users';
	protected $primaryKey = 'userid';

	  protected $keyType = 'string';   // IMPORTANT
    public $incrementing = false;

	protected $hidden = array('password', 'remember_token');

}