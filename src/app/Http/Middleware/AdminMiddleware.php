<?php

namespace Lms\app\Http\Middleware;

use Lms\Core\Request;
use Lms\Core\Response;

class AdminMiddleware extends baseMiddleware
{
    public function handle(Request $request): void
    {
        if($request->user()?->role != 'admin'){
            if($request->acceptJson()){
                Response::json(['error' => 'Unauthorized'], 401);
            }else Response::error('Unauthorized: Admin role required',Response::HTTP_UNAUTHORIZED);
        }
    }
}