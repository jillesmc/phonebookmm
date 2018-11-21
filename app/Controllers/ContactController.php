<?php

namespace App\Controllers;

use Core\Auth;
use Core\AuthenticateTrait;
use Core\BaseController;
use Core\Container;
use Core\Response;
use Core\Validator;

/**
 * Class ContactController
 * @package App\Controllers
 */
class ContactController extends BaseController
{
    use AuthenticateTrait;
    /**
     * @var \Core\BaseModel
     */
    private $contact;
    /**
     * @var \Core\BaseModel
     */
    private $phone;

    /**
     * ContactController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->contact = Container::getModel('Contact');
        $this->phone = Container::getModel('ContactPhone');
    }

    /**
     * @param $user_id
     * @param $request
     * @return bool
     */
    public function index($user_id, $request)
    {
        if (Auth::getUserId() != $user_id) {
            return Response::json(Response::UNAUTHORIZED, [
                'status' => 'error',
                'message' => 'Acesso não autorizado',
            ]);
        }

        if (isset($request->get->q)) {
            $contactList = $this->contact->searchContacts($user_id, $request->get->q);
        } else {
            $contactList = $this->contact->listUserContacts($user_id);
        }
        if (!$contactList) {
            return Response::json(Response::NOT_FOUND, [
                'status' => 'error',
                'message' => 'Nenhum contato encontrado'
            ]);
        }
        return Response::json(Response::OK, [
            'status' => 'success',
            'data' => $contactList
        ]);
    }

    /**
     * @param $user_id
     * @param $contact_id
     * @return bool
     */
    public function show($user_id, $contact_id)
    {
        $contact = $this->contact->findUserContact($user_id, $contact_id);
        if (!$contact) {
            return Response::json(Response::NOT_FOUND, [
                'status' => 'error',
                'message' => 'Contato não encontrado'
            ]);
        }

        $contact->phones = $this->contact->getContactPhones($contact_id);

        return Response::json(Response::OK, [
            'status' => 'success',
            'data' => $contact
        ]);

    }

    /**
     * @param $user_id
     * @param $request
     * @return bool
     */
    public function store($user_id, $request)
    {
        if (Auth::getUserId() != $user_id) {
            return Response::json(Response::UNAUTHORIZED, [
                'status' => 'error',
                'message' => 'Acesso não autorizado',
            ]);
        }

        $contact = [
            'name' => $request->post->name,
            'email' => $request->post->email,
            'note' => $request->post->note,
        ];

        $phones = [];
        if (!empty($request->post->phones)) {

            foreach ($request->post->phones as $phone) {
                if ($phone != null && $phone !== '') {
                    $phones[] = [
                        'zone_code' => substr($phone, 0, 2),
                        'phone' => $phone
                    ];
                }
            }
        }

        if ($errors = Validator::make($contact, $this->contact->rulesCreate())) {
            return Response::json(Response::BAD_REQUEST, [
                'status' => 'error',
                'message' => 'Algo não deu certo na validação',
                'data' => $errors
            ]);
        }

        if (!empty($phones)) {
            foreach ($phones as $phone) {
                if ($errors = Validator::make($phone, $this->phone->rulesCreate())) {
                    return Response::json(Response::BAD_REQUEST, [
                        'status' => 'error',
                        'message' => 'Algo não deu certo na validação dos telefones',
                        'data' => $errors
                    ]);
                }
            }
        }

        try {
            $contact_id = $this->contact->createWithPhones($user_id, $contact, $phones);

            return Response::json(Response::OK, [
                'status' => 'success',
                'data' => [
                    'contactId' => $contact_id
                ]
            ]);

        } catch (\Exception $e) {
            return Response::json(Response::INTERNAL_SERVER_ERROR, [
                'error' => 'Algo não deu certo no banco de dados: ' . $e->getCode() . ' => ' . $e->getMessage()
            ]);
        }
    }

    /**
     * @param $user_id
     * @param $contact_id
     * @param $request
     * @return bool
     */
    public function update($user_id, $contact_id, $request)
    {
        if (Auth::getUserId() != $user_id) {
            return Response::json(Response::UNAUTHORIZED, [
                'status' => 'error',
                'message' => 'Acesso não autorizado',
            ]);
        }

        $contact = [
            'name' => $request->post->name,
            'email' => $request->post->email,
            'note' => $request->post->note,
        ];

        $phones = [];
        if (!empty($request->post->phones)) {

            foreach ($request->post->phones as $phone) {
                if ($phone != null && $phone !== '') {
                    $phones[] = [
                        'zone_code' => substr($phone, 0, 2),
                        'phone' => $phone
                    ];
                }
            }
        }

        if ($errors = Validator::make($contact, $this->contact->rulesCreate())) {
            return Response::json(Response::BAD_REQUEST, [
                'status' => 'error',
                'message' => 'Algo não deu certo na validação',
                'data' => $errors
            ]);
        }

        if (!empty($phones)) {
            foreach ($phones as $phone) {
                if ($errors = Validator::make($phone, $this->phone->rulesCreate())) {
                    return Response::json(Response::BAD_REQUEST, [
                        'status' => 'error',
                        'message' => 'Algo não deu certo na validação dos telefones',
                        'data' => $errors
                    ]);
                }
            }
        }

        try {
            $contact_id = $this->contact->updateWithPhones($user_id, $contact_id, $contact, $phones);

            return Response::json(Response::OK, [
                'status' => 'success',
                'message' => 'Contato atualizado com sucesso',
                'data' => [
                    'contactId' => $contact_id
                ]
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
     * @param $contact_id
     * @return bool
     */
    public function destroy($user_id, $contact_id)
    {
        if (Auth::getUserId() != $user_id) {
            return Response::json(Response::UNAUTHORIZED, [
                'status' => 'error',
                'message' => 'Acesso não autorizado',
            ]);
        }

        try {
            $this->contact->deleteUserContact($user_id, $contact_id);
            return Response::json(Response::OK, [
                'status' => 'success',
                'message' => 'Contato deletado com sucesso',
            ]);
        } catch (\Exception $e) {
            return Response::json(Response::INTERNAL_SERVER_ERROR, [
                'status' => 'error',
                'message' => 'Algo não deu certo no banco de dados',
            ]);
        }
    }

}