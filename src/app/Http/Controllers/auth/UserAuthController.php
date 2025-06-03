<?php

namespace Lms\app\Http\Controllers\auth;

use Lms\app\Http\Controllers\baseController;
use Firebase\JWT\JWT;
use Lms\app\Models\User;
use Lms\Core\Database;
use Lms\Core\Request;
use Lms\Core\Response;

class UserAuthController extends baseController
{

    public function login(Request $request)
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = User::where('email', $email)->first();
        if (!$user || $password != $user->password) {
            http_response_code(401);
            exit('Invalid credentials');
        }

        $payload = [
            'iss' => 'lms-app',
            'sub' => $user->id,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + 3600, // 1 hour
        ];
        $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
        $user_id = $user->id;
        $ip_address = $request->getIp();
        $user_agent = $request->getHeader('User_Agent') ?? '';
        $payload_json = json_encode($payload);
        $last_activity = time();
        try {
            $res = Database::getInstance()->query("INSERT INTO `sessions` (`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('$user_id','$ip_address','$user_agent','$payload_json','$last_activity');");
        }catch (\Exception $e){
            Response::error($e->getMessage());
        }
        if($request->acceptJson()){
            Response::json(['token' => $jwt]);
        }else{
            //set session here and redirect to $_GET['redirect_to'] ?? /
            $_SESSION['Authorization'] = "bearer $jwt";
            Response::redirect($_GET['redirect_to'] ?? '/');
        }
    }

    public function signUp(Request $request)
    {
        if(!$request->name or $request->name < 3){
            if($request->acceptJson()){
                Response::json(['error' => 'name is missing']);
            }else Response::error('name is missing');
        }
        if(!$request->email or !filter_var($request->email,FILTER_VALIDATE_EMAIL)){
            if($request->acceptJson()) {
                Response::json(['status' => false, 'error' => 'Invalid email'], Response::HTTP_BAD_REQUEST);
            }else Response::error('Invalid email');
        }

        if(!$request->password or strlen($request->password) < 8){
            if($request->acceptJson()) {
                Response::json(['status'=>false, 'error'=>'Password must be at least 8 characters'], Response::HTTP_BAD_REQUEST);
            }else Response::error('Password must be at least 8 characters');
        }

        if(User::findByEmail($request->email)){
            if($request->acceptJson()) {
                Response::json(['status' => false, 'error'=>'Email already exists'], Response::HTTP_BAD_REQUEST);
            }else Response::error('Email already exists');
        }
        if(User::create(['name'=>$request->name,'email'=>$request->email,'password'=>$request->password])){
            if($request->acceptJson()) {
                Response::json(['status' => true]);
            }else Response::error("User created");
        }else{
            if($request->acceptJson()) {
                Response::json(['status' => false,'error'=>'something went wrong']);
            }else Response::error("something went wrong");
        }
    }
}