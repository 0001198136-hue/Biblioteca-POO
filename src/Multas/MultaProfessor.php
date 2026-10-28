<?php

namespace App\Multas;

use App\Interfaces\MultaInterface;

class MultaProfessor implements MultaInterface {
    const VALOR_DIA = 1.00;

    public function getNome() {
        return "Multa de professor";
    }

    public function calcularMulta($diasAtraso) {
        if ($diasAtraso <= 0) {
            return 0;
        }

        return round($diasAtraso * self::VALOR_DIA, 2);
    }
}
