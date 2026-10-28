# Biblioteca POO

Projeto da atividade prática de PHP com Programação Orientada a Objetos. É uma biblioteca onde dá para emprestar livros, revistas e e-books para alunos e professores, devolver, cancelar e cobrar multa quando passa do prazo.

## Objetivo

Praticar POO em PHP usando encapsulamento, herança, abstração, polimorfismo, interfaces, composição, tratamento de exceções, namespaces e autoload.

## Descrição

O sistema controla materiais (livros, revistas e e-books), usuários (alunos e professores), empréstimos e o histórico de eventos. As regras de emprestar, devolver, cancelar e pagar multa ficam no `EmprestimoService`, e o `index.php` só monta os objetos e chama os métodos.

Prazos máximos: livro 14 dias, revista 7 dias e e-book 21 dias. O professor ganha 7 dias a mais em qualquer material. O aluno pode ficar com até 3 empréstimos ativos e o professor com até 5.

A multa do aluno é de R$ 2,00 por dia de atraso e a do professor é de R$ 1,00 por dia.

O e-book não tem exemplar físico. Quem controla ele é o número máximo de acessos simultâneos: cada empréstimo ocupa um acesso e a devolução libera.

As datas são passadas como texto no formato dd/mm/aaaa, assim o resultado da execução é sempre o mesmo.

## Requisitos

- PHP 7.4 ou superior
- Terminal (git bash, cmd ou outro) para rodar o comando

## Estrutura das pastas

```
biblioteca-poo/
├── src/
│   ├── Models/
│   │   ├── Material.php
│   │   ├── Livro.php
│   │   ├── Revista.php
│   │   ├── EBook.php
│   │   ├── Usuario.php
│   │   ├── Aluno.php
│   │   ├── Professor.php
│   │   ├── Emprestimo.php
│   │   └── Historico.php
│   ├── Interfaces/
│   │   └── MultaInterface.php
│   ├── Multas/
│   │   ├── MultaPadrao.php
│   │   └── MultaProfessor.php
│   ├── Services/
│   │   └── EmprestimoService.php
│   └── Exceptions/
│       ├── MaterialIndisponivelException.php
│       ├── LimiteEmprestimoException.php
│       ├── UsuarioBloqueadoException.php
│       └── EmprestimoInvalidoException.php
├── autoload.php
├── diagrama.puml
├── index.php
└── README.md
```

Além da estrutura pedida, criei o `autoload.php` para carregar as classes e o `diagrama.puml` com o diagrama de classes.

## Classes implementadas

- `Material` (abstrata), `Livro`, `Revista` e `EBook`
- `Usuario` (abstrata), `Aluno` e `Professor`
- `Emprestimo` e `Historico`
- `MultaInterface`, `MultaPadrao` e `MultaProfessor`
- `EmprestimoService`
- `MaterialIndisponivelException`, `LimiteEmprestimoException`, `UsuarioBloqueadoException` e `EmprestimoInvalidoException`

## Regras de negócio

1. Material sem exemplar disponível não pode ser emprestado (`Material`)
2. Usuário no limite de empréstimos não pega outro material (`EmprestimoService`)
3. Empréstimo já devolvido não pode ser devolvido de novo (`Emprestimo`)
4. Empréstimo cancelado não pode ser devolvido (`Emprestimo`)
5. O estoque físico é atualizado no empréstimo, na devolução e no cancelamento (`Material`)
6. E-book controla acessos simultâneos e não mexe no estoque físico (`EBook`)
7. O prazo considera o tipo do material (`Livro`, `Revista` e `EBook`)
8. Professor recebe 7 dias a mais no prazo (`Professor`)
9. A multa só é calculada quando há atraso (`EmprestimoService`)
10. Usuário bloqueado não faz empréstimo (`EmprestimoService`)
11. O número de exemplares disponíveis nunca fica negativo (`Material`)
12. Os acessos simultâneos do e-book não passam do limite (`EBook`)

Regras extras: e-mail precisa ser válido, as datas precisam estar em dd/mm/aaaa, a devolução não pode ser antes do empréstimo e o usuário só desbloqueia depois de pagar a multa.

## Conceitos de POO utilizados

**Encapsulamento:** todos os atributos são `private` e só são acessados por getters. Os exemplares do `Material` só mudam pelos métodos `emprestar()`, `devolver()` e `adicionarExemplares()`, que validam antes. O status e a multa pendente do `Usuario` não têm setter: só mudam por `adicionarMulta()` e `pagarMulta()`. O status do `Emprestimo` só muda por `registrarDevolucao()`, `marcarAtrasado()` e `cancelar()`.

**Herança:** `Livro`, `Revista` e `EBook` herdam de `Material`. `Aluno` e `Professor` herdam de `Usuario`. As quatro exceções personalizadas herdam de `Exception`.

**Abstração:** `Material` e `Usuario` são abstratas, então não dá para fazer `new Material()` nem `new Usuario()`. O `Material` obriga as filhas a implementarem `getPrazoMaximo()` e `getTipo()`. O `Usuario` obriga a implementarem `getLimiteEmprestimos()`, `getPrazoExtra()`, `getPoliticaMulta()` e `getTipo()`.

**Polimorfismo:** o `EmprestimoService` chama `getPrazoMaximo()` sem saber se o material é livro, revista ou e-book, e chama `getPoliticaMulta()->calcularMulta()` sem saber se o usuário é aluno ou professor. O `EBook` também sobrescreve `emprestar()` e `devolver()`, então o mesmo comando mexe em exemplares físicos no livro e em acessos no e-book.

**Composição:**
- `Emprestimo` → `Usuario` e `Material`: o empréstimo guarda quem pegou e o que foi pego.
- `EmprestimoService` → `Historico`: o service cria o histórico e é dono dele.
- `EmprestimoService` → `Emprestimo`: o service guarda todos os empréstimos criados.

**Interface:** a `MultaInterface` é o contrato das multas: toda classe que implementa ela precisa ter `calcularMulta()` e `getNome()`. É isso que deixa trocar a regra de multa sem mexer no `Emprestimo` nem no `EmprestimoService`.

## Como executar

Precisa do PHP instalado. Dentro da pasta `biblioteca-poo`:

```
php index.php
```

Para salvar o resultado da execução em um arquivo:

```
php index.php > evidencias.txt
```

## Diagrama UML

O diagrama de classes está no arquivo `diagrama.puml`, que pode ser aberto no PlantUML (site plantuml.com ou extensão do VS Code).

## Testes realizados

Resultado da execução:

```
Acervo

Código: 1
Tipo: Livro
Título: Como Desmontar um AK em 30 Segundos Volume 2
Autor: Tião Culatra
Ano: 2019
Exemplares disponíveis: 3
Prazo máximo: 14 dias
ISBN: 978-85-0014-002-7
Páginas: 320
Editora: Editora Cano Curto
Gênero: Manual Duvidoso

Código: 2
Tipo: Livro
Título: Rua dos Tiros 14+1: Guia do Bairro Glocks Filhos
Autor: Zé Cano Curto
Ano: 2021
Exemplares disponíveis: 2
Prazo máximo: 14 dias
ISBN: 978-85-0014-141-5
Páginas: 150
Editora: Editora Só Bala Perdida
Gênero: Guia de Bairro

Código: 3
Tipo: Livro
Título: Receitas de Quem Só Sabe Fritar Ovo
Autor: Dona Gasolina
Ano: 1998
Exemplares disponíveis: 1
Prazo máximo: 14 dias
ISBN: 978-85-0014-003-4
Páginas: 80
Editora: Editora Panela Velha
Gênero: Culinária

Código: 4
Tipo: Revista
Título: Revista Tiguer 7 Mensal
Autor: Editora Só Tem 7 no Mundo
Ano: 2026
Exemplares disponíveis: 2
Prazo máximo: 7 dias
Edição: 145
Volume: 12
Mês: Outubro

Código: 5
Tipo: E-book
Título: Manual do Tiguer 7 Que Só Anda Quando Quer
Autor: Dona Gasolina
Ano: 2024
Acessos simultâneos: 0 de 10
Prazo máximo: 21 dias
Formato: PDF
Tamanho do arquivo: 12 MB
Link de acesso: https://biblioteca.com/downloads/manual-tiguer-7

Usuários

Matrícula: A001
Tipo: Aluno
Nome: Zé do Glock
E-mail: naomandegmail@gmail.com
Telefone: (11) 91234-5678
Status: ATIVO
Limite de empréstimos: 3
Empréstimos ativos: 0
Curso: Balística Aplicada

Matrícula: P001
Tipo: Professor
Nome: Professor Pardal da Silva
E-mail: professorpardal@gmail.com
Telefone: (11) 95555-0007
Status: ATIVO
Limite de empréstimos: 5
Empréstimos ativos: 0
Departamento: Departamento de Munição e Afins

Cenário 1: empréstimo do aluno

EMPRÉSTIMO Nº 1

Usuário: Zé do Glock
Tipo: Aluno
Material: Como Desmontar um AK em 30 Segundos Volume 2 (Livro)
Data do empréstimo: 01/11/2026
Data prevista: 15/11/2026
Status: ATIVO

Exemplares do AK no acervo: 2

Cenário 2: empréstimo do professor (7 dias a mais)

EMPRÉSTIMO Nº 2

Usuário: Professor Pardal da Silva
Tipo: Professor
Material: Como Desmontar um AK em 30 Segundos Volume 2 (Livro)
Data do empréstimo: 01/11/2026
Data prevista: 22/11/2026
Status: ATIVO

Exemplares do AK no acervo: 1

Cenário 3: devolução do aluno com 5 dias de atraso

DEVOLUÇÃO DO EMPRÉSTIMO Nº 1

Usuário: Zé do Glock
Material: Como Desmontar um AK em 30 Segundos Volume 2
Data prevista: 15/11/2026
Devolvido em: 20/11/2026
Dias de atraso: 5
Multa: R$ 10,00
Status: DEVOLVIDO

Exemplares do AK no acervo: 2
Status do Zé do Glock: BLOQUEADO

Teste 1: material sem exemplares disponíveis
Tião Tiguer pegou o Receitas de Quem Só Sabe Fritar Ovo
Exemplares sobrando: 0
Erro: O material Receitas de Quem Só Sabe Fritar Ovo não está disponível no momento.

Teste 2: quarto empréstimo para aluno com três ativos
Empréstimos ativos do Tião Tiguer: 3
Erro: O usuário Tião Tiguer já está com 3 empréstimos e o limite é 3.

Teste 3: usuário bloqueado tentando pegar material
Status do Zé do Glock: BLOQUEADO
Erro: O usuário Zé do Glock está bloqueado até pagar a multa de R$ 10,00.

Teste 4: devolver um empréstimo já devolvido
Erro: O empréstimo 1 já foi devolvido.

Teste 5: passar do limite de acessos simultâneos do e-book
Acessos simultâneos em uso: 10 de 10
Exemplares físicos do e-book: 0
Erro: O material Manual do Tiguer 7 Que Só Anda Quando Quer não está disponível no momento.

Teste 6: devolução dentro do prazo

DEVOLUÇÃO DO EMPRÉSTIMO Nº 2

Usuário: Professor Pardal da Silva
Material: Como Desmontar um AK em 30 Segundos Volume 2
Data prevista: 22/11/2026
Devolvido em: 20/11/2026
Dias de atraso: 0
Multa: R$ 0,00
Status: DEVOLVIDO


Teste 7: devolução com atraso

DEVOLUÇÃO DO EMPRÉSTIMO Nº 4

Usuário: Tião Tiguer
Material: Revista Tiguer 7 Mensal
Data prevista: 08/11/2026
Devolvido em: 10/11/2026
Dias de atraso: 2
Multa: R$ 4,00
Status: DEVOLVIDO

Status do Tião Tiguer: BLOQUEADO

Teste 8: multa do aluno contra multa do professor com o mesmo atraso

Prevista do aluno: 15/12/2026
Prevista do professor: 22/12/2026

Seu Barriga (Multa padrão): 5 dias, R$ 10,00
Professor Pardal (Multa de professor): 5 dias, R$ 5,00

Teste 9: marcar empréstimos como atrasados
Empréstimos marcados como atrasados: 1
Status do empréstimo 3: ATRASADO
Status do empréstimo 5: ATIVO

Teste 10: cancelar empréstimo e tentar devolver
Status do empréstimo 5: CANCELADO
Acessos simultâneos em uso: 9 de 10
Erro: O empréstimo 5 foi cancelado e não pode ser devolvido.

Teste 11: pagar a multa e voltar a pegar material
Zé do Glock pagou R$ 10,00 de multa.
Status do Zé do Glock: ATIVO
Devolução prevista do novo empréstimo: 05/12/2026

Teste 12: dados inválidos
Erro: Data de empréstimo inválida: use o formato dd/mm/aaaa.
Exemplares da revista continuam: 2
Erro: A data de devolução não pode ser anterior à data do empréstimo.
Erro: E-mail inválido.
Erro: A quantidade de exemplares não pode ser negativa.
Erro: O número máximo de acessos simultâneos precisa ser maior que zero.

HISTÓRICO DE Zé do Glock

28/10/2026 18:40:41 | Empréstimo realizado
Material: Como Desmontar um AK em 30 Segundos Volume 2
Observação: Devolução prevista para 15/11/2026.

28/10/2026 18:40:41 | Devolução realizada
Material: Como Desmontar um AK em 30 Segundos Volume 2
Observação: Devolvido em 20/11/2026.

28/10/2026 18:40:41 | Material devolvido com atraso
Material: Como Desmontar um AK em 30 Segundos Volume 2
Observação: 5 dias de atraso.

28/10/2026 18:40:41 | Multa aplicada
Material: Como Desmontar um AK em 30 Segundos Volume 2
Observação: Multa de R$ 10,00 pela política Multa padrão.

28/10/2026 18:40:41 | Empréstimo realizado
Material: Rua dos Tiros 14+1: Guia do Bairro Glocks Filhos
Observação: Devolução prevista para 05/12/2026.

Eventos registrados no histórico: 31
Total de multas geradas na biblioteca: R$ 29,00
```

Em todos os casos o sistema mostra uma mensagem de erro e continua rodando, sem quebrar.
