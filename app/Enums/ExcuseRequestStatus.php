<?php

namespace App\Enums;

enum ExcuseRequestStatus: string
{
    case Pending = 'Pending';
    case Acknowledged = 'Acknowledged';
    case Rejected = 'Rejected';
}
