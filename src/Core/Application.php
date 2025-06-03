<?php

namespace Lms\Core;

use Dotenv\Dotenv;

class Application
{

    private ?Request $request = null;
    private static ?self $instance = null;
    protected array $middlewares = [];
    protected array $globalMiddlewares = [];

    public function middleware(array $middlewares): static
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    private function __construct(?string $base_dir = null)
    {
        $dotenv = Dotenv::createImmutable($base_dir ?? __DIR__.'/../../');
        $dotenv->safeLoad();

        $this->request = Request::getInstance();
        $this->runGlobalMiddleware();
    }

    public static function getInstance(...$args): self
    {
        if(self::$instance == null){
            self::$instance = new self(...$args);
        }
        return self::$instance;
    }

    public function controller(string $controller_method,array $args = []): void
    {
        $explode = explode('@', $controller_method);
        count($explode) == 2 || throw new \Exception('you must specify a controller with method name seperated by @');
        $this->controllerMethodCaller($explode[0], $explode[1],$args);
    }

    public function resource(string $controller): void
    {
        $request = Request::getInstance();
        $request_method = $request->method();
        $uri = $request->uri();
        $path = parse_url($uri, PHP_URL_PATH);
        $segments = explode('/', trim($path, '/'));

        $id = null;
        if (is_numeric(end($segments))) {
            $id = (int) array_pop($segments);
        }

        switch ($request_method) {
            case 'GET':
                if ($id) {
                    $this->controllerMethodCaller($controller, 'show', [$id]);
                } else {
                    $this->controllerMethodCaller($controller, 'index');
                }
                break;

            case 'POST':
                $this->controllerMethodCaller($controller, 'store');
                break;

            case 'PUT':
            case 'PATCH':

                if ($id) {
                    $this->controllerMethodCaller($controller, 'update', [$id]);
                } else {
                    Response::unSuccess("Missing ID for update.",);
                }
                break;

            case 'DELETE':
                if ($id) {
                    $this->controllerMethodCaller($controller, 'destroy', [$id]);
                } else {
                    Response::unSuccess("Missing ID for delete.");
                }
                break;

            default:
                Response::unSuccess("Method not allowed.",Response::HTTP_METHOD_NOT_ALLOWED);
                break;
        }
    }

    protected function runGlobalMiddleware(): void
    {
        foreach ($this->globalMiddlewares as $middleware_name) {
            $this->runMiddleware($middleware_name);
        }
    }

    protected function runMiddleware(string $middleware_name): void
    {
        $middleware_name = str_replace('/','\\',$middleware_name);
        $middleware_class = "\\Lms\\app\\Http\\Middleware\\".$middleware_name;
        if (class_exists($middleware_class)) {
            $middleware_obj = new $middleware_class();
            if (method_exists($middleware_obj, 'handle')) {
                $middleware_obj->handle(Request::getInstance());
            }
        }
    }

    protected function controllerMethodCaller(string $controller, string $method, array $args = []) : void
    {

        foreach ($this->middlewares as $middleware_name) {
            $this->runMiddleware($middleware_name);
        }


        $controller = str_replace('/','\\', $controller);
        $controller_class = "\\Lms\\app\\Http\\Controllers\\" . $controller;

        if (!class_exists($controller_class)) {
            if($_ENV['APP_DEBUG'] ?? true){
                Response::error("controller `$controller_class` not found.",Response::HTTP_INTERNAL_SERVER_ERROR);
            }else{
                Response::error("internal error",Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        $controllerReflection = new \ReflectionClass($controller_class);
        $constructor = $controllerReflection->getConstructor();
        $dependencies = [];

        if ($constructor) {
            foreach ($constructor->getParameters() as $param) {
                $type = $param->getType();
                if ($type && !$type->isBuiltin()) {
                    $className = $type->getName();
                    $dependencies[] = $this->resolveDependency($className);
                } else {
                    $dependencies[] = null;
                }
            }
        }

        $instance = $controllerReflection->newInstanceArgs($dependencies);

        if (!method_exists($instance, $method)) {
            if($_ENV['APP_DEBUG'] ?? true){
                Response::error("Method `$method` not found in controller `$controller`.",Response::HTTP_INTERNAL_SERVER_ERROR);
            }else{
                Response::error("internal error",Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        $methodReflection = new \ReflectionMethod($instance, $method);
        $finalArgs = [];

        foreach ($methodReflection->getParameters() as $i => $param) {
            $type = $param->getType();
            if ($type && !$type->isBuiltin()) {
                $className = $type->getName();
                $finalArgs[] = $this->resolveDependency($className);
            } elseif (isset($args[$param->getName()])) {
                $finalArgs[] = $args[$param->getName()];
            } else {
                $finalArgs[] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
            }
        }
        $methodReflection->invokeArgs($instance, $finalArgs);
    }

    protected function resolveDependency(string $className): mixed
    {
        if (!class_exists($className)) {
            return null;
        }

        $reflection = new \ReflectionClass($className);
        $constructor = $reflection->getConstructor();

        // Singleton: if constructor is private and has getInstance
        if ($constructor && $constructor->isPrivate()) {
            if ($reflection->hasMethod('getInstance')) {
                $getInstance = $reflection->getMethod('getInstance');
                if ($getInstance->isStatic() && $getInstance->isPublic()) {
                    return $getInstance->invoke(null);
                }
            }
        }

        // If no constructor or constructor is public, make normally
        return new $className();
    }

    public function setGlobalMiddlewares(array $globalMiddlewares): void
    {
        $this->globalMiddlewares = $globalMiddlewares;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }

}