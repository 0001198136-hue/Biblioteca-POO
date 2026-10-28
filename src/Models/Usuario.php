<?php

namespace App\Models;

use InvalidArgumentException;

abstract class Usuario {
    const ATIVO = "ATIVO";
    const BLOQUEADO = "BLOQUEADO";

    private $matricula;
    private $nome;
    private $email;
    private $telefone;
    private $status;
    private $multaPendente = 0;
    private $emprestimosAtivos = 0;

    public function __construct($matricula, $nome, $email, $telefone) {
        $this->matricula = $matricula;
        $this->nome = $nome;
        $this->telefone = $telefone;
        $this->status = self::ATIVO;
        $this->setEmail($email);
    }

    public function getMatricula() {
        return $this->matricula;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("E-mail inválido.");
        }

        $this->email = $email;
    }

    public function getTelefone() {
        return $this->telefone;
    }

    public function getStatus() {
        return $this->status;
    }

    public function estaBloqueado() {
        return $this->status == self::BLOQUEADO;
    }

    public function getMultaPendente() {
        return $this->multaPendente;
    }

    public function getEmprestimosAtivos() {
        return $this->emprestimosAtivos;
    }

    public function podeEmprestar() {
        return !$this->estaBloqueado() && $this->emprestimosAtivos < $this->getLimiteEmprestimos();
    }

    public function registrarEmprestimo() {
        $this->emprestimosAtivos++;
    }

    public function registrarDevolucao() {
        if ($this->emprestimosAtivos > 0) {
            $this->emprestimosAtivos--;
        }
    }

    public function adicionarMulta($valor) {
        if ($valor <= 0) {
            throw new InvalidArgumentException("O valor da multa deve ser maior que zero.");
        }

        $this->multaPendente = round($this->multaPendente + $valor, 2);
        $this->status = self::BLOQUEADO;
    }

    public function pagarMulta() {
        $valor = $this->multaPendente;

        $this->multaPendente = 0;
        $this->status = self::ATIVO;

        return $valor;
    }

    public function consultarDados() {
        echo "Matrícula: " . $this->matricula . "\n";
        echo "Tipo: " . $this->getTipo() . "\n";
        echo "Nome: " . $this->nome . "\n";
        echo "E-mail: " . $this->email . "\n";
        echo "Telefone: " . $this->telefone . "\n";
        echo "Status: " . $this->status . "\n";
        echo "Limite de empréstimos: " . $this->getLimiteEmprestimos() . "\n";
        echo "Empréstimos ativos: " . $this->emprestimosAtivos . "\n";
    }

    abstract public function getLimiteEmprestimos();

    abstract public function getPrazoExtra();

    abstract public function getPoliticaMulta();

    abstract public function getTipo();
}
