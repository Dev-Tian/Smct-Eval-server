<?php

namespace App\Models;

use App\Enum\EvalReviewType;
use Illuminate\Database\Eloquent\Model;

class UsersEvaluation extends Model
{

    protected $guarded = [];

    protected $casts = [
        'reviewTypeRegular' => EvalReviewType::class
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function jobKnowledge()
    {
        return $this->hasMany(JobKnowledge::class, 'users_evaluation_id');
    }

    public function adaptability()
    {
        return $this->hasMany(Adaptability::class, 'users_evaluation_id');
    }

    public function qualityOfWorks()
    {
        return $this->hasMany(QualityOfWork::class, 'users_evaluation_id');
    }

    public function teamworks()
    {
        return $this->hasMany(Teamwork::class, 'users_evaluation_id');
    }

    public function reliabilities()
    {
        return $this->hasMany(Reliability::class, 'users_evaluation_id');
    }

    public function ethicals()
    {
        return $this->hasMany(Ethical::class, 'users_evaluation_id');
    }

    public function customerServices()
    {
        return $this->hasMany(CustomerService::class, 'users_evaluation_id');
    }

    public function search($query, $search)
    {
        return $query
            ->when(
                $search,
                function ($query, $search) {
                    $query->whereHas(
                        'employee',
                        fn($q)
                        =>
                        $q->whereRaw('CONCAT(fname, " ", lname) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('CONCAT(lname, " ", fname) LIKE ?', ["%{$search}%"])
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%")
                    );
                    $query->whereHas(
                        'evaluator',
                        fn($q)
                        =>
                        $q->whereRaw('CONCAT(fname, " ", lname) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('CONCAT(lname, " ", fname) LIKE ?', ["%{$search}%"])
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%")
                    );
                }
            );
    }
}
