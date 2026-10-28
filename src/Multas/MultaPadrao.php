<?php

namespace App\Multas;

use App\Interfaces\MultaInterface;

class MultaPadrao implements MultaInterface {
    const VALOR_DIA = 2.00;

    public function getNome() {
        return "Multa padrão";
    }

    public function calcularMulta($diasAtraso) {
        if ($diasAtraso <= 0) {
            return 0;
        }

        return round($diasAtraso * self::VALOR_DIA, 2);
    }
}
