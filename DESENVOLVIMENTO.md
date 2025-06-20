# Documentação do Projeto - Spassu Challenge

**Sistema de Gerenciamento de Livros com Laravel e Vue.js**

---

## 📋 Visão Geral do Projeto

Este documento apresenta o desenvolvimento completo de uma aplicação web moderna para gerenciamento de livros, implementada com **Laravel 12** e **Vue.js 3**, seguindo padrões arquiteturais avançados e boas práticas de desenvolvimento.

### Objetivos do Sistema

- Gerenciamento completo de livros, autores e assuntos
- Interface moderna e responsiva
- API RESTful para integração
- Relatórios em PDF
- Dashboard com gráficos interativos

---

## 🛠 Tecnologias e Ferramentas

### Backend

- **PHP 8.2+** - Linguagem principal
- **Laravel 12** - Framework web
- **PostgreSQL 15** - Banco de dados
- **Composer** - Gerenciador de dependências

### Frontend

- **Vue.js 3** - Framework frontend
- **TypeScript** - Tipagem estática
- **Inertia.js** - Stack moderno Laravel + Vue
- **Vite** - Build tool e hot reload
- **Chart.js** - Gráficos interativos
- **ShadCN/UI** - Sistema de design

### DevOps e Ambiente

- **Docker** - Containerização
- **Docker Compose** - Orquestração
- **Nginx** - Servidor web
- **Node.js 20** - Runtime frontend

### Bibliotecas Auxiliares

- **DomPDF** - Geração de relatórios PDF
- **PHPUnit** - Testes unitários
- **Mockery** - Mocking para testes

---

## 🏗 Arquitetura e Design Patterns

### Padrões Arquiteturais Principais

#### 1. **MVC (Model-View-Controller)**

- **Models**: Eloquent ORM para representação de dados
- **Views**: Componentes Vue.js com Inertia.js
- **Controllers**: Coordenação de requisições e respostas

#### 2. **Repository Pattern**

- Abstração da camada de acesso a dados
- Centralização de queries
- Facilita testes com mocking

#### 3. **Service Layer Pattern**

- Encapsulamento da lógica de negócio
- Coordenação entre repositories
- Gerenciamento de transações

#### 4. **Dependency Injection (DI)**

- Container IoC do Laravel
- Injeção automática de dependências
- Padrão Singleton para services

### Padrões Comportamentais

#### 5. **Command Pattern**

- Form Requests para validação
- Encapsulamento de operações específicas

#### 6. **Observer Pattern**

- Sistema de reatividade Vue.js
- Watchers para mudanças de estado

### Padrões Estruturais

#### 7. **DTO (Data Transfer Object)**

- Transferência tipada de dados
- Propriedades readonly para imutabilidade

#### 8. **Active Record**

- Eloquent ORM
- Relacionamentos declarativos
- Operações CRUD integradas


## 🚀 Processo de Desenvolvimento

### 1. Configuração do Ambiente

#### Inicialização do Projeto

```bash
# Instalação do Laravel com Vue.js
laravel new spassu-challenge --vue

# Configuração do PHPUnit como framework de testes
cd spassu-challenge
```

#### Configuração Docker Completa

**Estrutura de Containers:**

- **App Container**: PHP 8.2-FPM + Composer + aplicação Laravel
- **Nginx Container**: Servidor web otimizado para Laravel
- **Node Container**: Node.js 20 + Vite para desenvolvimento frontend
- **PostgreSQL Container**: Banco de dados com persistência

**Arquivos Docker Criados:**

- `Dockerfile`: Container principal da aplicação
- `docker-compose.yml`: Orquestração dos serviços
- `docker/nginx/nginx.conf`: Configuração Nginx
- `docker/php/local.ini`: Configurações PHP
- `docker/php/docker-entrypoint.sh`: Script de inicialização

### 2. Modelagem do Banco de Dados

#### Estrutura das Tabelas

```bash
# Criação das migrações
php artisan make:migration create_assunto_table
php artisan make:migration create_autor_table
php artisan make:migration create_livro_table
php artisan make:migration create_livro_autor_table
php artisan make:migration create_livro_assunto_table
```

**Entidades Principais:**

- **Assunto**: Categorias/assuntos dos livros
- **Autor**: Informações dos autores
- **Livro**: Dados principais dos livros
- **Livro_Autor**: Relacionamento N:N entre livros e autores
- **Livro_Assunto**: Relacionamento N:N entre livros e assuntos

#### Relacionamentos Implementados

- Livros ↔ Autores (Many-to-Many)
- Livros ↔ Assuntos (Many-to-Many)

### 3. Implementação Backend

#### Models e Controllers

```bash
# Criação com recursos completos
php artisan make:model [nome] -c --resource
```

#### Sistema de Validação

```bash
# Form Requests especializados
php artisan make:request StoreBookRequest
php artisan make:request UpdateBookRequest
```

#### Arquitetura em Camadas

1. **Controllers**: Coordenação de requisições
2. **Services**: Lógica de negócio
3. **Repositories**: Acesso a dados
4. **DTOs**: Transferência de dados
5. **Form Requests**: Validação

#### Seeders para Dados de Teste

```bash
php artisan make:seeder [nome]
php artisan db:seed
```

### 4. Implementação Frontend

#### Bibliotecas de Interface

```bash
# Sistema de design moderno
npm install shadcn-vue radix-vue

# Tabelas avançadas
npm install @tanstack/vue-table

# Gráficos interativos
npm install chart.js vue-chartjs

# Componentes UI adicionais
npm install primevue primeicons vue-sonner
```

#### Estrutura de Componentes

- **Componentes Base**: Reutilizáveis (Button, Input, Table)
- **Componentes de Negócio**: Específicos da aplicação
- **Layouts**: Estruturas de página
- **Pages**: Páginas da aplicação

#### Funcionalidades Frontend

- Dashboard com gráficos Chart.js
- DataTables com paginação e busca
- Sistema de notificações toast
- Confirmação de ações de exclusão
- Responsividade completa

### 5. Funcionalidades Avançadas

#### Geração de Relatórios PDF

```bash
composer require barryvdh/laravel-dompdf
```

#### Sistema de Logs Estruturado

- LogService para rastreamento de operações
- Logs contextuais para debugging

#### Testes Automatizados

- Testes unitários para Services
- Mocking de dependências
- Cobertura de cenários críticos

---

## 📊 Funcionalidades Implementadas

### CRUD Completo

- **Livros**: Criação, edição, listagem, exclusão
- **Autores**: Gerenciamento completo
- **Assuntos**: Administração de categorias

### Dashboard Analítico

- Gráficos de livros por assunto
- Estatísticas gerais
- Últimos livros cadastrados

### Sistema de Relatórios

- Relatórios de autores em PDF
- Filtros por período
- Exportação de dados

### Interface Moderna

- Design responsivo
- Tema escuro/claro
- Componentes interativos
- Feedback visual para ações

---

## 🧪 Metodologias e Boas Práticas

### Princípios SOLID Aplicados

- **Single Responsibility**: Cada classe com uma responsabilidade
- **Open/Closed**: Extensível sem modificação
- **Dependency Inversion**: Dependências de abstrações

### Padrões de Código

- **PSR-12**: Padrões de codificação PHP
- **Strict Types**: Tipagem rigorosa PHP 8.1+
- **TypeScript**: Tipagem estática no frontend

### Organização do Projeto

- Estrutura de pastas Laravel padrão
- Separação clara de responsabilidades
- Nomenclatura descritiva

---

## 🚀 Execução do Projeto

### Pré-requisitos

- Docker e Docker Compose
- Git

### Comandos de Inicialização

```bash
# Clone do repositório
git clone [repository-url]
cd spassu-challenge

# Inicialização dos containers
docker-compose up -d

# Instalação automática de dependências
# (executada pelo docker-entrypoint.sh)

# Acesso à aplicação
http://localhost
```

### Estrutura de Containers

- **App**: `localhost:9000` (PHP-FPM)
- **Web**: `localhost:80` (Nginx)
- **Node**: `localhost:5173` (Vite dev server)
- **Database**: `localhost:5432` (PostgreSQL)

---


_Este projeto demonstra a aplicação prática de padrões de design modernos e boas práticas de desenvolvimento, resultando em uma aplicação robusta, escalável e maintível._
