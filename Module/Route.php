<?php

namespace Module;

class Route
{
    private static Route $instance;
    private static string $prefix = '';
    private static string $method;
    private static string $uri;
    private static array $args;
    private static string $class;
    private static string $namespace = 'Controller';

    private static $patterns = [
        '/' => '\\/',
        '{number}' => '(\d+)',
        '{string}' => '(\pL+)',
        '{any}' => '(.+)',
        '{skip}' => '.+'
    ];

    public static function __init()
    {
        self::$instance = new Route();
    }

    public function handle($callback)
    {
        $pass = self::match_route();
        if (!$pass) return false;

        $slice = array_slice($pass, 1);
        $callback(...$slice);
        die();
    }

    public static function set_namespace(string $namespace)
    {
        self::$namespace = $namespace;
    }

    public static function group(string $prefix): callable
    {
        self::$prefix .= $prefix;
        return function (...$ignores) use ($prefix) {
            $len = strlen($prefix);
            self::$prefix = substr(self::$prefix, 0, -$len);
        };
    }

    public static function format($uri)
    {
        $keys = array_keys(self::$patterns);
        $values = array_values(self::$patterns);
        $pattern = str_replace($keys, $values, $uri);
        return "/^$pattern$/";
    }

    public static function hash()
    {
        $hash =
            self::$class .
            self::$method .
            self::$uri .
            serialize(self::$args);

        return hash('xxh128', $hash);
    }

    private static function compare($method, $uri)
    {
        $method = strtoupper($method);
        if (Route\Info::$method !== $method) return false;
        if (strcmp(Route\Info::$uri, $uri) === 0) return [$uri];

        $pattern = self::format($uri);
        if (!preg_match($pattern, Route\Info::$uri, $matches)) return false;

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

    public function __get($class)
    {
        self::$class = $class;
        return self::class;
    }

    public static function __callStatic($method, $args)
    {
        if (!empty(self::$method)) {
            $pass = self::match_route();
            if ($pass === false) return self::clear();

            $class = self::$namespace . '\\' . self::$class;

            $instance = new $class();
            $slice = array_slice($pass, 1);

            self::$args = [...$slice, ...$args];

            Cache::fetch();

            $instance->$method(...self::$args);
            die();
        }

        if (!Route\Method::have($method)) {
            err('Router', 'Invalid HTTP Method called');
        }
        self::$method = $method;
        self::$uri = $args[0];

        return self::$instance;
    }
}
