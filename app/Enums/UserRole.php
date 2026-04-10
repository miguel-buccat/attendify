<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'Admin';
    case Teacher = 'Teacher';
    case Student = 'Student';
}
