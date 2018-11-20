<?php

namespace Core;

trait AuthenticateTrait
{
    public function login()
    {
        $this->setPageTitle('Entrar');
        return $this->renderView('/user/login', 'layout-login');
    }

    public function register()
    {
        $this->setPageTitle('Registrar');
        return $this->renderView('/user/register', 'layout-register');
    }

    public function logout()
    {
        return Redirect::route('/login');
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
        $result = Container::getModel('User')
            ->findByField('email', $request->post->email);

        if ($result && $result->email === $request->post->email
            && password_verify($request->post->password, $result->password)) {

            $userData = [
                'id' => $result->id,
                'name' => $result->name,
                'email' => $result->email,
                'role' => 'user'
            ];

            $jwt = Auth::createTokek($userData);

            $data = [
                'user' => $userData,
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