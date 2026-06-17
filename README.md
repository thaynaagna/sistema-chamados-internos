# Sistema de Controle de Chamados Internos 🎫

## 📌 Sobre o projeto

Sistema web para gerenciamento de chamados internos, permitindo abertura, atribuição automática e acompanhamento de solicitações dentro de um ambiente corporativo.

O sistema foi desenvolvido como teste técnico, com foco em boas práticas de desenvolvimento full stack e em uma decisão de arquitetura específica: distribuir os chamados de forma equilibrada entre o time de suporte, evitando que algumas pessoas fiquem sobrecarregadas enquanto outras ficam sem tarefas.

---

## 🚀 Tecnologias utilizadas

- **Laravel 11 (PHP 8.2+)**
- **Vue.js 3 (Composition API)**
- **Inertia.js**
- **Tailwind CSS**
- **Vite**
- **SQLite** (banco padrão do projeto, sem necessidade de servidor externo)
- **Laravel Breeze** (autenticação)

---

## ⚙️ Funcionalidades

- Abertura, listagem e visualização detalhada de chamados
- **Atribuição automática de responsável**, com base na carga de trabalho atual de cada pessoa do suporte (ponderada por prioridade do chamado)
- Reatribuição manual de responsável, quando necessário
- Atualização de status (aberto → em andamento → resolvido → fechado)
- Histórico completo de cada chamado (quem abriu, quem mudou o status, quando)
- Painel de distribuição de carga, mostrando visualmente quem está mais ou menos sobrecarregado
- Filtros por status, prioridade, categoria e responsável
- Controle de acesso por perfil (solicitante vê só os próprios chamados; suporte/admin vê todos)
- Interface dinâmica (SPA via Inertia.js)
- Validação de dados no back-end

---

## 📁 Estrutura do projeto

- `app/Models/` → Models (User, Chamado, Categoria, ChamadoHistorico)
- `app/Services/AtribuicaoService.php` → lógica de distribuição automática de chamados
- `app/Http/Controllers/` → Controllers (ChamadoController, DashboardController)
- `resources/js/Pages/` → telas Vue (Chamados e Dashboard de distribuição)
- `routes/` → rotas da aplicação
- `database/migrations/` e `database/seeders/` → estrutura das tabelas e dados de teste
- `config/` → configurações do Laravel

---

## 🛠️ Como executar o projeto localmente

### Pré-requisitos

- **PHP 8.2 ou superior** (versões anteriores não são compatíveis com o Laravel 11 / Breeze atual)
- **Composer**
- **Node.js 18+** e **npm**
- Extensão PHP `zip` habilitada (necessária para o Composer instalar as dependências)

> 💡 Se ao rodar `composer install` aparecer um erro de versão de PHP, confirme sua versão com `php -v`. Se for menor que 8.2, será necessário instalar uma versão mais recente antes de continuar.

### 1. Clonar o repositório

```bash
git clone https://github.com/thaynaagna/sistema-chamados-internos.git
cd sistema-chamados-internos
```

### 2. Instalar as dependências do Backend (PHP)

```bash
composer install
```

### 3. Instalar as dependências do Frontend (Node.js)

```bash
npm install
```

### 4. Configurar o arquivo de ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configurar o Banco de Dados (SQLite)

O projeto já vem configurado para usar SQLite por padrão — não é necessário instalar ou configurar nenhum servidor de banco de dados separado. Basta criar o arquivo do banco:

```bash
touch database/database.sqlite
```

> No Windows (PowerShell), use: `New-Item -ItemType File -Path "database\database.sqlite"`

### 6. Executar Migrations e Seeders

```bash
php artisan migrate --seed
```

> O `--seed` é obrigatório: ele cria os usuários de teste (admin, 3 pessoas de suporte e 2 solicitantes), sem os quais a atribuição automática não tem para quem atribuir.

### 7. Rodar o Servidor de Backend

```bash
php artisan serve
```

### 8. Rodar o Servidor de Frontend

Em um **novo terminal** (mantenha o anterior aberto):

```bash
npm run dev
```

Acesse **http://localhost:8000/chamados** (lista de chamados).

---

## 👥 Usuários de teste

Senha de todos: `password`

| Perfil | E-mail |
|---|---|
| Admin | admin@empresa.com |
| Suporte | carlos@empresa.com |
| Suporte | fernanda@empresa.com |
| Suporte | rafael@empresa.com |
| Solicitante | juliana@empresa.com |
| Solicitante | marcos@empresa.com |

---

## 🧠 Decisão de arquitetura: atribuição automática

Em vez de atribuir pelo critério simples de "quem tem menos chamados", o sistema calcula uma **carga ponderada por prioridade**: cada chamado ativo (status `aberto` ou `em_andamento`) soma à carga da pessoa responsável, com peso maior para prioridades mais altas (alta = 3, média = 2, baixa = 1). O próximo chamado aberto vai automaticamente para quem tiver a menor carga total no momento.

Essa lógica está isolada em `app/Services/AtribuicaoService.php`, sem nenhuma regra de negócio espalhada pelos controllers.

---

## 📚 Bibliotecas externas

- **Laravel Breeze**: autenticação (login, registro, perfis de usuário)
- **Inertia.js**: ponte entre Laravel e Vue, sem necessidade de API REST separada

---

## 📝 Observações

- Utiliza Inertia.js para uma experiência de SPA sem a complexidade de uma API separada
- Segue o padrão MVC do Laravel, com a regra de distribuição isolada em uma camada de Service
- Não há dependência de serviços externos (sem fila, sem SMTP real) — tudo roda localmente

---

## 👨‍💻 Autora

Desenvolvido por **Thayná Ágna**




