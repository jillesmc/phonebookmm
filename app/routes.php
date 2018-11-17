<?php
$route[] = ['GET','/', 'UserController@login'];
$route[] = ['GET','/users/login', 'UserController@login'];
$route[] = ['GET','/users/register', 'UserController@register'];
$route[] = ['POST','/users', 'UserController@store'];
$route[] = ['PUT','/users/{user_id}', 'UserController@update'];

return $route;