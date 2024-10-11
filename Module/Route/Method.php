<?php

namespace Module\Route;

class Method
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const PATCH = 'PATCH';
    const OPTIONS = 'OPTIONS';
    const HEAD = 'HEAD';
    const CONNECT = 'CONNECT';
    const TRACE = 'TRACE';

    static function have($method)
    {
        $method = strtoupper($method);
        return defined(self::class . '::' . $method);
    }
}
