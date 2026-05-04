<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
     protected $guarded=[];

    public function usersBranches(): BelongsToMany
    {
         return $this->belongsToMany(User::class, 'branch_user');
    }

    public function userBranch(): HasMany
    {
        return $this->hasMany(User::class, 'branch_id');
    }

}
