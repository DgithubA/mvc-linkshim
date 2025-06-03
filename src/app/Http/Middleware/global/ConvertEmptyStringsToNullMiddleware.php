<?php

namespace Lms\app\Http\Middleware\global;

use Lms\app\Http\Middleware\baseMiddleware;
use Lms\Core\Request;

class ConvertEmptyStringsToNullMiddleware extends baseMiddleware
{
    public function handle(Request $request): void
    {
        $request->ConvertEmptyStringsToNull();
    }
}