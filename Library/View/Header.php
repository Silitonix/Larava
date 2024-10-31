<?php

namespace Library\View;

use Library\Mime;

class Header
{
    static function redirect(string $new)
    {
        self::code(303);
        header("Location: $new");
    }
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
