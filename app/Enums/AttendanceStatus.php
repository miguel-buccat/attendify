<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Present = 'Present';
    case Late = 'Late';
    case Absent = 'Absent';
    case Excused = 'Excused';
}
