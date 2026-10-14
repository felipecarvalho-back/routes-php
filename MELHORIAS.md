# Roadmap de Melhorias - Sistema de Rotas e MVC PHP

Este documento reúne sugestões e melhorias recomendadas para expandir a estrutura do projeto, tornando-o mais flexível, seguro e alinhado aos padrões modernos do ecossistema PHP.

---

## 1. Motor de Rotas (`Src\Core\Routes`)

- [x] **Suporte a múltiplos verbos HTTP**
  - Implementar métodos como `$rota->post(...)`.
  - Validar a requisição contra `$_SERVER['REQUEST_METHOD']`.
- [x] **Parâmetros dinâmicos na URL**
  - Permitir rotas com parâmetros, por exemplo: `/usuario/{id}` ou `/post/{slug}`.
  - Utilizar expressões regulares (`preg_match`) para casar as URLs e repassar os argumentos capturados aos métodos do controller.
- [ ] **Suporte a funções anônimas (Closures/Callbacks)**
  - Permitir registrar ações inline sem a necessidade obrigatória de criar uma classe controller:
    ```php
    $rota->get('/status', function () {
        echo 'API online';
    });
    ```
- [ ] **Sistema de Middlewares**
  - Executar interceptadores antes da ação final do controller (ex.: checagem de login, validação de sessão ou sanitização de cabeçalhos).
- [ ] **Agrupamento de rotas e prefixos**
  - Suporte a grupos como `$rota->group('/admin', function ($rota) { ... })`.

---

## 2. Controllers e Camada de Apresentação (Views)

- [x] **Passagem de dados para as Views**
  - Permitir passar arrays de variáveis no método `view()` de `Src\Controller\Controller`:
    ```php
    $this->view('perfil', ['nome' => 'Felipe', 'idade' => 20]);
    ```
  - Usar `extract($dados)` antes de incluir o arquivo da view.
- [ ] **Sistema de Layout Mestre (Template Base)**
  - Criação de uma estrutura com cabeçalho (`header`), rodapé (`footer`) e injeção do conteúdo específico da view solicitada.
- [ ] **Respostas em formato JSON**
  - Adicionar método auxiliar `$this->json($dados, $statusCode)` no controller base para respostas de APIs REST.

---

## 3. Abstração de Requisições e Respostas

- [ ] **Classe `Request`**
  - Encapsular e sanitizar dados de `$_GET`, `$_POST`, `$_SERVER` e o corpo bruto da requisição (`file_get_contents('php://input')`).
- [ ] **Classe `Response`**
  - Abstrair o envio de cabeçalhos HTTP, status codes e métodos de redirecionamento (`$response->redirect('/login')`).

---

## 4. Arquitetura, Segurança e Padrões PSR

- [ ] **Pasta `public/` como Document Root**
  - Mover o ponto de entrada para `public/index.php`.
  - Proteger diretórios internos como `src/` e `vendor/` para não ficarem expostos diretamente no servidor web.
  - Adicionar arquivo `.htaccess` configurado com reescrita de URL (*URL rewrite*) para redirecionar requisições para `public/index.php`.
- [ ] **Aproveitamento completo do Autoload do Composer**
  - Remover `require_once` manuais de arquivos de controller dentro da classe `Routes`, deixando a resolução automática para a PSR-4 configurada no `composer.json`.
- [ ] **Padronização de Namespaces (PSR-4)**
  - Padronizar o case das pastas e namespaces (usar `Src\Controller` de forma uniforme, evitando variações como `Src\controller`).

---

## 5. Banco de Dados e Model (MVC Completo)

- [ ] **Conexão PDO Centralizada**
  - Criar classe de conexão utilizando padrão Singleton ou Factory com `PDO`.
- [ ] **Model Base**
  - Criar classe abstrata `Model` com métodos utilitários para operações básicas (como `all()`, `find()`, `create()`, `delete()`).

---

## 6. Ferramentas de Desenvolvimento e Tratamento de Erros

- [x] **Scripts no `composer.json`**
  - Adicionar atalho para subir o servidor embutido do PHP:
    ```json
    "scripts": {
        "start": "php -S localhost:8000 public/index.php"
    }
    ```
- [ ] **Páginas de Erro Personalizadas**
  - Criar views estilizadas para status HTTP `404 - Não Encontrado` e `500 - Erro Interno do Servidor`.
- [ ] **Suporte a variáveis de ambiente (`.env`)**
  - Configurar carregamento de credenciais de banco e ambiente sem deixar senhas no código fonte.
