# Roadmap de Melhorias - Sistema de Rotas e MVC PHP

Este documento reúne o progresso atual do projeto, sugestões de evolução e um guia passo a passo do que priorizar a seguir.

---

## 🎯 Direção Recomendada (Por onde seguir agora?)

Você já construiu toda a fundação difícil (Roteador com Regex, Container de Injeção de Dependência, Request, Dotenv e Conexão PDO). A melhor ordem para continuar sem se perder é:

1. **Passo 1 — Completar o CRUD no `Model.php` (Foco Principal Agora)**
   - Você já injeta `Usuario` no controller e conecta no banco. Agora implemente métodos reais no [`Model.php`](file:///d:/FELIPE%20ETEC/Materia/PHP/routes-php/src/model/Model.php) usando `$this->table`: `all()`, `find($id)`, `create($dados)`, `update($id, $dados)` e `delete($id)`.
   - No [`Database.php`](file:///d:/FELIPE%20ETEC/Materia/PHP/routes-php/src/core/Database.php), salve o `$pdo` em um atributo `private static ?PDO $instance = null;` (Singleton) para não abrir várias conexões na mesma requisição.
2. **Passo 2 — Helpers de Resposta no `Controller.php`**
   - Adicione `$this->redirect('/caminho')` (para redirecionar após salvar um formulário POST) e `$this->json($dados, $status)` no [`Controller.php`](file:///d:/FELIPE%20ETEC/Materia/PHP/routes-php/src/controller/Controller.php).
3. **Passo 3 — Padronização PSR-4 e Limpeza**
   - Padronize o namespace de [`Controller.php`](file:///d:/FELIPE%20ETEC/Materia/PHP/routes-php/src/controller/Controller.php) para `Src\Controller` (com `C` maiúsculo).
   - Remova a verificação manual `file_exists` de [`Routes.php`](file:///d:/FELIPE%20ETEC/Materia/PHP/routes-php/src/core/Routes.php), deixando o Composer cuidar 100% do carregamento da classe.
4. **Passo 4 — Evolução de Views e Middlewares**
   - Crie suporte a layout base (cabeçalho/rodapé reaproveitáveis) e proteção de rotas (Middlewares de autenticação).

---

## 1. Motor de Rotas e Core (`Src\Core`)

- [x] **Suporte a múltiplos verbos HTTP (`GET` e `POST`)**
  - Implementado em `$rota->get(...)` e `$rota->post(...)` validando `$_SERVER['REQUEST_METHOD']`.
- [x] **Parâmetros dinâmicos na URL**
  - Rotas com parâmetros como `/home/{id}` usando grupos nomeados de Regex (`(?P<id>...)`).
- [x] **Container de Injeção de Dependências (`Src\Core\Container`)**
  - Resolução automática de dependências no `__construct` dos Controllers via `ReflectionClass`.
- [ ] **Verbos HTTP adicionais (`PUT`, `DELETE`, `PATCH`)**
  - Adicionar métodos no `Routes` e suporte a method spoofing (`_method` em formulários HTML).
- [ ] **Sistema de Middlewares**
  - Executar interceptadores antes da ação final do controller (ex.: checagem de login/sessão).
- [ ] **Suporte a funções anônimas (Closures/Callbacks) e Grupos**
  - Permitir registrar ações inline e agrupar rotas sob prefixos (ex: `/admin`).

---

## 2. Abstração de Requisições e Respostas

- [x] **Classe `Request` Básica (`Src\Core\Request`)**
  - Captura de parâmetros de rota via `$request->param()` e dados de formulário via `$request->post()`.
- [ ] **Expandir a classe `Request`**
  - Adicionar `$request->query()` para ler parâmetros de `$_GET` (ex: `?busca=php&pagina=2`).
  - Suporte a leitura de corpo JSON (`json_decode(file_get_contents('php://input'), true)`).
- [ ] **Respostas e Redirecionamentos**
  - Criar métodos utilitários no `Controller` base ou uma classe `Response`:
    - `redirect(string $url)`: envia `header("Location: $url")`.
    - `json(array $data, int $status = 200)`: envia `Content-Type: application/json` e `json_encode($data)`.

---

## 3. Banco de Dados e Model (MVC Completo)

- [x] **Conexão PDO com Variáveis de Ambiente (`Src\Core\Database`)**
  - Conexão configurada com `utf8mb4`, `ERRMODE_EXCEPTION` e `FETCH_ASSOC`.
- [ ] **Singleton na Conexão PDO**
  - Evitar recriar a instância do `PDO` toda vez que `Database::conn()` for chamado.
- [x] **Estrutura Inicial da `Model` (`Src\Model\Model`)**
  - Classe abstrata com definição de `$table` e `$atributos`.
- [ ] **Métodos CRUD na `Model` Base**
  - Implementar consultas dinâmicas seguras com *Prepared Statements*:
    - `all()`: retorna todos os registros da tabela.
    - `find(int|string $id)`: busca um registro pelo ID.
    - `create(array $dados)`: faz `INSERT` filtrando apenas colunas permitidas em `$this->atributos`.
    - `update(int|string $id, array $dados)`: atualiza um registro pelo ID.
    - `delete(int|string $id)`: remove um registro pelo ID.

---

## 4. Controllers e Camada de Apresentação (Views)

- [x] **Passagem de dados para as Views**
  - Uso de `extract($dados)` no método `view()` de `Src\Controller\Controller`.
- [ ] **Sistema de Layout Mestre (Template Base)**
  - Criação de uma estrutura com cabeçalho (`header`), rodapé (`footer`) e injeção do conteúdo da view.
- [ ] **Páginas de Erro Personalizadas**
  - Criar views dedicadas para status HTTP `404 - Não Encontrado` e `500 - Erro Interno do Servidor`.

---

## 5. Arquitetura, Segurança e Padrões PSR

- [x] **Pasta `public/` como Document Root**
  - Ponto de entrada isolado em `public/index.php`.
- [x] **Suporte a variáveis de ambiente (`.env`)**
  - Carregamento seguro de credenciais com `vlucas/phpdotenv`.
- [x] **Scripts no `composer.json`**
  - Atalho `composer start` configurado.
- [x] **Aproveitamento 100% do Autoload PSR-4**
  - Remover a checagem fixa `file_exists(__DIR__ . "/../controller/{$controller}.php")` em `Routes::addRoute()`, validando apenas com `class_exists()`.
- [ ] **Padronização de Case em Namespaces e Pastas (PSR-4)**
  - Uniformizar `namespace Src\Controller;` em `Controller.php` (atualmente está `Src\controller` com `c` minúsculo).
