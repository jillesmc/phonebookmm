<?php

namespace Core;

use PDO;

/**
 * Class BaseModel
 * @package Core
 */
abstract class BaseModel
{
    protected $pdo;
    protected $table;

    /**
     * BaseModel constructor.
     * @param $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return array of stdClass
     */
    public function all(): array
    {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find(string $id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id=:id LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    /**
     * @param $field
     * @param $value
     * @return mixed
     */
    public function findByField(string $field, string $value)
    {
        $query = "SELECT * FROM {$this->table} WHERE {$field}=:{$field} LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":{$field}", $value);
        $stmt->execute();
        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function create(array $data): bool
    {
        $data = $this->prepareDataInsert($data);
        $query = "INSERT INTO {$this->table} ({$data[0]}) VALUES ({$data[1]})";
        $stmt = $this->pdo->prepare($query);

        for ($i = 0; $i < count($data[2]); $i++) {
            $stmt->bindValue("{$data[2][$i]}", $data[3][$i]);
        }

        $result = $stmt->execute();
        $stmt->closeCursor();
        return $result;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareDataInsert(array $data): array
    {
        $strKeys = "";
        $strBinds = "";
        $binds = [];
        $values = [];

        foreach ($data as $key => $value) {
            $strKeys = "{$strKeys}, {$key}";
            $strBinds = "{$strBinds},:{$key}";
            $binds[] = ":{$key}";
            $values[] = $value;
        }

        $strKeys = substr($strKeys, 1);
        $strBinds = substr($strBinds, 1);

        return [
            $strKeys,
            $strBinds,
            $binds,
            $values
        ];
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
     * @param array $data
     * @return array
     */
    protected function prepareDataUpdate(array $data): array
    {
        $strKeysBinds = "";
        $binds = [];
        $values = [];

        foreach ($data as $key => $value) {
            $strKeysBinds = "{$strKeysBinds},{$key}=:{$key}";
            $binds[] = ":{$key}";
            $values[] = $value;
        }
        $strKeysBinds = substr($strKeysBinds, 1);

        return [
            $strKeysBinds,
            $binds,
            $values
        ];
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        $query = "DELETE FROM {$this->table} WHERE id=:id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":id", $id);
        $result = $stmt->execute();
        $stmt->closeCursor();
        return $result;
    }
}