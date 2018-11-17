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
}