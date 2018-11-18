<?php

namespace Core;

class Response
{
    CONST OK = 200;
    CONST CREATED = 201;
    CONST NO_CONTENT = 204; //PUT, POST AND DELETE WITH NO RESPONSE MESSAGE BODY
    CONST BAD_REQUEST = 400;
    CONST UNAUTHORIZED = 401; //FORGOT SOME CREDENTIAL INFORMATION
    CONST FORBIDDEN = 403;
    CONST NOT_FOUND = 404;
    CONST METHOD_NOT_ALLOWED = 405;
    CONST CONFLICT = 409;
    CONST INTERNAL_SERVER_ERROR= 500;

    public static function json($statusCode, array $messageArray = [])
    {
        http_response_code($statusCode);
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($messageArray);
        return true;
    }

}