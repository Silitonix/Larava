<?php

namespace Module;

class Json
{
    static function from(string $string)
    {
        json_decode($string, true);
    }

    static function create(mixed $mix): string|false
    {
        return json_encode($mix);
    }

    static function error(string $msg) {
        
    }

    static function serve(mixed $mix): never
    {
        Header::mime('.json');
        echo self::create($mix);
        die;
    }
}
