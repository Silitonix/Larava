<?php

namespace System\Route;

class Info
{
    static public string $uri;
    static public string $method;

    static function __init()
    {
        self::$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        self::$method = strtoupper($_SERVER['REQUEST_METHOD']);
    }
}
