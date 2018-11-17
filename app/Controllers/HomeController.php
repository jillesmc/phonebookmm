<?php

namespace App\Controllers;

use Core\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        $this->setPageTitle("Titulo");
        $this->renderView('home/index', 'layout');

//        http_response_code(200);
//        header("Content-Type: application/json; charset=UTF-8");
//        echo json_encode($request->get);
    }

}