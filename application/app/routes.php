<?php
$route[] = ['GET','/', 'UserController@login'];
$route[] = ['GET','/login', 'UserController@login'];
$route[] = ['GET','/register', 'UserController@register'];
$route[] = ['GET','/logout', 'UserController@logout'];
$route[] = ['POST','/login/auth', 'UserController@auth'];

$route[] = ['GET','/app', 'AppController@index'];

$route[] = ['POST','/users', 'UserController@store'];
$route[] = ['GET','/users/{user_id}', 'UserController@show', 'user-auth'];
$route[] = ['PUT','/users/{user_id}', 'UserController@update', 'user-auth'];

$route[] = ['POST','/users/{user_id}/contacts', 'ContactController@store', 'user-auth'];
$route[] = ['GET','/users/{user_id}/contacts', 'ContactController@index', 'user-auth'];
$route[] = ['GET','/users/{user_id}/contacts/{contact_id}', 'ContactController@show', 'user-auth'];
$route[] = ['PUT','/users/{user_id}/contacts/{contact_id}', 'ContactController@update', 'user-auth'];
$route[] = ['DELETE','/users/{user_id}/contacts/{contact_id}', 'ContactController@destroy', 'user-auth'];

$route[] = ['GET','/admin', 'AdminController@login'];
$route[] = ['GET','/admin/users/{admin_id}', 'AdminController@show', 'admin-auth'];
$route[] = ['GET','/admin/login', 'AdminController@login'];
$route[] = ['GET','/admin/logout', 'AdminController@logout'];
$route[] = ['POST','/admin/login/auth', 'AdminController@auth'];
$route[] = ['GET','/admin/dashboard', 'AdminController@index'];

$route[] = ['GET','/admin/data', 'AdminController@data', 'admin-auth'];

return $route;