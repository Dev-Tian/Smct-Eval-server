<?php

namespace App\Models;

use App\Enum\EvalReviewType;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UsersEvaluation extends Model
{

    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'reviewTypeRegular' => EvalReviewType::class
    ];

    public function employee() : BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function jobKnowledge(): HasMany
    {
        return $this->hasMany(JobKnowledge::class, 'users_evaluation_id');
    }

    public function adaptability(): HasMany
    {
        return $this->hasMany(Adaptability::class, 'users_evaluation_id');
    }

    public function qualityOfWorks(): HasMany
    {
        return $this->hasMany(QualityOfWork::class, 'users_evaluation_id');
    }

    public function teamworks(): HasMany
    {
        return $this->hasMany(Teamwork::class, 'users_evaluation_id');
    }

    public function reliabilities(): HasMany
    {
        return $this->hasMany(Reliability::class, 'users_evaluation_id');
    }

    public function ethicals(): HasMany
    {
        return $this->hasMany(Ethical::class, 'users_evaluation_id');
    }

    public function customerServices(): HasMany
    {
        return $this->hasMany(CustomerService::class, 'users_evaluation_id');
    }

    public function managerialSkills(): HasMany
    {
        return $this->hasMany(ManagerialSkills::class, 'users_evaluation_id');
    }

    public function loadRelations()
    {
        $relations = [
            'employee',
            'employee.branch:id,branch_code,branch_name',
            'employee.branches:id,branch_code,branch_name',
            'employee.positions:id,label',
            'employee.roles:id,name',
            'evaluator',
            'evaluator.branch:id,branch_code,branch_name',
            'evaluator.branches:id,branch_code,branch_name',
            'evaluator.positions:id,label',
            'evaluator.roles:id,name',
            'jobKnowledge',
            'adaptability',
            'qualityOfWorks',
            'teamworks',
            'reliabilities',
            'ethicals',
        ];

        if ($this->evaluationType === 'BranchRankNFile' || $this->evaluationType === 'BranchBasic') {
            $relations[] = 'customerServices';
        }

        if ($this->evaluationType === 'HoBasic' || $this->evaluationType === 'BranchBasicAreaManager' || $this->evaluationType === 'BranchBasic') {
            $relations[] = 'managerialSkills';
        }

        $user_eval = $this->load($relations);

        return $user_eval;
    }

    #[Scope]
    public function search(Builder $query, ?string $search) : Builder
    {
        return
            $query->when(
                $search,
                function ($sub) use ($search) {
                    $sub->where( function($query) use ($search) {
                        $query->whereHas('employee', function ($e) use ($search) {
                            $e->where( function ($q) use ($search){
                                    $q->whereRaw("CONCAT(fname, ' ', lname) LIKE ?", ["%{$search}%"])
                                    ->orWhereRaw("CONCAT(lname, ' ', fname) LIKE ?", ["%{$search}%"]);
                                })
                                ->orWhereAny(['email', 'username'], 'LIKE', "%{$search}%");
                        })
                        ->orWhereHas('evaluator', function ($e) use ($search) {
                            $e->where( function ($q) use ($search){
                                    $q->whereRaw("CONCAT(fname, ' ', lname) LIKE ?", ["%{$search}%"])
                                    ->orWhereRaw("CONCAT(lname, ' ', fname) LIKE ?", ["%{$search}%"]);
                                })
                                ->orWhereAny(['email', 'username'], 'LIKE', "%{$search}%");
                        });
                    });
                }
            );
    }
}
