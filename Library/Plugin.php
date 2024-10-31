<?php

namespace Library;

use Library\Route\Router;

class Plugin
{
    public const DIRECTORY = 'Plugin';
    private static ?array $list = null;

    static function get_list()
    {
        if (self::$list) {
            return self::$list;
        }

        $enabled = [];
        $disabled = [];
        $files = File::mglob(
            self::DIRECTORY . '/*/Plugin.php',
            self::DIRECTORY . '/.*/Plugin.php'
        );

        if ($files[0] !== false) {
            $enabled = array_map(fn($file) => basename(dirname($file)), $files[0]);
        }

        if ($files[1] !== false) {
            $disabled = array_map(fn($file) => ltrim(basename(dirname($file)), '.'), $files[1]);
        }

        return self::$list = [
            'enabled' => $enabled,
            'disabled' => $disabled,
            'all' => array_merge($enabled, $disabled)
        ];
    }

    static function delete($name)
    {
        $is_disabled = in_array($name, self::get_list()['disabled']);
        $path = self::DIRECTORY . '/' . ($is_disabled ? ".$name" : $name);
        $path_router = Router::DIRECTORY . '/' . self::DIRECTORY . '/' . "$name.php";

        File::delete($path);
        File::delete($path_router);
    }

    static function disable($name): bool
    {
        if (in_array($name, self::get_list()['disabled'])) {
            return true;
        }

        if (!in_array($name, self::get_list()['all'])) {
            self::error("Disable plugin $name does not exist");
            return false;
        }

        File::rename(self::DIRECTORY . '/' . $name, ".$name");
        File::rename(Router::DIRECTORY . '/' . self::DIRECTORY . "/$name.php", "/.$name.php");

        return true;
    }

    static function enable($name)
    {
        if (in_array($name, self::get_list()['enabled'])) {
            return true;
        }

        if (!in_array($name, self::get_list()['all'])) {
            self::error("Enable plugin $name does not exist");
            return false;
        }

        File::rename(self::DIRECTORY . "/.$name", $name);
        File::rename(Router::DIRECTORY . '/' . self::DIRECTORY . "/.$name.php", "$name.php");

        return true;
    }

    static function load()
    {
        $files = File::glob(Router::DIRECTORY . '/' . self::DIRECTORY . '/*.php');

        if ($files === false) {
            return;
        }

        $routes = array_map(fn($file) => pathinfo($file, PATHINFO_FILENAME), $files);

        foreach ($routes as $route) {
            $prefix = strtolower($route);
            Router::group("/$prefix")(
                Router::load(self::DIRECTORY . '\\' . $route, 'Plugin')
            );
        }
    }
    private static function error(string $message): void
    {
        err('Plugin', $message);
    }
}
