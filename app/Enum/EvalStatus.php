<?php

namespace App\Enum;

enum EvalStatus:string
{
    case draft = 'draft';
    case rejected = 'rejected';
    case pending = 'pending';
    case completed = 'completed';
}
