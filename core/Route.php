<?php

namespace Core;


class Route
{
    private $routes;

    public function __construct(array $routes)
    {
        $this->setRoutes($routes);
        $this->run();
    }

    private function setRoutes(array $routes)
    {
        $parsedRoutes = [];
        foreach ($routes as $route) {
            $controllerActionArray = explode('@', $route[2]);
            if (isset($route[3])) {
                $r = [$route[0], $route[1], $controllerActionArray[0], $controllerActionArray[1], $route[3]];
            } else {
                $r = [$route[0], $route[1], $controllerActionArray[0], $controllerActionArray[1]];
            }
            $parsedRoutes[] = $r;
        };
        $this->routes = $parsedRoutes;
    }

    private function getRequest()
    {
        $obj = new \stdClass;
        $obj->get = null;
        $obj->post = null;

        foreach ($_GET as $key => $value) {
            @$obj->get->$key = $value;
        }
        if ($this->getRequestContentType() == 'application/json') {
            $_POST = json_decode(file_get_contents('php://input'), true) ?? [];
            foreach ($_POST as $key => $value) {
                @$obj->post->$key = $value;
            }
        }

        return $obj;
    }

    private function getRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    private function getRequestContentType()
    {
        return $_SERVER['HTTP_CONTENT_TYPE'] ?? null;
    }

    private function getUrl()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    private function run()
    {
        $routeFound = false;

        $url = $this->getUrl();
        $urlArray = explode('/', $url);
        $method = $this->getRequestMethod();
        foreach ($this->routes as $route) {
            if ($route[0] != $method) {
                continue;
            }

            $param = [];
            $routeArray = explode('/', $route[1]);

            for ($i = 0; $i < count($routeArray); $i++) {
                if (strpos($routeArray[$i], '{') !== false
                    && count($urlArray) == count($routeArray)) {
                    $routeArray[$i] = $urlArray[$i];
                    $param[] = $urlArray[$i];
                }
                $route[1] = implode($routeArray, '/');
            }

            if ($url == $route[1]) {
                $routeFound = true;
                $controller = $route[2];
                $action = $route[3];

                //Proteção de rota com JWT

                if (isset($route[4])) {
                    $dataToken = Auth::validateToken();
                    if (!$dataToken) {
                        $action = 'forbidden';
                    } elseif ($route[4] == 'user-auth' && $dataToken->data->role !== 'user') {
                        $action = 'forbidden';
                    } elseif ($route[4] == 'admin-auth' && $dataToken->data->role !== 'admin') {
                        $action = 'forbidden';
                    }
                }


                break;
            }
        }

        if ($routeFound) {
            $controller = Container::newController($controller);

            switch (count($param)) {
                case 1:
                    $controller->$action($param[0], $this->getRequest());
                    break;
                case 2:
                    $controller->$action($param[0], $param[1], $this->getRequest());
                    break;
                case 3:
                    $controller->$action($param[0], $param[1], $param[2], $this->getRequest());
                    break;
                default:
                    $controller->$action($this->getRequest());
                    break;
            }
        } else {
            Container::pageNotFound();
        }
    }
}