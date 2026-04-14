<?php

namespace App\Enums;

enum SessionStatus: string
{
    case Scheduled = 'Scheduled';
    case Active = 'Active';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';
}
