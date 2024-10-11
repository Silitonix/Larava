<?php

namespace Module;

class Plugin
{
    private static string $directory = 'Plugin';

    static function list()
    {
    }

    static function routes()
    {
    }

    static function __init()
    {
        File::mkdir(self::$directory);
    }
}
