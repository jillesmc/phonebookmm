<?php

namespace App\Models;

use Core\BaseModel;
use Exception;

class Contact extends BaseModel
{
    protected $table = 'contacts';
    protected $phonesTable = 'contacts_phones';

    public function rulesCreate()
    {
        return [
            'name' => 'required',
            'email' => 'email',
        ];
    }

    public function rulesUpdate($id)
    {
        return [
            'name' => 'required',
            'email' => "email",
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
        $query = "SELECT zone_code, count(*) as total from {$this->phonesTable} group by zone_code";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }



    public function findUserContact($user_id, $contact_id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id=:id AND users_id=:users_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $contact_id);
        $stmt->bindValue(':users_id', $user_id);
        $stmt->execute();

        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    public function getContactPhones($contact_id)
    {
        $query = "SELECT * FROM {$this->phonesTable} WHERE contacts_id=:contacts_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':contacts_id', $contact_id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }

    public function listUserContacts($user_id)
    {
        $query = "SELECT " .
            "{$this->table}.id, {$this->table}.name, {$this->table}.email, "
            . "{$this->table}.note, {$this->phonesTable}.phone "
            . "FROM {$this->table} LEFT JOIN {$this->phonesTable} "
            . "ON {$this->table}.id = {$this->phonesTable}.contacts_id "
            . "WHERE users_id=:users_id GROUP BY {$this->table}.id ORDER BY {$this->table}.name";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":users_id", $user_id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }

    public function searchContacts($user_id, $search_query)
    {

        $query = "SELECT " .
            "{$this->table}.id, {$this->table}.name, {$this->table}.email, "
            . "{$this->table}.note, {$this->phonesTable}.phone "
            . "FROM {$this->table} LEFT JOIN {$this->phonesTable} "
            . "ON {$this->table}.id = {$this->phonesTable}.contacts_id "
            . "WHERE ("
            . "{$this->table}.name LIKE :name "
            . "OR {$this->table}.email LIKE :email "
            . "OR {$this->phonesTable}.phone LIKE :phone "
            . ") AND users_id=:users_id GROUP BY {$this->table}.id ORDER BY {$this->table}.name";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":users_id", $user_id);
        $stmt->bindValue(":name", "%{$search_query}%");
        $stmt->bindValue(":email", "%{$search_query}%");
        $stmt->bindValue(":phone", "%{$search_query}%");
        $stmt->execute();

        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }

    public function createWithPhones($users_id, array $contact, array $phones = [])
    {
        $this->pdo->beginTransaction();

        try {
            $data = $this->prepareDataInsert($contact);
            $query = "INSERT INTO {$this->table} (users_id, {$data[0]}) VALUES (:users_id, {$data[1]})";
            $stmt = $this->pdo->prepare($query);

            $stmt->bindValue(":users_id", $users_id);
            for ($i = 0; $i < count($data[2]); $i++) {
                $stmt->bindValue("{$data[2][$i]}", $data[3][$i]);
            }
            $stmt->execute();

            $contacts_id = $this->pdo->lastInsertId();
            if (!empty($phones)) {
                foreach ($phones as $phone) {
                    $this->createRelatedPhone($contacts_id, $phone);
                }
            }

            $this->pdo->commit();
            $stmt->closeCursor();

            return $contacts_id;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update(array $data, $id)
    {
        $data = $this->prepareDataUpdate($data);
        $query = "UPDATE {$this->table} SET {$data[0]},updated_at=NOW() WHERE id=:id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $id);
        for ($i = 0; $i < count($data[1]); $i++) {
            $stmt->bindValue("{$data[1][$i]}", $data[2][$i]);
        }
        $result = $stmt->execute();
        $stmt->closeCursor();
        return $result;

    }

    public function updateWithPhones($users_id, $contact_id, array $contact, array $phones = [])
    {
        $this->pdo->beginTransaction();
        try {
            $data = $this->prepareDataUpdate($contact);
            $query = "UPDATE {$this->table} SET {$data[0]},updated_at=NOW() WHERE id=:id AND users_id=:users_id";
            $stmt = $this->pdo->prepare($query);

            $stmt->bindValue(":id", $contact_id);
            $stmt->bindValue(":users_id", $users_id);
            for ($i = 0; $i < count($data[1]); $i++) {
                $stmt->bindValue("{$data[1][$i]}", $data[2][$i]);
            }
            $stmt->execute();

            if (!empty($phones)) {
                $this->deleteRelatedPhones($contact_id);
                foreach ($phones as $phone) {
                    $this->createRelatedPhone($contact_id, $phone);
                }
            }

            $this->pdo->commit();
            $stmt->closeCursor();

            return $contact_id;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function deleteRelatedPhones($contacts_id)
    {
        $query = "DELETE FROM {$this->phonesTable} WHERE contacts_id=:contacts_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":contacts_id", $contacts_id);
        $stmt->execute();
    }

    private function createRelatedPhone($contacts_id, array $phones)
    {
        $data = $this->prepareDataInsert($phones);

        $query = "INSERT INTO {$this->phonesTable} (contacts_id, {$data[0]}) VALUES (:contacts_id, {$data[1]})";
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(":contacts_id", $contacts_id);
        for ($i = 0; $i < count($data[2]); $i++) {
            $stmt->bindValue("{$data[2][$i]}", $data[3][$i]);
        }

        $stmt->execute();
    }

    public function deleteUserContact($user_id, $contact_id)
    {
        $query = "DELETE FROM {$this->table} WHERE id=:id and users_id=:users_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":id", $contact_id);
        $stmt->bindValue(":users_id", $user_id);
        $result = $stmt->execute();
        $stmt->closeCursor();
        return $result;
    }


}