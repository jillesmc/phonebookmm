<?php

namespace App\Controllers;

use Core\BaseController;
use Core\Container;

class HomeController extends BaseController
{
    private $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = Container::getModel('User');
//        $this->user = new User(Database::getDataBase());
    }

    public function index()
    {
        $this->setPageTitle("Titulo");

        $data = [
            'email' => 'seu@email.com',
            'password' => 'agoravai',
        ];

        if ($this->user->create($data)) {
            var_dump($this->user->all());
        }

        if ($this->user->create($data)) {
            var_dump($this->user->all());
        }

        $data = [
            'password' => 'atualizou',
        ];
        if ($this->user->update($data, 2)) {
            var_dump($this->user->all());
        }

        if ($this->user->delete(1)) {
            var_dump($this->user->all());
        }


//            $this->view->users = $this->user->all();
//            return $this->renderView('home/index', 'layout');


//        http_response_code(200);
//        header("Content-Type: application/json; charset=UTF-8");
//        echo json_encode($request->get);
    }

}