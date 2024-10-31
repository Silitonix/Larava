<?php

namespace System\Route;

use System\View\Cache;
use System\View\Header;

/**
 * Router
 *
 * @method static \System\Route get(?string $url)
 * @method static \System\Route post(?string $url)
 * @method static \System\Route put(?string $url)
 * @method static \System\Route delete(?string $url)
 * @method static \System\Route patch(?string $url)
 * @method static \System\Route options(?string $url)
 * @method static \System\Route head(?string $url)
 * @method static \System\Route connect(?string $url)
 * @method static \System\Route trace(?string $url)
 */
class Router
{

    public const DIRECTORY = 'Route';
    private static Router $instance;
    private static string $prefix = '';
    private static string $namespace;
    private static string $class;
    private static string $method;
    private static string $uri;

    private static $patterns = [
        '/\//' => '\\/',
        '/{skip}/' => '.*',
        '/{(.+)}/' => '(?<$1>.+)',
        '/:any}/' => ':.+}',
        '/:int}/' => ':[-+]?\d+}',
        '/:float}/' => ':[-+]?[0-9]*\.[0-9]+}',
        '/:hex}/' => ':[0-9a-fA-F]+}',
        '/:octal}/' => ':[0-7]+}',
        '/:decimal}/' => ':\d+}',
        '/:string}/' => ':\pL+}',
        '/:kabab}/' => ':[a-zA-Z0-9\-]+}',
        '/:snake}/' => ':[a-zA-Z0-9_]+}',
        '/\{([\pL_]+)(?:\:((?:[^{}]|{[^{}]*})+))?\}/' => '(?<$1>$2)',
    ];

    public static function __init()
    {
        self::$instance = new Router();
    }

    public function handle($callback)
    {
        $pass = self::match_route();
        if (!$pass) return false;

        $params = array_filter($pass, 'is_string', ARRAY_FILTER_USE_KEY);

        $callback(...$params);
        die();
    }

    public function redirect(string $route): void
    {
        if (!self::matchRoute()) return;

        Header::redirect($route);
        exit();
    }

    public static function load(string $namespace)
    {
        self::$namespace = $namespace;
        import(self::DIRECTORY . '/' . $namespace);
    }

    public static function group(string $prefix): callable
    {
        self::$prefix .= $prefix;
        return function (...$ignores) use ($prefix) {
            $len = strlen($prefix);
            self::$prefix = substr(self::$prefix, 0, -$len);
        };
    }

    private static function format($uri)
    {
        $keys = array_keys(self::$patterns);
        $values = array_values(self::$patterns);
        $pattern = preg_replace($keys, $values, $uri);
        return "/^$pattern$/";
    }

    private static function compare($method, $uri)
    {
        $method = strtoupper($method);
        if (Info::$method !== $method) return false;
        if (strcmp(Info::$uri, $uri) === 0) return [$uri];

        $pattern = self::format($uri);
        if (!preg_match($pattern, Info::$uri, $matches)) return false;

        return $matches;
    }

    private static function match_route()
    {
        $uri = self::$prefix . self::$uri;
        $matches = self::compare(self::$method, $uri);
        if ($matches === false) return self::clear();
        return $matches;
    }

    private static function clear()
    {
        self::$class = '';
        self::$method = '';
        self::$uri = '';
        return false;
    }

    public function __call($method, $args)
    {
        if (!empty(self::$class)) {
            $pass = self::match_route();
            if ($pass === false) return self::clear();

            $class = self::$namespace . '\\' . self::$class;
            $instance = new $class();

            Cache::fetch();

            $params = array_filter([...$pass, ...$args], 'is_string', ARRAY_FILTER_USE_KEY);

            try {
                $instance->$method(...$params);
            } catch (\Throwable $th) {

                err(
                    title: 'Router',
                    msg: "there is a problem for calling connected class to route<br>{$th->getMessage()}"
                );
            }


            die();
        }
    }

    public function __get($class)
    {
        self::$class = $class;
        return self::class;
    }

    public static function __callStatic($method, $args)
    {
        if (!empty(self::$class)) {
            $pass = self::match_route();
            if ($pass === false) return self::clear();

            $class = self::$namespace . '\\' . self::$class;
            $instance = new $class();

            Cache::fetch();

            $params = array_filter([...$pass, ...$args], 'is_string', ARRAY_FILTER_USE_KEY);

            try {
                $instance->$method(...$params);
            } catch (\Throwable $th) {

                err(
                    title: 'Router',
                    msg: "there is a problem for calling connected class to route<br>{$th->getMessage()}"
                );
            }


            die();
        }

        if (!Method::is_valid($method)) {
            err('Router', 'Invalid HTTP Method called');
        }
        self::$method = $method;
        self::$uri = $args[0] ?? '';

        return self::$instance;
    }
}
