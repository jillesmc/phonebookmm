<?php

namespace App\Controllers;

use App\Models\User;
use Core\Auth;
use Core\BaseController;
use Core\Container;
use Core\Database;
use Core\Redirect;
use Core\Session;
use Core\Validator;

class AppController extends BaseController
{
    private $user;

    public function __construct()
    {
        parent::__construct();
//        $this->user = Container::getModel('User');
//        $this->user = new User(Database::getDataBase());
    }

    public function index()
    {
        if(!Auth::check()){
            Session::destroy('auth');
            Session::destroy('user');
            Redirect::route('/login');
        }
        var_dump(Session::get('user'));
    }

}