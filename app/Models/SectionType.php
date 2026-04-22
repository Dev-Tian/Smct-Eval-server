<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionType extends Model
{
    protected $guarded = [];

    public function sections()
    {
        return $this->hasMany(SubSection::class, 'type_id');
    }
}
