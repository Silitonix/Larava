<?php

namespace Library\Route;

use Closure;
use Library\View\Cache;
use Library\View\Header;
use Throwable;

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
    private static string $namespace = '';
    private static string $class = '';
    private static string $method = '';
    private static string $uri = '';

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
        $params = self::match_route();
        if (!$params) return false;

        $params = array_filter($params, 'is_string', ARRAY_FILTER_USE_KEY);
        $callback(...$params);
        exit();
    }

    public function redirect(string $route): void
    {
        if (!self::match_route()) return;
        Header::redirect($route);
        exit();
    }

    public static function load(string $namespace, string $class = '')
    {
        self::$class = $class;
        self::$namespace = $namespace;
        import(self::DIRECTORY . '/' . $namespace);
    }

    public static function group(string $prefix, Closure $closure): void
    {
        $prefix_original = self::$prefix;
        self::$prefix .= $prefix;
        if (!str_starts_with(Info::$uri, self::$prefix)) return;
        $closure();
        self::$prefix = $prefix_original;
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
        self::$method = '';
        self::$uri = '';
        return false;
    }

    public function __call($method, $args)
    {
        self::invoke_class_method($method, $args);
    }

    public function __get($class)
    {
        self::$class = $class;
        return self::class;
    }

    public static function __callStatic($method, $args)
    {
        if (empty(self::$class) || empty(self::$method)) {
            return self::prepare_request_method($method, $args[0] ?? '');
        }
        self::invoke_class_method($method, $args);
    }

    private static function invoke_class_method($method, $args)
    {
        $url_params = self::match_route();
        if ($url_params === false) return self::clear();

        $class = self::$namespace . '\\' . self::$class;
        $instance = new $class();

        Cache::fetch();
        $merged = array_merge($url_params, $args);
        $params = array_filter($merged, 'is_string', ARRAY_FILTER_USE_KEY);

        try {
            $instance->$method(...$params);
        } catch (Throwable $th) {
            self::error("there is a problem for calling connected class to route", $th);
        }

        exit();
    }

    private static function prepare_request_method($method, $uri)
    {
        if (!Method::is_valid($method)) {
            err('Router', 'Invalid HTTP Method called');
        }
        self::$method = $method;
        self::$uri = $uri;

        return self::$instance;
    }

    private static function error(string $message, Throwable $th)
    {
        err(
            title: 'Router',
            msg: "$message<br>{$th->getMessage()}"
        );
    }
}
