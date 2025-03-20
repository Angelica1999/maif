<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Kyslik\ColumnSortable\Sortable;

class OnlineUser extends Authenticatable{
	
		use HasFactory;
		use Sortable;

		protected $table = 'users';
		protected $hidden = array('password', 'remember_token');
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
				return $this->belongsTo(Facility::class, 'type_identity', 'id');
		}

		public function facility1(){
				return $this->belongsTo(Facility::class, 'type_identity', 'id');
		}

		public function proponent(){
			return $this->belongsTo(Proponent::class, 'type_identity', 'id');
		}

}