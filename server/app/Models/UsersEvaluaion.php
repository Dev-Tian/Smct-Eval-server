<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersEvaluaion extends Model
{

   protected $guarded=[];

    public function employees(){
         return $this->belongsTo(User::class, 'employee_id');
    }

    public function evaluators(){
         return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function quarterUsersEvaluations(){
        return $this->belongsTo(QuarterUsersEvaluaion::class, 'quarter_of_submission_id');
    }

    //submissions extended morph table relations

    public function jobKnowledges(){
         return $this->hasMany(JobKnowledge::class, 'users_evalution_id');
    }

    public function adaptabilities(){
         return $this->hasMany(Adaptability::class, 'users_evalution_id');
    }

    public function qualityOfWorks(){
         return $this->hasMany(QualityOfWork::class, 'users_evalution_id');
    }

    public function teamworks(){
         return $this->hasMany(Teamwork::class, 'users_evalution_id');
    }

    public function reliabilities(){
         return $this->hasMany(Reliability::class, 'users_evalution_id');
    }

    public function ethicals(){
         return $this->hasMany(Ethical::class, 'users_evalution_id');
    }

    public function customerServices(){
         return $this->hasMany(CustomerService::class, 'users_evalution_id');
    }
}
