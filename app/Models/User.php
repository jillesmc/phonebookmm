<?php

namespace App\Models;

use Core\BaseModel;

class User extends BaseModel
{
    protected $table = 'users';

    public function rulesCreate()
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:User:email',
            'phone' => 'phone',
            'password' => 'required',
        ];
    }

    public function rulesUpdate($id)
    {
        return [
            'name' => 'required',
            'email' => "required|email|unique:User:email:$id",
            'phone' => 'phone',
            'password' => 'required',
        ];
    }

    public function countTotal()
    {
        $query = "SELECT count(*) as total FROM {$this->table}";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    public function countTotalLastMonth()
    {
        $query = "SELECT count(*) as total FROM {$this->table} where created_at >= (CURDATE() - INTERVAL 1 MONTH)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    public function countTotalLastFifteenDaysPerDay()
    {
        $query = "SELECT DATE_FORMAT(created_at, '%d/%m') as day, count(*) as total "
            ."FROM {$this->table} "
            ."where created_at >= (CURDATE() - INTERVAL 15 DAY ) "
            ."GROUP BY DATE_FORMAT(created_at, '%m%d') "
            ."ORDER BY created_at";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }

    public function countPerZoneCode()
    {
        $query = "SELECT zone_code, count(*) as total from {$this->table} group by zone_code";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }


}