<?php

namespace App\Models;

use Core\BaseModel;

class User extends BaseModel
{
    protected $table = 'users';

    public function rules(){
        return [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'phone',
            'password' => 'required',
        ];
    }

}