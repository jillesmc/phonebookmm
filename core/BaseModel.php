<?php

namespace Core;

use PDO;

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

    public function all()
    {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        return $result;
    }

    public function find($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id=:id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    public function findByField($field, $value)
    {
        $query = "SELECT * FROM {$this->table} WHERE {$field}=:{$field}";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":{$field}", $value);
        $stmt->execute();
        $result = $stmt->fetch();
        $stmt->closeCursor();
        return $result;
    }

    public function create(array $data)
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

    protected function prepareDataInsert(array $data)
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

    protected function prepareDataUpdate(array $data)
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

    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id=:id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":id", $id);
        $result = $stmt->execute();
        $stmt->closeCursor();
        return $result;
    }
}