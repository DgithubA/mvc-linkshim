<?php

namespace Lms\app\Http\Controllers;

use Lms\Core\Request;

class mainController extends baseController
{
    public function mainScreen(Request $request)
    {

        view('mainScreen');
    }
}