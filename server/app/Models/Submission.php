<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
     protected $guarded=[];

    public function employees(){
         return $this->belongsTo(User::class, 'employee_id');
    }

    public function evaluator(){
         return $this->belongsTo(User::class, 'evaluator_id');
    }


}
