<?php

namespace App\Controllers;

use App\Models\Contact;
use App\Models\User;
use Core\AuthAdmin;
use Core\AuthenticateAdmin;
use Core\BaseController;
use Core\Database;
use Core\Redirect;
use Core\Response;
use Core\Session;

class AdminController extends BaseController
{
    use AuthenticateAdmin;

    private $authAdmin;
    private $user;
    private $contact;

    public function __construct()
    {
        parent::__construct();
        $this->authAdmin = new AuthAdmin();
        $this->user = new User(Database::getDataBase());
        $this->contact = new Contact(Database::getDataBase());
    }

    public function index()
    {
        if (!AuthAdmin::check()) {
            Session::destroy('admin');
            return Redirect::route('/admin/login');
        }

        $this->setPageTitle('Dashboard');
        return $this->renderView('/adm/dashboard', 'layout-admin-dashboard');
    }

    public function data()
    {
        Response::json(Response::OK, [
            'users' => $this->user->countTotal(),
            'contacts' => $this->contact->countTotal(),
            'users_last_month' => $this->user->countTotalLastMonth(),
            'contacts_last_month' => $this->contact->countTotalLastMonth(),
            'users_last_fifteen_days_per_day' => $this->user->countTotalLastFifteenDaysPerDay(),
            'contacts_last_fifteen_days_per_day' => $this->contact->countTotalLastFifteenDaysPerDay(),
            'users_per_zone_code' => $this->user->countPerZoneCode(),
            'contacts_per_zone_code' => $this->contact->countPerZoneCode()
        ]);


    }

}