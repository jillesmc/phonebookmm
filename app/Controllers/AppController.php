<?php

namespace App\Controllers;

use Core\BaseController;

/**
 * Class AppController
 * @package App\Controllers
 */
class AppController extends BaseController
{
    /**
     * @return mixed
     */
    public function index()
    {
        $this->setPageTitle('App');
        return $this->renderView('app/index', 'layout-app');
    }

}