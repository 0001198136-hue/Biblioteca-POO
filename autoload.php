<?php

spl_autoload_register(function ($classe) {
    $caminho = str_replace("App\\", "src/", $classe);
    $caminho = str_replace("\\", "/", $caminho);
    $arquivo = __DIR__ . "/" . $caminho . ".php";

    if (file_exists($arquivo)) {
        require $arquivo;
    }
});
