<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Logs extends Model
{
    protected $guarded = [];

    public function deletedBy():BelongsTo
    {
        return $this->belongsTo(User::class,'deleted_by');
    }
}
