<?php

namespace Core;

class Auth
{

    private static $id = null;
    private static $name = null;
    private static $email = null;
    private static $zoneCode = null;
    private static $phone = null;
    private static $createdAt = null;
    private static $updatedAt = null;

    public function __construct()
    {
        if (Session::get('user')) {
            $user = Session::get('user');
            self::$id = $user['id'];
            self::$name = $user['name'];
            self::$email = $user['email'];
            self::$zoneCode = $user['zone_code'];
            self::$phone = $user['phone'];
            self::$createdAt = $user['created_at'];
            self::$updatedAt = $user['updated_at'];
        }
    }

    public static function id()
    {
        return self::$id;
    }

    public static function name()
    {
        return self::$name;
    }

    public static function email()
    {
        return self::$email;
    }

    public static function zoneCode()
    {
        return self::$zoneCode;
    }

    public static function phone()
    {
        return self::$phone;
    }

    public static function createdAt()
    {
        return self::$createdAt;
    }

    public static function updatedAt()
    {
        return self::$updatedAt;
    }

    public static function check()
    {
        if (self::$id == null
            || self::$email == null
            || self::$name == null
            || self::$zoneCode == null
            || self::$phone == null
            || self::$createdAt == null
        ) {
            return false;
        }
        return true;
    }

}