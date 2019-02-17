<?php
namespace pe\engine;
class Router {
    private static $context = '';
    private static $routes = [];
    public static function add ($type, $pattern, $method) {
        $method = explode('.', $method);
        $pattern = ltrim($pattern, '/');
        $pattern = str_replace('/', '\\/', $pattern);
        $pattern = preg_replace('/{(\w+):([^{}]*)}/', '(?P<$1>$2)', $pattern);
        $pattern = '/^'.$pattern.'/';
        self::$routes[] = [
            'pattern' => $pattern,
            'callback' => [self::$context.'\\'.$method[0], $method[1]]
        ];
    }

    public static function context ($ctx, $cb) {
        self::$context = $ctx;
        $cb();
        self::$context = '';
    }

    public static function dispatch ($path) {
        foreach (self::$routes as $route) {
            $matches = [];
            if (preg_match($route['pattern'], $path, $matches)) {
                call_user_func_array($route['callback'], [$matches]);
                exit;
            }
        }

        View::error(404, 'No routes for path `/'.$path.'`');
    }
}