<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method \Illuminate\Support\Collection getRoleNames()
 * @method void assignRole(...$roles)
 */

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'full_name'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function departments(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function positions(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(UsersEvaluation::class, 'employee_id');
    }

    public function doesEvaluated(): HasMany
    {
        return $this->hasMany(UsersEvaluation::class, 'evaluator_id');
    }

    public function memos(): HasMany
    {
        return $this->hasMany(MemorandumViolation::class, 'evaluator_id');
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_user');
    }

    public function assignedEmployees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'assigned_user', 'evaluator_id', 'employee_id');
    }

    public function assignedEvaluators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'assigned_user',  'employee_id', 'evaluator_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->fname . " " . $this->lname;
    }

    #[Scope]
    public function search(Builder $query, ?string $term):Builder
    {
        return $query
            ->when(
                $term,
                fn($filter)
                =>
                $filter->where(
                    fn($user)
                    =>
                    $user->where( function ($q) use ($term){
                            $q->whereRaw("CONCAT(fname, ' ', lname) LIKE ?", ["%{$term}%"])
                                ->orWhereRaw("CONCAT(lname, ' ', fname) LIKE ?", ["%{$term}%"]);
                        })
                        ->orWhereAny(['email', 'username'], 'LIKE', "%{$term}%")
                )
            );
    }
}
