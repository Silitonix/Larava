<?php

use Module\Error;
use Module\Header;
use Module\Route\Info;
use Module\System;

define('ROOT', __DIR__);

function err(string $title, string $msg, int $code = 500): never
{
    Header::code($code);
    echo "<h1>$title 500 Error</h1> <p>$msg<p>";
    ob_start();
    debug_print_backtrace();
    echo str_replace('#', '<br>#', ob_get_clean());
    die();
}

function path_real(string $path): string
{
    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    $pieces = explode(DIRECTORY_SEPARATOR, $path);
    $pieces = array_filter($pieces, function ($piece) {
        if (empty($piece)) return false;
        if ($piece == '.') return false;
        if ($piece == '..') return false;
        return true;
    });

    $path = ROOT . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, $pieces);

    return $path;
}

function path(string $path): string
{
    $path = path_real($path);
    $exist = file_exists($path);

    if ($exist === false) {
        err(title: 'Path', msg: 'Path does not exist');
    }

    return $path;
}

function import(string $name)
{
    return require_once path($name . '.php');
}

spl_autoload_register(function ($class) {
    import($class);
    if (method_exists($class, '__init')) $class::__init();
});

System::init();
