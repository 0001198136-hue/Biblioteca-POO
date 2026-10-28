<?php

namespace App\Models;

class Livro extends Material {
    private $isbn;
    private $paginas;
    private $editora;
    private $genero;

    public function __construct($codigo, $titulo, $autor, $ano, $exemplares, $isbn, $paginas, $editora, $genero) {
        parent::__construct($codigo, $titulo, $autor, $ano, $exemplares);

        $this->isbn = $isbn;
        $this->paginas = $paginas;
        $this->editora = $editora;
        $this->genero = $genero;
    }

    public function getIsbn() {
        return $this->isbn;
    }

    public function getPaginas() {
        return $this->paginas;
    }

    public function getEditora() {
        return $this->editora;
    }

    public function getGenero() {
        return $this->genero;
    }

    public function getPrazoMaximo() {
        return 14;
    }

    public function getTipo() {
        return "Livro";
    }

    public function consultarDados() {
        parent::consultarDados();
        echo "ISBN: " . $this->isbn . "\n";
        echo "Páginas: " . $this->paginas . "\n";
        echo "Editora: " . $this->editora . "\n";
        echo "Gênero: " . $this->genero . "\n";
        echo "\n";
    }
}
