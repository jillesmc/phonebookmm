<?php

namespace App\Models;

use Core\BaseModel;
use Exception;

/**
 * Class Contact
 * @package App\Models
 */
class Contact extends BaseModel
{
    protected $table = 'contacts';
    protected $phonesTable = 'contacts_phones';

    /**
     * @return array
     */
    public function rulesCreate()
    {
        return [
            'name' => 'required',
            'email' => 'email',
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function rulesUpdate($id)
    {
        return [
            'name' => 'required',
            'email' => "email",
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
        $query = "SELECT zone_code, count(*) as total from {$this->phonesTable} group by zone_code";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }


    /**
     * @param $user_id
     * @param $contact_id
     * @return mixed
     */
    public function findUserContact($user_id, $contact_id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id=:id AND users_id=:users_id LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $contact_id);
        $stmt->bindValue(':users_id', $user_id);
        $stmt->execute();

        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    /**
     * @param $contact_id
     * @return array
     */
    public function getContactPhones($contact_id): array
    {
        $query = "SELECT * FROM {$this->phonesTable} WHERE contacts_id=:contacts_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':contacts_id', $contact_id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }

    /**
     * @param $user_id
     * @return array
     */
    public function listUserContacts($user_id): array
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

    /**
     * @param $user_id
     * @param $search_query
     * @return array
     */
    public function searchContacts($user_id, $search_query): array
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

    /**
     * @param $users_id
     * @param array $contact
     * @param array $phones
     * @return string
     * @throws Exception
     */
    public function createWithPhones(string $users_id, array $contact, array $phones = []): string
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

    /**
     * @param array $data
     * @param $id
     * @return bool
     */
    public function update(array $data, string $id): bool
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

    /**
     * @param $users_id
     * @param $contact_id
     * @param array $contact
     * @param array $phones
     * @return string
     * @throws Exception
     */
    public function updateWithPhones(string $users_id, string $contact_id, array $contact, array $phones = []): string
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

    /**
     * @param $contacts_id
     * @return bool
     */
    private function deleteRelatedPhones(string $contacts_id): bool
    {
        $query = "DELETE FROM {$this->phonesTable} WHERE contacts_id=:contacts_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":contacts_id", $contacts_id);
        $result = $stmt->execute();
        $stmt->closeCursor();
        return $result;
    }

    /**
     * @param $contacts_id
     * @param array $phones
     * @return bool
     */
    private function createRelatedPhone(string $contacts_id, array $phones): bool
    {
        $data = $this->prepareDataInsert($phones);

        $query = "INSERT INTO {$this->phonesTable} (contacts_id, {$data[0]}) VALUES (:contacts_id, {$data[1]})";
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(":contacts_id", $contacts_id);
        for ($i = 0; $i < count($data[2]); $i++) {
            $stmt->bindValue("{$data[2][$i]}", $data[3][$i]);
        }

        $result = $stmt->execute();
        $stmt->closeCursor();
        return $result;
    }

    /**
     * @param $user_id
     * @param $contact_id
     * @return bool
     */
    public function deleteUserContact(string $user_id, string $contact_id): bool
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