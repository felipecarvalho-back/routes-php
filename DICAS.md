# Dicas de Melhorias e Próximos Passos

Este documento detalha as revisões técnicas, pontos de atenção no código atual e os próximos passos recomendados para a evolução da arquitetura do projeto.

---

## 1. Pontos de Atenção e Correções Técnicas

### 1.1 Parâmetros de rota perdidos em requisições POST
- **Problema atual:** No método `run()` de `Src\Core\Routes`, a captura dos parâmetros capturados pela regex só é instanciada dentro do bloco `if ($metodoHttp == 'GET')`. Quando a requisição é `POST`, o `Request` é criado com `null` no primeiro argumento.
- **Impacto:** Se existir uma rota como `$rota->post('/usuario/{id}', ...)`, o valor de `{id}` não estará disponível em `$request->param()`.
- **Solução recomendada:** Extrair os parâmetros da rota independentemente do método HTTP e passá-los para o `Request` em qualquer situação.

### 1.2 Interrupção prematura do loop de rotas (`break`)
- **Problema atual:** No `foreach` de rotas, caso a expressão regular da URL confira, mas o método HTTP não esteja definido para aquela rota, o bloco cai em um `else { break; }`.
- **Impacto:** O loop é abortado imediatamente e resulta em erro `404 - Rota não encontrada`.
- **Solução recomendada:**
  - Se a URL existe mas o método não é compatível, o status correto é `405 Method Not Allowed`.
  - Caso não queira disparar 405 de imediato, remover o `break` do `else` permite que o loop continue procurando outras regras de rota que possam ser compatíveis.

### 1.3 Otimização da classe `Request`
- **Cópia desnecessária de dados:** O método `post()` percorre `$this->posts` com um `foreach` apenas para montar uma cópia idêntica do array. Pode retornar `$this->posts` diretamente.
- **Acesso pontual a campos:** Permitir busca por chave específica ou retorno completo:
  ```php
  public function post(?string $campo = null): mixed
  {
      if ($campo === null) {
          return $this->posts;
      }
      return $this->posts[$campo] ?? null;
  }
  ```
- **Suporte a múltiplos dados:** Evoluir a classe para receber e centralizar:
  - Parâmetros de rota (`/usuario/{id}`).
  - Query parameters de URL (`$_GET`, como `?busca=php`).
  - Corpo da requisição (`$_POST` ou JSON).

### 1.4 Resolução de classes via Composer PSR-4 (Remover `require_once` manual)
- **Problema atual:** Em `Routes.php`, é feita checagem de arquivo com `file_exists` e carregamento manual com `require_once`.
- **Solução recomendada:** Como o `composer.json` já mapeia o namespace `"Src\\": "src/"`, o autoload da PSR-4 faz esse trabalho automaticamente. Basta checar a existência da classe:
  ```php
  if (!class_exists($nomeCompletoDaClasse)) {
      http_response_code(500);
      echo "Controller '$nomeCompletoDaClasse' não encontrado.";
      return;
  }
  ```

### 1.5 Padronização de maiúsculas/minúsculas nos namespaces (Compatibilidade com Linux)
- **Problema atual:** Em `HomeController.php`, há `use Src\controller\Controller;` (com `c` minúsculo no diretório `controller`).
- **Impacto:** No Windows funciona sem problemas, mas em ambientes Linux ou contêineres Docker o autoload falhará devido ao sistema de arquivos case-sensitive.
- **Solução recomendada:** Padronizar sempre para PascalCase: `use Src\Controller\Controller;`.

---

## 2. Roteiro Recomendado (Trilha de Evolução)

### Passo 1: Consolidação da classe `Request`
- Permitir múltiplos parâmetros dinâmicos na rota (ex: `/produto/{categoria}/{id}`).
- Disponibilizar métodos utilitários:
  - `$request->param('id')` ou `$request->params()`.
  - `$request->get('filtro')` para query strings (`$_GET`).
  - `$request->post('nome')` para dados do formulário (`$_POST`).

### Passo 2: Estrutura de Document Root (`public/`)
- Mover o ponto de entrada da aplicação para `public/index.php`.
- Criar a pasta `public/` para isolar arquivos públicos (CSS, JS, imagens).
- Proteger o código-fonte (`src/`) e as dependências (`vendor/`) contra acesso direto via navegador.
- Configurar reescrita de URL (.htaccess para Apache ou configuração equivalente no Nginx/PHP Built-in Server).

### Passo 3: Criação da classe `Response`
- Centralizar o envio de respostas HTTP:
  - `$response->status(200);`
  - `$response->json($dados);`
  - `$response->redirect('/caminho');`
- Evitar o uso de `echo` solto ou manipulação manual de cabeçalhos dentro dos controllers.

### Passo 4: Integração com Banco de Dados (Camada Model)
- Criar uma classe de conexão com o banco de dados baseada em PDO.
- Desenvolver uma classe abstrata `Model` para operações comuns (buscar por ID, listar todos, inserir, atualizar e excluir).
- Ligar as ações dos controllers às operações de banco e renderizar os resultados nas views.
