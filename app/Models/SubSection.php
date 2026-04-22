<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSection extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users(){
        return $this->hasMany(SubSection::class, 'section_id');
    }

    public function sectionType()
    {
        return $this->belongsTo(SectionType::class, 'type_id');
    }
}
