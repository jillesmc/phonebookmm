<?php
/**
 * Created by PhpStorm.
 * User: jilles
 * Date: 16/11/18
 * Time: 18:36
 */

namespace App\Controllers;


class HomeController
{
    public function index()
    {
        http_response_code(200);
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($request->get);
    }

}