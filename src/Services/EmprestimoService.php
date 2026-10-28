<?php

namespace App\Services;

use App\Exceptions\EmprestimoInvalidoException;
use App\Exceptions\LimiteEmprestimoException;
use App\Exceptions\MaterialIndisponivelException;
use App\Exceptions\UsuarioBloqueadoException;
use App\Models\Emprestimo;
use App\Models\Historico;
use App\Models\Material;
use App\Models\Usuario;
use DateTime;
use InvalidArgumentException;

class EmprestimoService {
    private $historico;
    private $emprestimos = [];
    private $proximoCodigo = 1;

    public function __construct() {
        $this->historico = new Historico();
    }

    public function getHistorico() {
        return $this->historico;
    }

    public function getEmprestimos() {
        return $this->emprestimos;
    }

    public function validarUsuario(Usuario $usuario) {
        if ($usuario->estaBloqueado()) {
            throw new UsuarioBloqueadoException("O usuário " . $usuario->getNome() . " está bloqueado até pagar a multa de R$ " . number_format($usuario->getMultaPendente(), 2, ",", ".") . ".");
        }

        if ($usuario->getEmprestimosAtivos() >= $usuario->getLimiteEmprestimos()) {
            throw new LimiteEmprestimoException("O usuário " . $usuario->getNome() . " já está com " . $usuario->getEmprestimosAtivos() . " empréstimos e o limite é " . $usuario->getLimiteEmprestimos() . ".");
        }
    }

    public function validarMaterial(Material $material) {
        if (!$material->estaDisponivel()) {
            throw new MaterialIndisponivelException("O material " . $material->getTitulo() . " não está disponível no momento.");
        }
    }

    private function converterData($data, $mensagem) {
        $objeto = DateTime::createFromFormat("!d/m/Y", $data);

        if (!$objeto || $objeto->format("d/m/Y") != $data) {
            throw new EmprestimoInvalidoException($mensagem);
        }

        return $objeto;
    }

    public function calcularDataPrevista(Usuario $usuario, Material $material, $dataEmprestimo) {
        $inicio = $this->converterData($dataEmprestimo, "Data de empréstimo inválida: use o formato dd/mm/aaaa.");

        $prazo = $material->getPrazoMaximo() + $usuario->getPrazoExtra();
        $inicio->modify("+" . $prazo . " days");

        return $inicio->format("d/m/Y");
    }

    public function calcularAtraso($dataPrevista, $dataDevolucao) {
        $prevista = $this->converterData($dataPrevista, "Data prevista inválida.");
        $devolucao = $this->converterData($dataDevolucao, "Data de devolução inválida: use o formato dd/mm/aaaa.");

        if ($devolucao <= $prevista) {
            return 0;
        }

        return $prevista->diff($devolucao)->days;
    }

    public function calcularMulta(Usuario $usuario, $diasAtraso) {
        if ($diasAtraso <= 0) {
            return 0;
        }

        return $usuario->getPoliticaMulta()->calcularMulta($diasAtraso);
    }

    public function realizarEmprestimo(Usuario $usuario, Material $material, $dataEmprestimo) {
        $this->validarUsuario($usuario);
        $this->validarMaterial($material);

        $dataPrevista = $this->calcularDataPrevista($usuario, $material, $dataEmprestimo);

        $emprestimo = new Emprestimo($this->proximoCodigo, $usuario, $material, $dataEmprestimo, $dataPrevista);

        $material->emprestar();
        $usuario->registrarEmprestimo();

        $this->emprestimos[] = $emprestimo;
        $this->proximoCodigo++;

        $this->historico->registrar(Historico::EMPRESTIMO_REALIZADO, $usuario, $material, "Devolução prevista para " . $dataPrevista . ".");

        return $emprestimo;
    }

    public function registrarDevolucao(Emprestimo $emprestimo, $dataDevolucao) {
        $emprestimo->validarDevolucao($dataDevolucao);

        $usuario = $emprestimo->getUsuario();
        $material = $emprestimo->getMaterial();

        $diasAtraso = $this->calcularAtraso($emprestimo->getDataPrevista(), $dataDevolucao);
        $multa = $this->calcularMulta($usuario, $diasAtraso);

        $emprestimo->registrarDevolucao($dataDevolucao, $diasAtraso, $multa);
        $material->devolver();
        $usuario->registrarDevolucao();

        $this->historico->registrar(Historico::DEVOLUCAO_REALIZADA, $usuario, $material, "Devolvido em " . $dataDevolucao . ".");

        if ($diasAtraso > 0) {
            $this->historico->registrar(Historico::DEVOLVIDO_COM_ATRASO, $usuario, $material, $diasAtraso . " dias de atraso.");
        }

        if ($multa > 0) {
            $usuario->adicionarMulta($multa);
            $this->historico->registrar(Historico::MULTA_APLICADA, $usuario, $material, "Multa de R$ " . number_format($multa, 2, ",", ".") . " pela política " . $usuario->getPoliticaMulta()->getNome() . ".");
        }
    }

    public function cancelarEmprestimo(Emprestimo $emprestimo) {
        $emprestimo->cancelar();

        $emprestimo->getMaterial()->devolver();
        $emprestimo->getUsuario()->registrarDevolucao();

        $this->historico->registrar(Historico::EMPRESTIMO_CANCELADO, $emprestimo->getUsuario(), $emprestimo->getMaterial(), "Empréstimo " . $emprestimo->getCodigo() . " cancelado.");
    }

    public function verificarAtrasos($dataAtual) {
        $atual = $this->converterData($dataAtual, "Data inválida: use o formato dd/mm/aaaa.");
        $quantidade = 0;

        foreach ($this->emprestimos as $emprestimo) {
            if ($emprestimo->getStatus() != Emprestimo::ATIVO) {
                continue;
            }

            $prevista = $this->converterData($emprestimo->getDataPrevista(), "Data prevista inválida.");

            if ($atual > $prevista) {
                $emprestimo->marcarAtrasado();
                $quantidade++;
            }
        }

        return $quantidade;
    }

    public function pagarMulta(Usuario $usuario) {
        if ($usuario->getMultaPendente() <= 0) {
            throw new InvalidArgumentException("O usuário " . $usuario->getNome() . " não possui multa pendente.");
        }

        return $usuario->pagarMulta();
    }

    public function totalMultasGeradas() {
        $total = 0;

        foreach ($this->emprestimos as $emprestimo) {
            $total += $emprestimo->getMulta();
        }

        return round($total, 2);
    }
}
