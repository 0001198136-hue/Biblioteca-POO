<?php

require __DIR__ . "/autoload.php";

use App\Exceptions\EmprestimoInvalidoException;
use App\Exceptions\LimiteEmprestimoException;
use App\Exceptions\MaterialIndisponivelException;
use App\Exceptions\UsuarioBloqueadoException;
use App\Models\Aluno;
use App\Models\EBook;
use App\Models\Livro;
use App\Models\Professor;
use App\Models\Revista;
use App\Services\EmprestimoService;

$service = new EmprestimoService();

$ak = new Livro(1, "Como Desmontar um AK em 30 Segundos Volume 2", "Tião Culatra", 2019, 3, "978-85-0014-002-7", 320, "Editora Cano Curto", "Manual Duvidoso");
$glocks = new Livro(2, "Rua dos Tiros 14+1: Guia do Bairro Glocks Filhos", "Zé Cano Curto", 2021, 2, "978-85-0014-141-5", 150, "Editora Só Bala Perdida", "Guia de Bairro");
$ovo = new Livro(3, "Receitas de Quem Só Sabe Fritar Ovo", "Dona Gasolina", 1998, 1, "978-85-0014-003-4", 80, "Editora Panela Velha", "Culinária");
$revista = new Revista(4, "Revista Tiguer 7 Mensal", "Editora Só Tem 7 no Mundo", 2026, 2, 145, 12, "Outubro");
$ebook = new EBook(5, "Manual do Tiguer 7 Que Só Anda Quando Quer", "Dona Gasolina", 2024, "PDF", 12, "https://biblioteca.com/downloads/manual-tiguer-7", 10);

$zeGlock = new Aluno("A001", "Zé do Glock", "naomandegmail@gmail.com", "(11) 91234-5678", "Balística Aplicada");
$tiao = new Aluno("A002", "Tião Tiguer", "tiaotiguer7@gmail.com", "(11) 98888-7777", "Engenharia de Tiro ao Alvo");
$barriga = new Aluno("A003", "Seu Barriga", "seubarriga@gmail.com", "(11) 97777-1414", "Cobrança de Aluguel");
$pardal = new Professor("P001", "Professor Pardal da Silva", "professorpardal@gmail.com", "(11) 95555-0007", "Departamento de Munição e Afins");

echo "Acervo\n";
echo "\n";
$ak->consultarDados();
$glocks->consultarDados();
$ovo->consultarDados();
$revista->consultarDados();
$ebook->consultarDados();

echo "Usuários\n";
echo "\n";
$zeGlock->consultarDados();
$pardal->consultarDados();

echo "Cenário 1: empréstimo do aluno\n";
echo "\n";
try {
    $empZe = $service->realizarEmprestimo($zeGlock, $ak, "01/11/2026");
    $empZe->exibirResumo();
    echo "Exemplares do AK no acervo: " . $ak->getExemplares() . "\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Cenário 2: empréstimo do professor (7 dias a mais)\n";
echo "\n";
try {
    $empPardal = $service->realizarEmprestimo($pardal, $ak, "01/11/2026");
    $empPardal->exibirResumo();
    echo "Exemplares do AK no acervo: " . $ak->getExemplares() . "\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Cenário 3: devolução do aluno com 5 dias de atraso\n";
echo "\n";
try {
    $service->registrarDevolucao($empZe, "20/11/2026");
    $empZe->exibirDevolucao();
    echo "Exemplares do AK no acervo: " . $ak->getExemplares() . "\n";
    echo "Status do Zé do Glock: " . $zeGlock->getStatus() . "\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 1: material sem exemplares disponíveis\n";
try {
    $empOvo = $service->realizarEmprestimo($tiao, $ovo, "01/11/2026");
    echo "Tião Tiguer pegou o " . $ovo->getTitulo() . "\n";
    echo "Exemplares sobrando: " . $ovo->getExemplares() . "\n";

    $service->realizarEmprestimo($pardal, $ovo, "02/11/2026");
} catch (MaterialIndisponivelException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 2: quarto empréstimo para aluno com três ativos\n";
try {
    $empRevista = $service->realizarEmprestimo($tiao, $revista, "01/11/2026");
    $empEbook = $service->realizarEmprestimo($tiao, $ebook, "01/11/2026");
    echo "Empréstimos ativos do Tião Tiguer: " . $tiao->getEmprestimosAtivos() . "\n";

    $service->realizarEmprestimo($tiao, $glocks, "01/11/2026");
} catch (LimiteEmprestimoException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 3: usuário bloqueado tentando pegar material\n";
try {
    echo "Status do Zé do Glock: " . $zeGlock->getStatus() . "\n";
    $service->realizarEmprestimo($zeGlock, $glocks, "21/11/2026");
} catch (UsuarioBloqueadoException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 4: devolver um empréstimo já devolvido\n";
try {
    $service->registrarDevolucao($empZe, "22/11/2026");
} catch (EmprestimoInvalidoException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 5: passar do limite de acessos simultâneos do e-book\n";
try {
    for ($i = 1; $i <= 9; $i++) {
        $leitor = new Professor("L00" . $i, "Leitor Fantasma " . $i, "leitor" . $i . "@gmail.com", "(11) 90000-000" . $i, "Departamento de Assombração");
        $service->realizarEmprestimo($leitor, $ebook, "01/11/2026");
    }

    echo "Acessos simultâneos em uso: " . $ebook->getAcessosAtuais() . " de " . $ebook->getMaxAcessos() . "\n";
    echo "Exemplares físicos do e-book: " . $ebook->getExemplares() . "\n";

    $service->realizarEmprestimo($barriga, $ebook, "01/11/2026");
} catch (MaterialIndisponivelException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 6: devolução dentro do prazo\n";
echo "\n";
try {
    $service->registrarDevolucao($empPardal, "20/11/2026");
    $empPardal->exibirDevolucao();
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 7: devolução com atraso\n";
echo "\n";
try {
    $service->registrarDevolucao($empRevista, "10/11/2026");
    $empRevista->exibirDevolucao();
    echo "Status do Tião Tiguer: " . $tiao->getStatus() . "\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 8: multa do aluno contra multa do professor com o mesmo atraso\n";
echo "\n";
try {
    $empAluno = $service->realizarEmprestimo($barriga, $glocks, "01/12/2026");
    $empProf = $service->realizarEmprestimo($pardal, $glocks, "01/12/2026");

    echo "Prevista do aluno: " . $empAluno->getDataPrevista() . "\n";
    echo "Prevista do professor: " . $empProf->getDataPrevista() . "\n";
    echo "\n";

    $service->registrarDevolucao($empAluno, "20/12/2026");
    $service->registrarDevolucao($empProf, "27/12/2026");

    echo "Seu Barriga (" . $barriga->getPoliticaMulta()->getNome() . "): " . $empAluno->getDiasAtraso() . " dias, R$ " . number_format($empAluno->getMulta(), 2, ",", ".") . "\n";
    echo "Professor Pardal (" . $pardal->getPoliticaMulta()->getNome() . "): " . $empProf->getDiasAtraso() . " dias, R$ " . number_format($empProf->getMulta(), 2, ",", ".") . "\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 9: marcar empréstimos como atrasados\n";
try {
    $quantidade = $service->verificarAtrasos("16/11/2026");
    echo "Empréstimos marcados como atrasados: " . $quantidade . "\n";
    echo "Status do empréstimo " . $empOvo->getCodigo() . ": " . $empOvo->getStatus() . "\n";
    echo "Status do empréstimo " . $empEbook->getCodigo() . ": " . $empEbook->getStatus() . "\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 10: cancelar empréstimo e tentar devolver\n";
try {
    $service->cancelarEmprestimo($empEbook);
    echo "Status do empréstimo " . $empEbook->getCodigo() . ": " . $empEbook->getStatus() . "\n";
    echo "Acessos simultâneos em uso: " . $ebook->getAcessosAtuais() . " de " . $ebook->getMaxAcessos() . "\n";

    $service->registrarDevolucao($empEbook, "05/11/2026");
} catch (EmprestimoInvalidoException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 11: pagar a multa e voltar a pegar material\n";
try {
    $valorPago = $service->pagarMulta($zeGlock);
    echo "Zé do Glock pagou R$ " . number_format($valorPago, 2, ",", ".") . " de multa.\n";
    echo "Status do Zé do Glock: " . $zeGlock->getStatus() . "\n";

    $empNovo = $service->realizarEmprestimo($zeGlock, $glocks, "21/11/2026");
    echo "Devolução prevista do novo empréstimo: " . $empNovo->getDataPrevista() . "\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

echo "Teste 12: dados inválidos\n";
try {
    $service->realizarEmprestimo($zeGlock, $revista, "ontem");
} catch (EmprestimoInvalidoException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Exemplares da revista continuam: " . $revista->getExemplares() . "\n";
}

try {
    $service->registrarDevolucao($empNovo, "01/01/2026");
} catch (EmprestimoInvalidoException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

try {
    $aluno4 = new Aluno("A004", "Chaves do Oito", "chavesdooito.com", "(11) 90000-0008", "Barril Aplicado");
} catch (InvalidArgumentException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

try {
    $livroRuim = new Livro(6, "Glock-18 Sem Estoque", "Seu Madruga", 2020, -3, "978-85-0000-000-0", 100, "Editora Vila", "Comédia");
} catch (InvalidArgumentException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

try {
    $ebookRuim = new EBook(7, "Receita de Churros Infinitos", "Dona Florinda", 2022, "PDF", 3, "https://biblioteca.com/downloads/churros", 0);
} catch (InvalidArgumentException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\n";

$service->getHistorico()->exibirPorUsuario($zeGlock);

echo "Eventos registrados no histórico: " . $service->getHistorico()->contar() . "\n";
echo "Total de multas geradas na biblioteca: R$ " . number_format($service->totalMultasGeradas(), 2, ",", ".") . "\n";
