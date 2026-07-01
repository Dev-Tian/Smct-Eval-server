<?php

namespace App\Enum;

enum EvalStatus:string
{
    case pending_approval_1 = 'pending_approval_1';
    case pending_approval_2 = 'pending_approval_2';
    case rejected = 'rejected';
    case pending = 'pending';
    case completed = 'completed';
}
