<?php

namespace App\Controllers;

use App\Models\Contact;
use App\Models\ContactPhone;
use Core\BaseController;
use Core\Database;
use Core\Response;
use Core\Validator;

class ContactController extends BaseController
{
    private $contact;
    private $phone;

    public function __construct()
    {
        parent::__construct();
        $this->contact = new Contact(Database::getDataBase());
        $this->phone = new ContactPhone(Database::getDataBase());
    }

    public function index($user_id, $request)
    {
        if (isset($request->get->q)) {
            $contactList = $this->contact->searchContacts($user_id, $request->get->q);
        } else {
            $contactList = $this->contact->listUserContacts($user_id);
        }
        if (!$contactList) {
            return Response::json(Response::OK);
        }
        return Response::json(Response::OK, $contactList);
    }

    public function show($user_id, $contact_id)
    {
        $contact = $this->contact->findUserContact($user_id, $contact_id);
        if (!$contact) {
            return Response::json(Response::NOT_FOUND);
        }

        $contact->phones = $this->contact->getContactPhones($contact_id);


        return Response::json(Response::OK, (array)$contact);
    }

    public function store($user_id, $request)
    {
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
                'error' => 'Algo não deu certo na validação',
                'fields' => $errors
            ]);
        }

        if (!empty($phones)) {
            foreach ($phones as $phone) {
                if ($errors = Validator::make($phone, $this->phone->rulesCreate())) {
                    return Response::json(Response::BAD_REQUEST, [
                        'error' => 'Algo não deu certo na validação dos telefones',
                        'fields' => $errors
                    ]);
                }
            }
        }

        try {
            $contact_id = $this->contact->createWithPhones($user_id, $contact, $phones);
            return Response::json(Response::OK, [
                'contactId' => $contact_id
            ]);
        } catch (\Exception $e) {
            return Response::json(Response::INTERNAL_SERVER_ERROR, [
                'error' => 'Algo não deu certo no banco de dados: ' . $e->getCode() . ' => ' . $e->getMessage()
            ]);
        }
    }

    public function update($user_id, $contact_id, $request)
    {
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
                'error' => 'Algo não deu certo na validação',
                'fields' => $errors
            ]);
        }

        if (!empty($phones)) {
            foreach ($phones as $phone) {
                if ($errors = Validator::make($phone, $this->phone->rulesCreate())) {
                    return Response::json(Response::BAD_REQUEST, [
                        'error' => 'Algo não deu certo na validação dos telefones',
                        'fields' => $errors
                    ]);
                }
            }
        }

        try {
            $contact_id = $this->contact->updateWithPhones($user_id, $contact_id, $contact, $phones);
            return Response::json(Response::OK, [
                'contactId' => $contact_id
            ]);
        } catch (\Exception $e) {
            return Response::json(Response::INTERNAL_SERVER_ERROR, [
                'error' => 'Algo não deu certo no banco de dados: ' . $e->getCode() . ' => ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($user_id, $contact_id)
    {
        try {
            $this->contact->deleteUserContact($user_id, $contact_id);
            return Response::json(Response::OK, []);
        } catch (\Exception $e) {
            return Response::json(Response::NOT_FOUND, []);
        }
    }

}