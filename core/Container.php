<?php
/**
 * Created by PhpStorm.
 * User: jilles
 * Date: 16/11/18
 * Time: 18:37
 */

namespace Core;


class Container
{
    public static function newController($controller)
    {
        $controller = "App\\Controllers\\" . $controller;
        return new $controller;
    }

    public static function pageNotFound()
    {
        if (file_exists(__DIR__ . "/../app/Views/404.phtml")) {
            return require_once __DIR__ . "/../app/Views/404.phtml";
        } else {
            echo "Erro 404: Page not found";
        }
    }
}