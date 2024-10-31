<?php

namespace System;

use System\Route\Router;

class Maintainer
{
    static function init(): void
    {
        Plugin::load();
        Router::load('Controller');
    }
}
