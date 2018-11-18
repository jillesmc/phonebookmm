<?php

namespace Core;

trait Authenticate
{
    public function login()
    {
        // @to-do mudar isso aqui para JWT
//        if (Session::get('auth')) {
//            Redirect::route('/app');
//        }

        $this->setPageTitle('Entrar');
        return $this->renderView('/user/login', 'layout-login');
    }

    public function register()
    {
        // @to-do mudar isso aqui para JWT
//        if(Session::get('user')){
//            Redirect::route('/app');
//        }

        $this->setPageTitle('Registrar');
        return $this->renderView('/user/register', 'layout-register');
    }

    public function logout(){
        Session::destroy('auth');
        Session::destroy('user');
        return Redirect::route('/login');
    }

    public function auth($request)
    {
        $result = Container::getModel('User')
            ->findByField('email', $request->post->email);

        if($result && password_verify($request->post->password, $result->password)){
            $user = [
                'id' => $result->id,
                'name' => $result->name,
                'email' => $result->email,
                'zone_code' => $result->zone_code,
                'phone' => $result->phone,
                'created_at' => $result->created_at,
                'updated_at' => $result->updated_at,
            ];

            Session::set('user', $user);

            return Redirect::route('/app');
        }

        return Response::json(Response::FORBIDDEN, [
            'error' => 'Usuário não autorizado!'
        ]);
    }
}