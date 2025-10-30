<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuarterUsersEvaluation extends Model
{
    protected $guarded = [];

    public function usersEvaluations(){
        return $this->hasMany(UsersEvaluation::class, 'quarter_of_submission_id');
    }
}
