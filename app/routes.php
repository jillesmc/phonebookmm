<?php
$route[] = ['GET','/', 'UserController@login'];
$route[] = ['GET','/login', 'UserController@login'];
$route[] = ['POST','/login/auth', 'UserController@auth'];
$route[] = ['GET','/register', 'UserController@register'];
$route[] = ['POST','/users', 'UserController@store'];

$route[] = ['PUT','/users/{user_id}', 'UserController@update', 'auth'];
$route[] = ['GET','/app', 'AppController@index', 'auth'];

return $route;