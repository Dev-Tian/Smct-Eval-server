<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerService extends Model
{
    protected $guarded = [];

    public function usersEvaluation(): BelongsTo
    {
        return $this->belongsTo(UsersEvaluation::class, 'users_evaluation_id');
    }
}
