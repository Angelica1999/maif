<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class OnlineUser extends Authenticatable{
	
	protected $table = 'users';

	protected $hidden = array('password', 'remember_token');

	public function facility(){
			return $this->belongsTo(Facility::class, 'identity_type', 'id');
	}

	public function proponent(){
		return $this->belongsTo(Proponent::class, 'identity_type', 'id');
	}

}