<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assign_approver extends Model
{
    protected $guarded = [];

    public function evaluator():BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function approver():BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
