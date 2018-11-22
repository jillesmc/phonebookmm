<?php

namespace Core;

use PDO;
use PDOException;

/**
 * Class Database
 * @package Core
 */
class Database
{
    /**
     * @return PDO
     */
    public static function getDataBase(): PDO
    {
        $conf = include __DIR__ . "/../app/database.php";;

        switch ($conf['driver']) {
            case 'sqlite':
                $sqlite = __DIR__ . "/../storage/database/" . $conf['sqlite']['database'];
                $sqlite = "sqlite:" . $sqlite;
                try {
                    $pdo = new PDO($sqlite);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                    return $pdo;
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }
                break;

            case 'mysql':
                $host = $conf['mysql']['host'];
                $db = $conf['mysql']['database'];
                $user = $conf['mysql']['user'];
                $pass = $conf['mysql']['pass'];
                $charset = $conf['mysql']['charset'];
                $collation = $conf['mysql']['collation'];
                try {
                    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES '$charset' COLLATE '$collation'");
                    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                    return $pdo;
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }
                break;
        }
        return null;
    }

}