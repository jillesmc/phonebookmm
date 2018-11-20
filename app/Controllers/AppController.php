<?php

namespace App\Controllers;

use Core\BaseController;

class AppController extends BaseController
{
    public function index()
    {
        $this->setPageTitle('App');
        return $this->renderView('app/index', 'layout-app');
    }

}