<?php

namespace Core;


/**
 * Class Redirect
 * @package Core
 */
class Redirect
{
    /**
     * @param string $url
     * @param array $with
     */
    public static function route(string $url, array $with = [])
    {
        if (count($with) > 0) {
            foreach ($with as $key => $value) {
                Session::set($key, $value);
            }
        }
        return header("location:$url");
    }
}