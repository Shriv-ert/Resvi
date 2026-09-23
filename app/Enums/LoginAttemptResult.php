<?php

namespace App\Enums;

enum LoginAttemptResult
{
    case Authenticated;
    case InvalidCredentials;
    case Throttled;
}
