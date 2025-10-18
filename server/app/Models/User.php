<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasRoles;

    protected $guarded = [];

    public function branches()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function departments()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function positions()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function evaluations()
    {
        return $this->hasMany(UsersEvaluation::class, 'employee_id');
    }

    public function doesEvaluated()
    {
        return $this->hasMany(UsersEvaluation::class, 'evaluator_id');
    }
}
