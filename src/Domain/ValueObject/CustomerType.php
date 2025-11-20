<?php

namespace App\Domain\ValueObject;

enum CustomerType: string
{
    case STANDARD = 'standard';
    case VIP = 'vip';
    case STUDENT = 'student';
}
