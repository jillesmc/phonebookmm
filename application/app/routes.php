<?php
// user routes
$route[] = ['GET','/', 'UserController@login'];
$route[] = ['GET','/login', 'UserController@login'];
$route[] = ['GET','/register', 'UserController@register'];
$route[] = ['GET','/logout', 'UserController@logout'];
$route[] = ['GET','/app', 'AppController@index'];

// admin routes
$route[] = ['GET','/admin', 'AdminController@login'];
$route[] = ['GET','/admin/login', 'AdminController@login'];
$route[] = ['GET','/admin/logout', 'AdminController@logout'];
$route[] = ['GET','/admin/dashboard', 'AdminController@index'];

// user api routes
$route[] = ['POST','/api/login/auth', 'UserController@auth'];
$route[] = ['POST','/api/users', 'UserController@store'];
$route[] = ['GET','/api/users/{user_id}', 'UserController@show', 'user-auth'];
$route[] = ['PUT','/api/users/{user_id}', 'UserController@update', 'user-auth'];

// contact api routes
$route[] = ['POST','/api/users/{user_id}/contacts', 'ContactController@store', 'user-auth'];
$route[] = ['GET','/api/users/{user_id}/contacts', 'ContactController@index', 'user-auth'];
$route[] = ['GET','/api/users/{user_id}/contacts/{contact_id}', 'ContactController@show', 'user-auth'];
$route[] = ['PUT','/api/users/{user_id}/contacts/{contact_id}', 'ContactController@update', 'user-auth'];
$route[] = ['DELETE','/api/users/{user_id}/contacts/{contact_id}', 'ContactController@destroy', 'user-auth'];

// admin api routes
$route[] = ['POST','/api/admin/login/auth', 'AdminController@auth'];
$route[] = ['GET','/api/admin/users/{admin_id}', 'AdminController@show', 'admin-auth'];
$route[] = ['GET','/api/admin/data', 'AdminController@data', 'admin-auth'];

return $route;