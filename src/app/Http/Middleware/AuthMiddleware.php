<?php
namespace Lms\app\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Lms\Core\Request;
use Lms\Core\Response;

class AuthMiddleware extends baseMiddleware
{
    public function handle(Request $request): void
    {
        $authorization = $request->getHeader('Authorization');

        if (!$authorization || !str_starts_with($authorization, 'Bearer ')) {
            Response::error('Unauthorized: No token provided', Response::HTTP_UNAUTHORIZED);
        }

        $token = str_replace('Bearer ', '', $authorization);

        try {
            JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        } catch (\Exception $e) {
            Response::error('Unauthorized: Invalid token', Response::HTTP_UNAUTHORIZED);
        }
    }
}
