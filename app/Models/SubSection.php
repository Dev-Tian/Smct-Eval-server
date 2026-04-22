<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSection extends Model
{
    protected $guarded = [];

    public function users(){
        return $this->hasMany(SubSection::class, 'section_id');
    }

}
