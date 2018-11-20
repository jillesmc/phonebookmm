<?php

namespace Core;

use Firebase\JWT\JWT;

class Auth
{
    private const SECRET = 'M@d&ir@_J0b';

    private static $userId;

    public static function createTokek($data)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 60;
        $token = [
            "iat" => $issuedAt,
            "exp" => $expirationTime + (60 * 60),
            "data" => $data
        ];

        $jwt = JWT::encode($token, self::SECRET);

        return $jwt;
    }

    public static function validateToken()
    {
        try {
            $headers = apache_request_headers();
            if (!isset($headers['Authorization'])) {
                throw new \Exception();
            }
            $authorization = explode(' ', $headers['Authorization']);
            $token = $authorization[1];
            $decoded = JWT::decode($token, self::SECRET, ['HS256']);

            self::$userId = $decoded->data->id;

            return $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getUserId(){
        return self::$userId;
    }

}