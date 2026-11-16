<?php

namespace App\Enum;

enum LogTypes:string
{
    case DELETED_USER = "user";
    case DELETED_EVALUATION = "evaluation";
}
