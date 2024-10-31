<?php

namespace Library;

use Library\Route\Router;

class Maintainer
{
    static function init(): void
    {
        Plugin::load();
        Router::load('Controller');
    }
}
