<?php

namespace App\Controllers;

use App\Models\Admin;
use App\Models\Contact;
use App\Models\User;
use Core\AuthenticateAdminTrait;
use Core\BaseController;
use Core\Database;
use Core\Response;

class AdminController extends BaseController
{
    use AuthenticateAdminTrait;

    private $user;
    private $admin;
    private $contact;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User(Database::getDataBase());
        $this->admin = new Admin(Database::getDataBase());
        $this->contact = new Contact(Database::getDataBase());
    }

    public function index()
    {
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

    public function show($admin_id)
    {
        $adminUser = $this->admin->find($admin_id);
        if (!$adminUser) {
            return Response::json(Response::NO_CONTENT);
        }

        return Response::json(Response::OK, [
            'status' => 'success',
            'data' => [
                'id' => $adminUser->id
            ]
        ]);
    }

}