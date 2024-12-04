<?php

use Library\Route\Router;


Router::get('/login')->login();
Router::get('/register')->register();