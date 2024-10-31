<?php

namespace Library;

use Library\View\Header;

class Json
{
    static function read(string $string)
    {
        json_decode($string, true);
    }

    static function from(mixed $mix): string|false
    {
        return json_encode($mix);
    }
    
    static function serve(mixed $mix): never
    {
        Header::mime('.json');
        echo self::from($mix);
        die;
    }
}
