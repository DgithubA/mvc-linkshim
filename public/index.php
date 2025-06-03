<?php
// public/index.php

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/../config/config.php";

$route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_method = $_SERVER['REQUEST_METHOD'];

$Lms = \Lms\Core\Application::getInstance(__DIR__.'/../');

$Lms->setGlobalMiddlewares(['global/TrimInputs','global/ConvertEmptyStringsToNull']);


if($route == '/'){
    $Lms->controller('mainController@mainScreen');
}elseif (str_starts_with($route,'/users')){
    if($route == '/users' and $request_method == 'GET') {
        $Lms->controller('usersController@showAllUsers');
    }elseif(preg_match('/^\/users\/(\d+)$/',$route,$matches) and $request_method == 'GET'){
        $Lms->controller('usersController@getUser', ['id'=>$matches[1]]);
    }elseif(preg_match('/^\/users\/(\d+)$/',$route,$matches) and( $request_method == 'PUT' or $request_method == 'PATCH')){
        $Lms->middleware(['AuthMiddleware'])->controller('api/usersApiController@update', ['id'=>$matches[1]]);
    }elseif ($route == '/users/signup' and $request_method == 'POST') {
        $Lms->controller('auth/UserAuthController@signup');
    }elseif ($route == '/users/login' and $request_method == 'POST') {
        $Lms->controller('auth/UserAuthController@login');
    }

}elseif (str_starts_with($route,'/admin/show-users')){
    $Lms->middleware(['AuthMiddleware','AdminMiddleware'])->controller('adminController@showAllUsers');
}elseif (str_starts_with($route,'/api/users')){
    $Lms->resource('UsersApiController');
}elseif (str_starts_with($route,'/courses')){
    \Lms\Core\Response::html("<b>courses</b>");
}elseif (str_starts_with($route,'/download')){
    if(preg_match('/^\/download\/(.+)$/',$route,$matches)){
        $file = STORAGE_DIR.'private/'.urldecode(basename($matches[1]));
        \Lms\Core\Response::send($file,'inline');
    }
}elseif (str_starts_with($route,'/storage')){
    $file_name = basename($route);
    $file = STORAGE_DIR.'public/'.$file_name;
    serveFile($file);
}


if ($Lms->getRequest()?->acceptJson()){
    \Lms\Core\Response::json(['message'=>'bad request',\Lms\Core\Response::HTTP_BAD_REQUEST]);
}else \Lms\Core\Response::error('bad  request',\Lms\Core\Response::HTTP_BAD_REQUEST);
