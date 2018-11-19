<?php

namespace Core;

trait AuthenticateAdmin
{
    public function login()
    {
        $this->setPageTitle('ADM');
        return $this->renderView('/adm/login', 'layout-admin-login');
    }

    public function logout(){
        Session::destroy('admin');
        return Redirect::route('/admin/login');
    }

    public function auth($request)
    {
        $result = Container::getModel('Admin')
            ->findByField('email', $request->post->email);

        if($result && password_verify($request->post->password, $result->password)){
            $admin = [
                'id' => $result->id,
                'email' => $result->email,
            ];

            Session::set('admin', $admin);

            return Redirect::route('/admin/dashboard');
        }

        return Response::json(Response::FORBIDDEN, [
            'error' => 'Usuário não autorizado!'
        ]);
    }
}