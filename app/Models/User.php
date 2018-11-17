<?php

namespace App\Models;

use Core\BaseModel;

class User extends BaseModel
{
    protected $table = 'users';

    public function find($id){
        $query = "SELECT * FROM {$this->table} where id={$id}";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }
}