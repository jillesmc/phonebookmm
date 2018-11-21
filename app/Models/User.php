<?php

namespace App\Models;

use Core\BaseModel;

/**
 * Class User
 * @package App\Models
 */
class User extends BaseModel
{
    protected $table = 'users';

    /**
     * @return array
     */
    public function rulesCreate(string $id): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:User:email:$id',
            'phone' => 'phone',
            'password' => 'required',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function rulesUpdate(string $id): array
    {
        return [
            'name' => 'required',
            'email' => "required|email|unique:User:email:$id",
            'phone' => 'phone',
            'password' => 'required',
        ];
    }

    /**
     * @return mixed
     */
    public function countTotal()
    {
        $query = "SELECT count(*) as total FROM {$this->table} LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    /**
     * @return mixed
     */
    public function countTotalLastMonth()
    {
        $query = "SELECT count(*) as total FROM {$this->table} where created_at >= (CURDATE() - INTERVAL 1 MONTH) LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    /**
     * @return array
     */
    public function countTotalLastFifteenDaysPerDay(): array
    {
        $query = "SELECT DATE_FORMAT(created_at, '%d/%m') as day, count(*) as total "
            . "FROM {$this->table} "
            . "where created_at >= (CURDATE() - INTERVAL 15 DAY ) "
            . "GROUP BY DATE_FORMAT(created_at, '%m%d') "
            . "ORDER BY created_at";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }

    /**
     * @return array
     */
    public function countPerZoneCode(): array
    {
        $query = "SELECT zone_code, count(*) as total from {$this->table} group by zone_code";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }


}