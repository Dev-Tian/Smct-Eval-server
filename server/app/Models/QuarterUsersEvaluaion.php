<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuarterUsersEvaluaion extends Model
{
    protected $guarded = [];

    public function usersEvaluaions(){
        return $this->hasMany(UsersEvaluaion::class, 'quarter_of_submission_id');
    }
}
