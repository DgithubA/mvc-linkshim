<?php

namespace Lms\app\Http\Controllers;


use Lms\app\Models\User;

class adminController extends baseController
{
    public function showAllUsers(){
        $users = User::all(except: ['password']);
        view('Users/show-all-users',['users'=>$users]);
    }

    public function getUser(int $id){
        $user = User::find($id)->except(columns: ['password'])->to_array();
        /*ob_start();
        var_dump($user);
        $output = ob_get_clean();*/
        json($user);
    }
}
