<?php

use Module\Route;

Route::group('/')(
  Route::get()->Page::index(),
  Route::get('index.php')->redirect('/')
);

Route::get('{skip}')->handle(fn() => print('404'));

die();
