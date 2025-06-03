<?php

namespace Lms\Core;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Lms\app\Models\User;

class Request
{
    private static ?Request $instance = null;
    private array $headers;
    private string $ip;
    private string $method;
    private string $uri;
    private array $queryParams;
    private array $bodyParams;
    private array $files;
    private array $cookies;
    private ?User $auth_user = null;

    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    private function __construct()
    {
        $this->headers = $this->parseHeaders();
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $user_ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $user_ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $user_ip = $_SERVER['REMOTE_ADDR'];
        }
        $this->ip = $user_ip;

        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->queryParams = $_GET;
        $this->bodyParams = $this->parseBody();
        $this->files = $_FILES;
        $this->cookies = $_COOKIE;


        if($authorization = $this->getHeader('Authorization')) {
            $token = str_replace('Bearer ', '', $authorization);

            try {
                $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
                $this->auth_user = User::find($decoded->sub);
            }catch (\Exception $e){

            }
        }
    }

    public function __get(string $key)
    {
        return $this->input($key);
    }
    private function parseHeaders(): array
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    private function parseBody(): array
    {
        if ($this->method === 'POST') {
            return $_POST;
        }

        // Handle JSON or PUT, PATCH, DELETE with raw input
        $input = file_get_contents('php://input');


        if (($contentType = $this->getHeader('Content-Type')) && str_contains($contentType, 'application/json')) {
            return json_decode($input, true) ?? [];
        }

        parse_str($input, $parsed);
        return $parsed;
    }

    public function getHeader(string $key): ?string
    {
        return $this->headers[$key] ?? null;
    }

    public function all(): array
    {
        return array_merge($this->queryParams, $this->bodyParams);
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->bodyParams[$key]
            ?? $this->queryParams[$key]
            ?? $default;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function files(): array
    {
        return $this->files;
    }

    public function cookies(): array
    {
        return $this->cookies;
    }

    public function isJson(): bool
    {
        return str_contains($this->getHeader('Content-Type') ?? '', 'application/json');
    }

    public function has(string $key): bool
    {
        return $this->input($key) !== null;
    }

    public function getIp():string
    {
        return $this->ip;
    }

    public function acceptJson(): bool
    {
        return ($this->headers['Accept'] ?? '') == 'application/json';
    }

    public function user(): ?User
    {
        return $this->auth_user;
    }

    public function ConvertEmptyStringsToNull()
    {
        foreach ($this->bodyParams as $key => $value) {
            if($value === '') $this->bodyParams[$key] = null;
        }

        foreach ($this->queryParams as $key => $value) {
            if($value === '') $this->bodyParams[$key] = null;
        }
    }
    public function trimInputs(): void{
        foreach ($this->bodyParams as $key => $value) {
            $this->bodyParams[$key] = trim($value);
        }
        foreach ($this->queryParams as $key => $value) {
            $this->queryParams[$key] = trim($value);
        }
    }
}
