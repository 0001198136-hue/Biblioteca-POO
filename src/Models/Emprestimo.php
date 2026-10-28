<?php

namespace App\Models;

use App\Exceptions\EmprestimoInvalidoException;
use DateTime;

class Emprestimo {
    const ATIVO = "ATIVO";
    const DEVOLVIDO = "DEVOLVIDO";
    const ATRASADO = "ATRASADO";
    const CANCELADO = "CANCELADO";

    private $codigo;
    private $usuario;
    private $material;
    private $dataEmprestimo;
    private $dataPrevista;
    private $dataDevolucao = null;
    private $diasAtraso = 0;
    private $multa = 0;
    private $status;

    public function __construct($codigo, Usuario $usuario, Material $material, $dataEmprestimo, $dataPrevista) {
        $this->codigo = $codigo;
        $this->usuario = $usuario;
        $this->material = $material;
        $this->dataEmprestimo = $dataEmprestimo;
        $this->dataPrevista = $dataPrevista;
        $this->status = self::ATIVO;
    }

    public function getCodigo() {
        return $this->codigo;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function getMaterial() {
        return $this->material;
    }

    public function getDataEmprestimo() {
        return $this->dataEmprestimo;
    }

    public function getDataPrevista() {
        return $this->dataPrevista;
    }

    public function getDataDevolucao() {
        return $this->dataDevolucao;
    }

    public function getDiasAtraso() {
        return $this->diasAtraso;
    }

    public function getMulta() {
        return $this->multa;
    }

    public function getStatus() {
        return $this->status;
    }

    public function estaEmAberto() {
        return $this->status == self::ATIVO || $this->status == self::ATRASADO;
    }

    public function validarDevolucao($dataDevolucao) {
        if ($this->status == self::CANCELADO) {
            throw new EmprestimoInvalidoException("O empréstimo " . $this->codigo . " foi cancelado e não pode ser devolvido.");
        }

        if ($this->status == self::DEVOLVIDO) {
            throw new EmprestimoInvalidoException("O empréstimo " . $this->codigo . " já foi devolvido.");
        }

        $devolucao = DateTime::createFromFormat("!d/m/Y", $dataDevolucao);
        $inicio = DateTime::createFromFormat("!d/m/Y", $this->dataEmprestimo);

        if (!$devolucao) {
            throw new EmprestimoInvalidoException("Data de devolução inválida: use o formato dd/mm/aaaa.");
        }

        if ($devolucao < $inicio) {
            throw new EmprestimoInvalidoException("A data de devolução não pode ser anterior à data do empréstimo.");
        }
    }

    public function registrarDevolucao($dataDevolucao, $diasAtraso, $multa) {
        $this->validarDevolucao($dataDevolucao);

        $this->dataDevolucao = $dataDevolucao;
        $this->diasAtraso = $diasAtraso;
        $this->multa = $multa;
        $this->status = self::DEVOLVIDO;
    }

    public function marcarAtrasado() {
        if ($this->status == self::ATIVO) {
            $this->status = self::ATRASADO;
        }
    }

    public function cancelar() {
        if (!$this->estaEmAberto()) {
            throw new EmprestimoInvalidoException("O empréstimo " . $this->codigo . " está com status " . $this->status . " e não pode ser cancelado.");
        }

        $this->status = self::CANCELADO;
    }

    public function exibirResumo() {
        echo "EMPRÉSTIMO Nº " . $this->codigo . "\n";
        echo "\n";
        echo "Usuário: " . $this->usuario->getNome() . "\n";
        echo "Tipo: " . $this->usuario->getTipo() . "\n";
        echo "Material: " . $this->material->getTitulo() . " (" . $this->material->getTipo() . ")\n";
        echo "Data do empréstimo: " . $this->dataEmprestimo . "\n";
        echo "Data prevista: " . $this->dataPrevista . "\n";
        echo "Status: " . $this->status . "\n";
        echo "\n";
    }

    public function exibirDevolucao() {
        echo "DEVOLUÇÃO DO EMPRÉSTIMO Nº " . $this->codigo . "\n";
        echo "\n";
        echo "Usuário: " . $this->usuario->getNome() . "\n";
        echo "Material: " . $this->material->getTitulo() . "\n";
        echo "Data prevista: " . $this->dataPrevista . "\n";
        echo "Devolvido em: " . $this->dataDevolucao . "\n";
        echo "Dias de atraso: " . $this->diasAtraso . "\n";
        echo "Multa: R$ " . number_format($this->multa, 2, ",", ".") . "\n";
        echo "Status: " . $this->status . "\n";
        echo "\n";
    }
}
