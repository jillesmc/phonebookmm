<?php

namespace App\Controllers;

use Core\AuthenticateAdminTrait;
use Core\BaseController;
use Core\Container;
use Core\Response;

/**
 * Class AdminController
 * @package App\Controllers
 */
class AdminController extends BaseController
{
    use AuthenticateAdminTrait;

    private $user;
    private $admin;
    private $contact;

    /**
     * AdminController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->user = Container::getModel('User');
        $this->admin = Container::getModel('Admin');
        $this->contact = Container::getModel('Contact');
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $this->setPageTitle('Dashboard');
        return $this->renderView('/adm/dashboard', 'layout-admin-dashboard');
    }

    /**
     * Retorna os dados do dashboard do admin
     */
    public function data()
    {
        try{
        Response::json(Response::OK, [
            'status' => 'success',
            'data' => [
                'users' => $this->user->countTotal(),
                'contacts' => $this->contact->countTotal(),
                'users_last_month' => $this->user->countTotalLastMonth(),
                'contacts_last_month' => $this->contact->countTotalLastMonth(),
                'users_last_fifteen_days_per_day' => $this->user->countTotalLastFifteenDaysPerDay(),
                'contacts_last_fifteen_days_per_day' => $this->contact->countTotalLastFifteenDaysPerDay(),
                'users_per_zone_code' => $this->user->countPerZoneCode(),
                'contacts_per_zone_code' => $this->contact->countPerZoneCode()
            ]
        ]);
        }catch (\Exception $e){
            Response::json(Response::INTERNAL_SERVER_ERROR, [
                'status' => 'error',
                'message' => 'Não foi possível consultar banco de dados',
            ]);
        }
    }

    /**
     * @param $admin_id
     * @return bool
     */
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