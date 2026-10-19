<?php

declare(strict_types=1);

namespace Src\Controller;

use Src\controller\Controller;
use Src\Core\Request;
use Src\Core\Response;
use Src\Model\Usuario;

class HomeController extends Controller
{
    public function __construct(private Usuario $usuario)
    {
    }

    public function home(Request $request)
    {
        // $u = new Usuario();
        // $this->usuario->select();
        // dd($request->param());
        // die();
        parent::view('home');
    }

    public function teste(Request $request, Response $reponse){
        $reponse->redirect("/home/11");
    }
}
