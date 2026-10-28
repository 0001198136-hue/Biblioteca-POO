<?php

namespace App\Models;

class Historico {
    const EMPRESTIMO_REALIZADO = "Empréstimo realizado";
    const DEVOLUCAO_REALIZADA = "Devolução realizada";
    const DEVOLVIDO_COM_ATRASO = "Material devolvido com atraso";
    const MULTA_APLICADA = "Multa aplicada";
    const EMPRESTIMO_CANCELADO = "Empréstimo cancelado";

    private $registros = [];

    public function registrar($acao, Usuario $usuario, Material $material, $observacao) {
        $this->registros[] = [
            "data" => date("d/m/Y"),
            "hora" => date("H:i:s"),
            "acao" => $acao,
            "usuario" => $usuario,
            "material" => $material,
            "observacao" => $observacao
        ];
    }

    public function getRegistros() {
        return $this->registros;
    }

    public function contar() {
        return count($this->registros);
    }

    public function buscarPorUsuario(Usuario $usuario) {
        $encontrados = [];

        foreach ($this->registros as $registro) {
            if ($registro["usuario"]->getMatricula() == $usuario->getMatricula()) {
                $encontrados[] = $registro;
            }
        }

        return $encontrados;
    }

    public function buscarPorAcao($acao) {
        $encontrados = [];

        foreach ($this->registros as $registro) {
            if ($registro["acao"] == $acao) {
                $encontrados[] = $registro;
            }
        }

        return $encontrados;
    }

    public function exibirPorUsuario(Usuario $usuario) {
        echo "HISTÓRICO DE " . $usuario->getNome() . "\n";
        echo "\n";

        $registros = $this->buscarPorUsuario($usuario);

        if (count($registros) == 0) {
            echo "Nenhum evento registrado.\n";
            echo "\n";
            return;
        }

        foreach ($registros as $registro) {
            echo $registro["data"] . " " . $registro["hora"] . " | " . $registro["acao"] . "\n";
            echo "Material: " . $registro["material"]->getTitulo() . "\n";
            echo "Observação: " . $registro["observacao"] . "\n";
            echo "\n";
        }
    }
}
