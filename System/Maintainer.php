<?php

namespace System;

use System\Route\Router;

class Maintainer
{
    static function init(): void
    {
        Router::load('Controller');
        Plugin::load();
    }
}
