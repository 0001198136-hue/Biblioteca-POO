<?php

namespace App\Models;

use App\Multas\MultaPadrao;

class Aluno extends Usuario {
    private $curso;

    public function __construct($matricula, $nome, $email, $telefone, $curso) {
        parent::__construct($matricula, $nome, $email, $telefone);

        $this->curso = $curso;
    }

    public function getCurso() {
        return $this->curso;
    }

    public function getLimiteEmprestimos() {
        return 3;
    }

    public function getPrazoExtra() {
        return 0;
    }

    public function getPoliticaMulta() {
        return new MultaPadrao();
    }

    public function getTipo() {
        return "Aluno";
    }

    public function consultarDados() {
        parent::consultarDados();
        echo "Curso: " . $this->curso . "\n";
        echo "\n";
    }
}
