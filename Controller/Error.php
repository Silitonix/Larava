<?php

namespace Controller;

use Module\Header;

class Error
{
    function view($code)
    {
        Header::code($code);
        echo $code;
    }
}
