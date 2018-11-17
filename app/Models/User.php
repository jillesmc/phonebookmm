<?php

namespace App\Models;

use Core\BaseModel;

class User extends BaseModel
{
    protected $table = 'users';

    public function rulesCreate(){
        return [
            'name' => 'required',
            'email' => 'required|email|unique:User:email',
            'phone' => 'phone',
            'password' => 'required',
        ];
    }

    public function rulesUpdate($id){
        return [
            'name' => 'required',
            'email' => "required|email|unique:User:email:$id",
            'phone' => 'phone',
            'password' => 'required',
        ];
    }



}