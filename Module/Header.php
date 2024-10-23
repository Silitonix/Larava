<?php

namespace Module;

class Header
{
    static function mime(string $name)
    {
        $mime = Mime::get_mime($name);
        self::content_type($mime);
    }
    static function content_type(string $mime)
    {
        header("Content-Type: $mime");
    }
    static function code(int $code)
    {
        return http_response_code($code);
    }
}
