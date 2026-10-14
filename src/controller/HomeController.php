<?php

declare(strict_types=1);

namespace Src\Controller;

use Src\controller\Controller;
use Src\Core\Request;

class HomeController extends Controller
{
    public function home()
    {
        parent::view('home');

        // $request->param();
    }

    public function create()
    {
        echo 'POST';
    }

    public function sobre()
    {
        parent::view('sobre');
    }

    public function parametro(Request $request)
    {
        dd($request->param());
    }

    public function teste(Request $request){
        $dados = $request->post();
        
        echo $dados['nome'] . "<br>";
        echo $dados['idade'];
    }
}
