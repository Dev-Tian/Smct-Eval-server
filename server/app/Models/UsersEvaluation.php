<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersEvaluation extends Model
{

   protected $guarded=[];

    public function employee(){
         return $this->belongsTo(User::class, 'employee_id');
    }

    public function evaluator(){
         return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function quarterUsersEvaluations(){
        return $this->belongsTo(QuarterUsersEvaluation::class, 'quarter_of_submission_id');
    }

    public function jobKnowledges(){
         return $this->hasMany(JobKnowledge::class, 'users_evaluation_id');
    }

    public function adaptabilities(){
         return $this->hasMany(Adaptability::class, 'users_evaluation_id');
    }

    public function qualityOfWorks(){
         return $this->hasMany(QualityOfWork::class, 'users_evaluation_id');
    }

    public function teamworks(){
         return $this->hasMany(Teamwork::class, 'users_evaluation_id');
    }

    public function reliabilities(){
         return $this->hasMany(Reliability::class, 'users_evaluation_id');
    }

    public function ethicals(){
         return $this->hasMany(Ethical::class, 'users_evaluation_id');
    }

    public function customerServices(){
         return $this->hasMany(CustomerService::class, 'users_evaluation_id');
    }
}
