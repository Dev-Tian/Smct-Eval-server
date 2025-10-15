<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teamwork extends Model
{
    protected $guarded=[];

     public function usersEvaluaions(){
            return $this->belongsTo(UsersEvaluaion::class, 'users_evalution_id');
        }

}
