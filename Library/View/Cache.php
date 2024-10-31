<?php

namespace Library\View;

use Library\File;
use Library\Route\Info;

class Cache
{
    private const DIRECTORY = 'Cache';
    private const ALGORITHM = 'sha256';

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
        $filename = hash(self::ALGORITHM, Info::$uri);
        $dir = self::DIRECTORY;
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
