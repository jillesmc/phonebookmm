<?php

namespace App\Controllers;

use Core\Auth;
use Core\AuthenticateTrait;
use Core\BaseController;
use Core\Container;
use Core\Response;
use Core\Validator;

/**
 * Class UserController
 * @package App\Controllers
 */
class UserController extends BaseController
{
    use AuthenticateTrait;

    /**
     * @var \Core\BaseModel
     */
    private $user;

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->user = Container::getModel('User');
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function show($user_id)
    {
        if (Auth::getUserId() != $user_id) {
            return Response::json(Response::UNAUTHORIZED, [
                'status' => 'error',
                'message' => 'Acesso não autorizado',
            ]);
        }

        $user = $this->user->find($user_id);
        if (!$user) {
            return Response::json(Response::NOT_FOUND, [
                'status' => 'success',
                'message' => 'Contato não encontrado',
            ]);
        }
        return Response::json(Response::OK, [
            'status' => 'success',
            'message' => 'Acesso não autorizado',
            'data' => $user
        ]);
    }

    /**
     * @param $request
     * @return bool
     */
    public function store($request)
    {
        $data = [
            'name' => $request->post->name,
            'email' => $request->post->email,
            'zone_code' => substr($request->post->phone, 0, 2),
            'phone' => $request->post->phone,
            'password' => $request->post->password,
        ];

        if ($errors = Validator::make($data, $this->user->rulesCreate($request->post->email))) {
            return Response::json(Response::CONFLICT, [
                'status' => 'error',
                'message' => 'Algo não deu certo na validação',
                'data' => $errors
            ]);
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        try {
            $this->user->create($data);
            return Response::json(Response::OK, [
                'status' => 'success',
                'message' => 'Usuário criado',
            ]);
        } catch (\Exception $e) {
            return Response::json(Response::INTERNAL_SERVER_ERROR, [
                'status' => 'error',
                'message' => 'Algo não deu certo no banco de dados',
            ]);

        }
    }

    /**
     * @param $user_id
     * @param $request
     * @return bool
     */
    public function update($user_id, $request)
    {
        if (Auth::getUserId() != $user_id) {
            return Response::json(Response::UNAUTHORIZED, [
                'status' => 'error',
                'message' => 'Acesso não autorizado',
            ]);
        }

        $contact = $this->user->find($user_id);

        if (!password_verify($request->post->passwordPrevious, $contact->password)) {
            return Response::json(Response::BAD_REQUEST, [
                'status' => 'error',
                'message' => 'Senha antiga incorreta',
            ]);
        }

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
                'status' => 'error',
                'message' => 'Algo não deu certo na validação',
                'data' => $errors
            ]);
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        try {
            $this->user->update($data, $user_id);

            return Response::json(Response::OK, [
                'status' => 'success',
                'message' => 'Usuário atualizado com sucesso',
            ]);

        } catch (\Exception $e) {
            switch ($e->getCode()) {
                case 23000:
                    return Response::json(Response::CONFLICT, [
                        'status' => 'error',
                        'message' => 'Usuário já existe',
                    ]);
                    break;
                default:
                    return Response::json(Response::INTERNAL_SERVER_ERROR, [
                        'status' => 'error',
                        'message' => 'Algo não deu certo no banco de dados',
                    ]);
                    break;
            }
        }
    }
}