<?php

namespace Module;

class Header
{
    static function mime($name)
    {
        $mime = Mime::get_mime($name);
        self::content_type($mime);
    }
    static function content_type($mime)
    {
        header("Content-Type: $mime");
    }
    static function code($code)
    {
        http_response_code($code);
    }
}
