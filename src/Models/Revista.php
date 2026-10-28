<?php

namespace App\Models;

class Revista extends Material {
    private $edicao;
    private $volume;
    private $mes;

    public function __construct($codigo, $titulo, $autor, $ano, $exemplares, $edicao, $volume, $mes) {
        parent::__construct($codigo, $titulo, $autor, $ano, $exemplares);

        $this->edicao = $edicao;
        $this->volume = $volume;
        $this->mes = $mes;
    }

    public function getEdicao() {
        return $this->edicao;
    }

    public function getVolume() {
        return $this->volume;
    }

    public function getMes() {
        return $this->mes;
    }

    public function getPrazoMaximo() {
        return 7;
    }

    public function getTipo() {
        return "Revista";
    }

    public function consultarDados() {
        parent::consultarDados();
        echo "Edição: " . $this->edicao . "\n";
        echo "Volume: " . $this->volume . "\n";
        echo "Mês: " . $this->mes . "\n";
        echo "\n";
    }
}
