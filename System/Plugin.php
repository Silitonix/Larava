<?php

namespace System;

use System\Route\Router;

class Plugin
{
    public const DIRECTORY = 'Plugin';
    private static $list = null;

    static function list()
    {
        if (self::$list) return self::$list;

        $files = File::mglob(
            self::DIRECTORY . '/*/plugin.php',
            self::DIRECTORY . '/.*/plugin.php'
        );

        $enable = array_map(fn($f) => basename(dirname($f)), $files[0] === false ? [] : $files[0]);
        $disable = array_map(fn($f) => ltrim(basename(dirname($f)), '.'), $files[1] === false ? [] : $files[1]);

        return self::$list = [
            'enable' => $enable,
            'disable' => $disable,
            'all' => [...$enable, ...$disable]
        ];
    }

    static function delete($plugin)
    {
        if (in_array($plugin, self::list()['disable'])) $plugin = ".$plugin";
        File::delete(self::DIRECTORY . '/' . $plugin);
        File::delete(Router::DIRECTORY . '/' . self::DIRECTORY . '/' . "$plugin.php");
    }

    static function disable($plugin)
    {
        if (in_array($plugin, self::list()['disable'])) return true;
        if (!in_array($plugin, self::list()['all'])) err('Hide plugin', "plugin $plugin does not exist");
        File::rename(self::DIRECTORY . '/' . $plugin, ".$plugin");
        File::rename(Router::DIRECTORY . '/' . self::DIRECTORY . '/' . $plugin, ".$plugin.php");
    }

    static function enable($plugin)
    {
        if (in_array($plugin, self::list()['enable'])) return true;
        if (!in_array($plugin, self::list()['all'])) err('Hide plugin', 'plugin does not exist');
        File::rename(self::DIRECTORY . '/.' . $plugin, $plugin);
        File::rename(Router::DIRECTORY . '/.' . self::DIRECTORY . '/' . "$plugin.php", "$plugin.php");
    }

    static function load()
    {
        $files = File::glob(
            Router::DIRECTORY . '/' . self::DIRECTORY . '/*/plugin.php',
        );

        if ($files === false) return;

        $routes = array_map(fn($f) => basename(dirname($f)), $files);
        foreach ($routes as $route) {
            Router::load(Router::DIRECTORY . '/' . self::DIRECTORY . '/' . $route);
        }
    }
}
