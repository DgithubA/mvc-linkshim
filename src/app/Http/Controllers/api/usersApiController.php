<?php

namespace Lms\app\Http\Controllers\api;

use Lms\app\Http\Controllers\baseController;
use Lms\Core\Request;
use Lms\Core\Response;

class usersApiController extends baseController
{
    public function update(Request $request, string $id)
    {
        $change = [];
        if(!is_null($request->name)){
            $change['name'] = $request->name;
        }

        if(!empty($change)){
            if($request->user()->update(array_merge(['id'=>$id],$change))){
                if($request->acceptJson()){
                    Response::json(['status'=>true,'message'=>'User has been updated.']);
                }else Response::text('User has been updated.');
            }else{
                if($request->acceptJson()){
                    Response::json(['status'=>false,'message'=>'User could not be updated.']);
                }else Response::text('User could not be updated.');
            }
        }else{
            if($request->acceptJson()){
                Response::json(['status'=>false,'message'=>'empty body']);
            }else Response::text('empty body');
        }
    }
}