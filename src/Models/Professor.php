<?php

namespace App\Models;

use App\Multas\MultaProfessor;

class Professor extends Usuario {
    private $departamento;

    public function __construct($matricula, $nome, $email, $telefone, $departamento) {
        parent::__construct($matricula, $nome, $email, $telefone);

        $this->departamento = $departamento;
    }

    public function getDepartamento() {
        return $this->departamento;
    }

    public function getLimiteEmprestimos() {
        return 5;
    }

    public function getPrazoExtra() {
        return 7;
    }

    public function getPoliticaMulta() {
        return new MultaProfessor();
    }

    public function getTipo() {
        return "Professor";
    }

    public function consultarDados() {
        parent::consultarDados();
        echo "Departamento: " . $this->departamento . "\n";
        echo "\n";
    }
}
