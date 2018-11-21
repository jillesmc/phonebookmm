<?php

namespace Core;


/**
 * Class Container
 * @package Core
 */
class Container
{
    /**
     * @param string $controller
     * @return mixed
     */
    public static function newController(string $controller)
    {
        $controller = "App\\Controllers\\" . $controller;
        return new $controller;
    }

    /**
     * @param string $model
     * @return BaseModel
     */
    public static function getModel(string $model) :BaseModel
    {
        $objModel = "\\App\\Models\\" . $model;
        return new $objModel(Database::getDataBase());
    }

    /**
     * @return mixed
     */
    public static function pageNotFound()
    {
        if (file_exists(__DIR__ . "/../app/Views/404.phtml")) {
            return require_once __DIR__ . "/../app/Views/404.phtml";
        } else {
            echo "Erro 404: Page not found";
        }
    }
}