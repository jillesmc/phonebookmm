<?php

namespace Core;


class Container
{
    public static function newController($controller)
    {
        $controller = "App\\Controllers\\" . $controller;
        return new $controller;
    }

    public static function getModel($model) :BaseModel
    {
        $objModel = "\\App\\Models\\" . $model;
        return new $objModel(Database::getDataBase());
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