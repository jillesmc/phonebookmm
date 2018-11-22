<?php

namespace Core;

use Firebase\JWT\JWT;

/**
 * Class Auth
 * @package Core
 */
class Auth
{
    private const SECRET = 'M&_C0ntR@t@_M@d&ir@_M@d&ir@_:D';

    private static $userId;

    /**
     *
     * @param $data
     * @return string
     */
    public static function createTokek($data): string
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

    /**
     * @return bool|\stdClass
     */
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

    /**
     * @return string
     */
    public static function getUserId(): string
    {
        return self::$userId;
    }

}