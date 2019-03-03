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
            'callback' => [self::$context.'\\'.$method[0], $method[1]],
            'addv' => array_slice(func_get_args(), 3)
        ];
    }

    private static $tmp;
    public static function module ($type, $pattern, $class) {
        self::$tmp = [$pattern, $type, $class, self::$context];
        self::context('pe\\engine', function () {
            $pattern = self::$tmp[0];
            $type = self::$tmp[1];
            $class = self::$tmp[2];
            $ctx = self::$tmp[3];
            $pattern = str_replace('<action>', '(?P<action>(-|\w)+)', $pattern);
            self::add('*', $pattern, 'Router.dispatchModule', $ctx.'\\'.$class);
        });
    }

    public static function dispatchModule ($params, $class) {
        if (method_exists($class, $params['action'])) {
            if (strpos($params['action'], '_') === 0) View::error(403, 'No access to private method');
            call_user_func_array([$class, $params['action']], array_merge([$params]));
        } else {
            View::error(404, 'No module action');
        }
    }

    public static function context ($ctx, $cb) {
        $lastCtx = self::$context;
        self::$context = $ctx;
        $cb();
        self::$context = $lastCtx;
    }

    public static function dispatch ($path) {
        foreach (self::$routes as $route) {
            $matches = [];
            if (preg_match($route['pattern'], $path, $matches)) {
                call_user_func_array($route['callback'], array_merge([$matches], $route['addv']));
                exit;
            }
        }

        View::error(404, 'No routes for path `/'.$path.'`');
    }
}