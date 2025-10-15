<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teamwork extends Model
{
    protected $guarded=[];

   public function usersEvaluation(){
            return $this->belongsTo(UsersEvaluation::class, 'users_evaluation_id');
        }

}
