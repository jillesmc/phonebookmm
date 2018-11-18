<?php

namespace App\Controllers;

use App\Models\Contact;
use Core\Auth;
use Core\BaseController;
use Core\Database;
use Core\Redirect;
use Core\Session;

class AppController extends BaseController
{
    private $contact;

    public function __construct()
    {
        parent::__construct();
        $this->contact = new Contact(Database::getDataBase());
    }

    public function index()
    {
        if(!Auth::check()){
            Session::destroy('auth');
            Session::destroy('user');
            Redirect::route('/login');
        }

        $this->setPageTitle('App');
        return $this->renderView('app/index', 'layout-app');
    }

}