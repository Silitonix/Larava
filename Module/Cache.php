<?php

namespace Module;

use Module\Route\Info;

class Cache
{
    private static string $directory = 'Cache';

    static function start()
    {
        ob_start();
    }

    static function store()
    {
        File::write(self::filename(), ob_get_contents());
    }

    static function flush()
    {
        File::write(self::filename(), ob_get_flush());
    }

    static function filename()
    {
        $filename = hash('sha256', Info::$uri);
        $dir = self::$directory;
        return "$dir/$filename.html";
    }

    static function purge()
    {
        $filename = self::filename();
        File::delete($filename);
    }

    static function fetch()
    {
        $filename = self::filename();
        if (!File::exist($filename)) return;
        File::serve($filename);
        die();
    }
}
