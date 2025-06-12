<?php

namespace core;

class Router
{
    private array $routes = [];

    public function add(string $method, string $path, string $controller, string $action): void
    {
        $method = strtoupper($method);
        $this->routes[$method][trim($path, '/')] = [$controller, $action];
    }

    public function dispatch(string $uri)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = trim(parse_url($uri, PHP_URL_PATH), '/');

        if (isset($this->routes[$method][$path])) {
            [$controller, $action] = $this->routes[$method][$path];
            (new $controller)->$action();
        } else {
            http_response_code(404);
            echo 'Page not found';
        }
    }
}