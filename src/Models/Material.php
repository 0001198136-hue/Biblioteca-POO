<?php

namespace App\Models;

use App\Exceptions\EmprestimoInvalidoException;
use App\Exceptions\MaterialIndisponivelException;
use InvalidArgumentException;

abstract class Material {
    private $codigo;
    private $titulo;
    private $autor;
    private $ano;
    private $exemplares;
    private $totalExemplares;

    public function __construct($codigo, $titulo, $autor, $ano, $exemplares) {
        if ($exemplares < 0) {
            throw new InvalidArgumentException("A quantidade de exemplares não pode ser negativa.");
        }

        $this->codigo = $codigo;
        $this->titulo = $titulo;
        $this->autor = $autor;
        $this->ano = $ano;
        $this->exemplares = $exemplares;
        $this->totalExemplares = $exemplares;
    }

    public function getCodigo() {
        return $this->codigo;
    }

    public function getTitulo() {
        return $this->titulo;
    }

    public function getAutor() {
        return $this->autor;
    }

    public function getAno() {
        return $this->ano;
    }

    public function getExemplares() {
        return $this->exemplares;
    }

    public function getTotalExemplares() {
        return $this->totalExemplares;
    }

    public function getQuantidadeDisponivel() {
        return $this->exemplares;
    }

    public function estaDisponivel() {
        return $this->getQuantidadeDisponivel() > 0;
    }

    public function emprestar() {
        if (!$this->estaDisponivel()) {
            throw new MaterialIndisponivelException("O material " . $this->titulo . " não está disponível: todos os exemplares estão emprestados.");
        }

        $this->exemplares--;
    }

    public function devolver() {
        if ($this->exemplares >= $this->totalExemplares) {
            throw new EmprestimoInvalidoException("Todos os exemplares de " . $this->titulo . " já estão no acervo.");
        }

        $this->exemplares++;
    }

    public function adicionarExemplares($quantidade) {
        if ($quantidade <= 0) {
            throw new InvalidArgumentException("Quantidade inválida para adicionar aos exemplares.");
        }

        $this->exemplares += $quantidade;
        $this->totalExemplares += $quantidade;
    }

    public function exibirDisponibilidade() {
        echo "Exemplares disponíveis: " . $this->exemplares . "\n";
    }

    public function consultarDados() {
        echo "Código: " . $this->codigo . "\n";
        echo "Tipo: " . $this->getTipo() . "\n";
        echo "Título: " . $this->titulo . "\n";
        echo "Autor: " . $this->autor . "\n";
        echo "Ano: " . $this->ano . "\n";
        $this->exibirDisponibilidade();
        echo "Prazo máximo: " . $this->getPrazoMaximo() . " dias\n";
    }

    abstract public function getPrazoMaximo();

    abstract public function getTipo();
}
