<?php
declare(strict_types=1);

class Router
{
    private array $routes = [];
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function get(string $uri, string $handler, array $middlewares = []): void
    {
        $this->add('GET', $uri, $handler, $middlewares);
    }

    public function post(string $uri, string $handler, array $middlewares = []): void
    {
        $this->add('POST', $uri, $handler, $middlewares);
    }

    public function match(array $methods, string $uri, string $handler, array $middlewares = []): void
    {
        foreach ($methods as $method) {
            $this->add($method, $uri, $handler, $middlewares);
        }
    }

    private function add(string $method, string $uri, string $handler, array $middlewares): void
    {
        $uri = '/' . trim($uri, '/');

        $this->routes[$method][$uri] = [
            'handler'     => $handler,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

        // ✅ REMOVE /public FROM URI IF PRESENT
        $uri = preg_replace('#^/public#', '', $uri);
        $uri = '/' . trim($uri, '/');

        $matchedRoute = null;
        $params = [];

        // 1. Try Exact Match
        if (isset($this->routes[$method][$uri])) {
            $matchedRoute = $this->routes[$method][$uri];
        } else {
            // 2. Try Regex Match
            foreach ($this->routes[$method] ?? [] as $routePath => $routeData) {
                // Convert {id} to named group (?P<id>[^/]+)
                $pattern = preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?P<\1>[^/]+)', $routePath);
                $pattern = "#^" . $pattern . "$#";

                if (preg_match($pattern, $uri, $matches)) {
                    $matchedRoute = $routeData;
                    
                    // Filter out numeric keys from matches
                    foreach ($matches as $key => $value) {
                        if (is_string($key)) {
                            $params[$key] = $value;
                        }
                    }
                    break;
                }
            }
        }

        if (!$matchedRoute) {
            http_response_code(404);

            if (
                isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            ) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error'   => "Route not found: {$method} {$uri}"
                ]);
            } else {
                echo "404 Not Found: {$method} {$uri}";
            }
            return;
        }

        // ==========================
        // MIDDLEWARE EXECUTION
        // ==========================
        foreach ($matchedRoute['middlewares'] as $middleware) {
            if (!class_exists($middleware)) {
                throw new RuntimeException("Middleware {$middleware} not found");
            }

            $middlewareInstance = new $middleware();

            if (!method_exists($middlewareInstance, 'handle')) {
                throw new RuntimeException("Middleware {$middleware} must have a handle() method");
            }

            $middlewareInstance->handle();

            // If middleware redirected or exited
            if (headers_sent()) {
                return;
            }
        }

        // ==========================
        // CONTROLLER EXECUTION
        // ==========================
        [$controller, $action] = explode('@', $matchedRoute['handler'], 2);

        if (!class_exists($controller)) {
            throw new RuntimeException("Controller {$controller} not found");
        }

        $reflection = new ReflectionClass($controller);

        $instance = $reflection->getConstructor()
            ? $reflection->newInstance($this->conn)
            : $reflection->newInstance();

        if (!method_exists($instance, $action)) {
            throw new RuntimeException("Method {$action} not found in {$controller}");
        }

        // Pass params to the method
        call_user_func_array([$instance, $action], $params);
    }
}
