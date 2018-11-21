<?php

namespace App\Models;

use Core\BaseModel;

/**
 * Class ContactPhone
 * @package App\Models
 */
class ContactPhone extends BaseModel
{
    protected $table = 'contacts';

    /**
     * @return array
     */
    public function rulesCreate(): array
    {
        return [
            'phone' => 'phone',
        ];
    }

}