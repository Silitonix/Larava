<?php

namespace Module;

class System
{
    static function init()
    {
        System::routes();
        Plugin::routes();
    }
    static function routes()
    {
        import('routes');
    }
}
