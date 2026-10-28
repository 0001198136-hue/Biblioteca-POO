<?php

namespace App\Models;

use App\Exceptions\EmprestimoInvalidoException;
use App\Exceptions\MaterialIndisponivelException;
use InvalidArgumentException;

class EBook extends Material {
    private $formato;
    private $tamanhoArquivo;
    private $linkAcesso;
    private $maxAcessos;
    private $acessosAtuais = 0;

    public function __construct($codigo, $titulo, $autor, $ano, $formato, $tamanhoArquivo, $linkAcesso, $maxAcessos) {
        if ($maxAcessos <= 0) {
            throw new InvalidArgumentException("O número máximo de acessos simultâneos precisa ser maior que zero.");
        }

        parent::__construct($codigo, $titulo, $autor, $ano, 0);

        $this->formato = $formato;
        $this->tamanhoArquivo = $tamanhoArquivo;
        $this->linkAcesso = $linkAcesso;
        $this->maxAcessos = $maxAcessos;
    }

    public function getFormato() {
        return $this->formato;
    }

    public function getTamanhoArquivo() {
        return $this->tamanhoArquivo;
    }

    public function getLinkAcesso() {
        return $this->linkAcesso;
    }

    public function getMaxAcessos() {
        return $this->maxAcessos;
    }

    public function getAcessosAtuais() {
        return $this->acessosAtuais;
    }

    public function getQuantidadeDisponivel() {
        return $this->maxAcessos - $this->acessosAtuais;
    }

    public function emprestar() {
        if (!$this->estaDisponivel()) {
            throw new MaterialIndisponivelException("O e-book " . $this->getTitulo() . " atingiu o limite de " . $this->maxAcessos . " acessos simultâneos.");
        }

        $this->acessosAtuais++;
    }

    public function devolver() {
        if ($this->acessosAtuais <= 0) {
            throw new EmprestimoInvalidoException("O e-book " . $this->getTitulo() . " não possui acessos em andamento.");
        }

        $this->acessosAtuais--;
    }

    public function exibirDisponibilidade() {
        echo "Acessos simultâneos: " . $this->acessosAtuais . " de " . $this->maxAcessos . "\n";
    }

    public function getPrazoMaximo() {
        return 21;
    }

    public function getTipo() {
        return "E-book";
    }

    public function consultarDados() {
        parent::consultarDados();
        echo "Formato: " . $this->formato . "\n";
        echo "Tamanho do arquivo: " . $this->tamanhoArquivo . " MB\n";
        echo "Link de acesso: " . $this->linkAcesso . "\n";
        echo "\n";
    }
}
