<?php
$route[] = ['GET','/', 'UserController@login'];
$route[] = ['GET','/login', 'UserController@login'];
$route[] = ['GET','/logout', 'UserController@logout'];
$route[] = ['POST','/login/auth', 'UserController@auth'];
$route[] = ['GET','/register', 'UserController@register'];
$route[] = ['POST','/users', 'UserController@store'];

$route[] = ['GET','/users/{user_id}', 'UserController@show', 'auth'];
$route[] = ['PUT','/users/{user_id}', 'UserController@update', 'auth'];
$route[] = ['GET','/app', 'AppController@index', 'auth'];

$route[] = ['GET','/users/{user_id}/contacts', 'ContactController@index', 'auth'];
$route[] = ['GET','/users/{user_id}/contacts/{contact_id}', 'ContactController@show', 'auth'];
$route[] = ['POST','/users/{user_id}/contacts', 'ContactController@store', 'auth'];
$route[] = ['PUT','/users/{user_id}/contacts/{contact_id}', 'ContactController@update', 'auth'];
$route[] = ['DELETE','/users/{user_id}/contacts/{contact_id}', 'ContactController@destroy', 'auth'];


$route[] = ['GET','/admin', 'AdminController@login'];
$route[] = ['GET','/admin/login', 'AdminController@login'];
$route[] = ['GET','/admin/logout', 'AdminController@logout'];
$route[] = ['POST','/admin/login/auth', 'AdminController@auth'];
$route[] = ['GET','/admin/dashboard', 'AdminController@index'];
$route[] = ['GET','/admin/data', 'AdminController@data', 'auth-admin'];

return $route;