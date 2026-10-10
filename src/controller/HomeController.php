<?php

declare(strict_types=1);

namespace Src\Controller;

use Src\controller\Controller;

class HomeController extends Controller{
    public function home(){
        parent::view('home');
    }

    public function sobre(){
        parent::view('sobre');
    }
}

