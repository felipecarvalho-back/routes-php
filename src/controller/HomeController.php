<?php

declare(strict_types=1);

namespace Src\Controller;

use Src\controller\Controller;
use Src\Model\Usuario;

class HomeController extends Controller
{
    public function home()
    {
        // $u = new Usuario();
        // $u->select();
        parent::view('home');
    }
}
