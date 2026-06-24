<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemorandumViolation extends Model
{
    // protected function casts(): array
    // {
    //     return [
    //         'violation_date' => 'datetime',
    //     ];
    // }
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
                            fn($q)
                            =>
                            $q->whereAny(['violation_title','offense'],'LIKE', "%{$term}%")
                        )
                        ->orWhereHas( 'user',
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
