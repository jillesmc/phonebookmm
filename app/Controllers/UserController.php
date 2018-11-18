<?php

namespace App\Controllers;

use App\Models\User;
use Core\Authenticate;
use Core\BaseController;
use Core\Database;
use Core\Response;
use Core\Validator;

class UserController extends BaseController
{
    use Authenticate;

    private $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = new User(Database::getDataBase());
    }

    public function show($user_id)
    {

    }

    public function store($request)
    {
        $data = [
            'name' => $request->post->name,
            'email' => $request->post->email,
            'zone_code' => substr($request->post->phone, 0, 2),
            'phone' => $request->post->phone,
            'password' => $request->post->password,
        ];

        if ($errors = Validator::make($data, $this->user->rulesCreate())) {
            return Response::json(Response::BAD_REQUEST, [
                'error' => 'Algo não deu certo na validação'
            ]);
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        try {
            $this->user->create($data);
        } catch (\Exception $e) {
            return Response::json(Response::INTERNAL_SERVER_ERROR, [
                'error' => 'Algo não deu certo no banco de dados: ' . $e->getCode() . ' => ' . $e->getMessage()
            ]);
        }
    }

    public function update($user_id, $request)
    {
        $data = [
            'id' => $user_id,
            'name' => $request->post->name,
            'email' => $request->post->email,
            'zone_code' => substr($request->post->phone, 0, 2),
            'phone' => $request->post->phone,
            'password' => $request->post->password,
        ];

        if ($errors = Validator::make($data, $this->user->rulesUpdate($user_id))) {
            return Response::json(Response::BAD_REQUEST, [
                'error' => $errors
            ]);
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        try {
            $this->user->update($data, $user_id);
        } catch (\Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    return Response::json(Response::CONFLICT, [
                        'error' => 'Usuário já existe'
                    ]);
                    break;
                default:
                    return Response::json(Response::INTERNAL_SERVER_ERROR, [
                        'error' => 'Algo não deu certo no banco de dados: ' . $e->getCode() . ' => ' . $e->getMessage()
                    ]);
                    break;
            }

        }

    }


}