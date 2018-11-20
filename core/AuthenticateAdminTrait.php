<?php

namespace Core;

trait AuthenticateAdminTrait
{
    public function login()
    {
        $this->setPageTitle('ADM');
        return $this->renderView('/adm/login', 'layout-admin-login');
    }

    public function logout(){
        return Redirect::route('/admin/login');
    }

    public function forbidden()
    {
        return Response::json(Response::UNAUTHORIZED, [
            'code' => Response::UNAUTHORIZED,
            'status' => 'failed',
            'message' => 'Usuário não autorizado'
        ]);
    }

    public function auth($request)
    {
        $result = Container::getModel('Admin')
            ->findByField('email', $request->post->email);

        if($result && $result->email === $request->post->email
            &&password_verify($request->post->password, $result->password)){
            $adminData = [
                'id' => $result->id
            ];

            $jwt = Auth::createTokek($adminData);

            $data = [
                'admin' => $adminData,
                'jwt' => $jwt
            ];

            return Response::json(Response::OK, [
                'status' => 'success',
                'message' => 'Credenciais de login válidas',
                'data' => $data
            ]);
        }

        return Response::json(Response::FORBIDDEN, [
            'status' => 'error',
            'message' => 'Credenciais de login inválidas'
        ]);
    }
}