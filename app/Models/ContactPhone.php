<?php

namespace App\Models;

use Core\BaseModel;

class ContactPhone extends BaseModel
{
    protected $table = 'contacts';

    public function rulesCreate(){
        return [
            'phone' => 'phone',
        ];
    }

    public function rulesUpdate($id){
        return [
            'phone' => 'phone',
        ];
    }

}