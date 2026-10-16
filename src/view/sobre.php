<?php

echo 'Pagina sobre';

?>


<form action="/sobre" method="POST">
    <label for="nome">Nome: </label>
    <input type="text" name="nome" id="nome">
    <label for="idade">Idade: </label>
    <input type="number" name="idade" id="idade">
    <button type="submit">Enviar</button>
</form>

<a href="/">Ir pra home</a>