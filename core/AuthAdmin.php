<?php

namespace Core;

class AuthAdmin
{

    private static $id = null;
    private static $email = null;

    public function __construct()
    {
        if (Session::get('admin')) {
            $user = Session::get('admin');
            self::$id = $user['id'];
            self::$email = $user['email'];
        }
    }

    public static function id()
    {
        return self::$id;
    }

    public static function email()
    {
        return self::$email;
    }

    public static function check()
    {
        if (self::$id == null
            || self::$email == null
        ) {
            return false;
        }
        return true;
    }

}