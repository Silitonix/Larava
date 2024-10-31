<?php

namespace Controller;

use System\Plugin;

class Page
{
    function index() {
        echo var_dump( Plugin::list()['all']);
        Plugin::disable("Shop");
        Plugin::disable("User");
    }
}
