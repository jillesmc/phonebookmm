<?php

namespace Core;

/**
 * Class Response
 * @package Core
 */
class Response
{
    const OK = 200;
    const CREATED = 201;
    const NO_CONTENT = 204;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const CONFLICT = 409;
    const INTERNAL_SERVER_ERROR = 500;

    /**
     * @param int $statusCode
     * @param array $messageArray
     * @return bool
     */
    public static function json(int $statusCode, array $messageArray = []): bool
    {
        try {
            http_response_code($statusCode);
            header("Content-Type: application/json; charset=UTF-8");
            echo json_encode($messageArray);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

}