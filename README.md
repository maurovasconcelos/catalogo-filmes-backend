# Catálogo de Filmes - Backend

## Descrição

Este projeto é uma API RESTful desenvolvida com Laravel que serve como backend para o catálogo de filmes. A API permite gerenciar uma lista de filmes favoritos, com operações de adição, listagem e remoção. Os dados são armazenados localmente em um banco de dados MySQL.

## Tecnologias Utilizadas

-   **Laravel**: Framework PHP para desenvolvimento de aplicações web
-   **MySQL**: Sistema de gerenciamento de banco de dados relacional
-   **Docker**: Containerização da aplicação
-   **Composer**: Gerenciador de dependências para PHP
-   **PHPUnit**: Framework de testes para PHP

## Funcionalidades

-   **Gerenciamento de Favoritos**:
    -   Adicionar filmes aos favoritos
    -   Remover filmes dos favoritos
    -   Listar filmes favoritos
    -   Filtrar favoritos por gênero

## Pré-requisitos

-   Docker e Docker Compose
-   PHP 8.2 ou superior (para desenvolvimento local)
-   Composer (para desenvolvimento local)

## Instalação e Execução

### Usando Docker

```bash
# Clonar o repositório
git clone <url-do-repositorio>
cd catalogo-filmes-backend

# Configurar variáveis de ambiente
cp .env.example .env

# Iniciar com Docker Compose
docker-compose up -d

# Instalar dependências
docker exec -it catalogo-filmes-app composer install

# Gerar chave da aplicação
docker exec -it catalogo-filmes-app php artisan key:generate

# Executar migrações
docker exec -it catalogo-filmes-app php artisan migrate
```

### Desenvolvimento Local

```bash
# Clonar o repositório
git clone <url-do-repositorio>
cd catalogo-filmes-backend

# Instalar dependências
composer install

# Configurar variáveis de ambiente
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate

# Executar migrações
php artisan migrate

# Iniciar servidor de desenvolvimento
php artisan serve
```

## Variáveis de Ambiente

Crie um arquivo `.env` na raiz do projeto com as seguintes variáveis principais:

```
APP_NAME=CatalogoFilmes
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=catalogo_filmes
DB_USERNAME=root
DB_PASSWORD=root
```

## Estrutura do Projeto

```
├── app/                # Código fonte da aplicação
│   ├── Http/           # Controllers, Middleware, Requests
│   │   ├── Controllers/
│   │   └── Middleware/
│   └── Models/         # Modelos da aplicação
├── bootstrap/          # Arquivos de inicialização
├── config/             # Arquivos de configuração
├── database/           # Migrações e seeders
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── docker/             # Configurações do Docker
├── routes/             # Definição de rotas
│   ├── api.php         # Rotas da API
│   └── web.php         # Rotas web
├── storage/            # Arquivos gerados pela aplicação
├── tests/              # Testes automatizados
│   ├── Feature/        # Testes de feature
│   └── Unit/           # Testes unitários
├── .env.example        # Exemplo de variáveis de ambiente
├── composer.json       # Dependências e scripts
├── Dockerfile          # Configuração do Docker
└── docker-compose.yml  # Configuração do Docker Compose
```

## Endpoints da API

-   **GET /api/favorites** - Listar todos os filmes favoritos

    -   Parâmetros opcionais:
        -   `genre_id`: Filtrar por ID de gênero específico
        -   `genre_ids`: Filtrar por múltiplos IDs de gênero

-   **POST /api/favorites** - Adicionar um filme aos favoritos

    -   Parâmetros obrigatórios:
        -   `tmdb_id`: ID do filme no TMDB
        -   `title`: Título do filme
    -   Parâmetros opcionais:
        -   `poster_path`: Caminho do poster
        -   `overview`: Sinopse do filme
        -   `release_date`: Data de lançamento
        -   `vote_average`: Média de votos
        -   `genre_ids`: IDs dos gêneros do filme

-   **DELETE /api/favorites/{tmdb_id}** - Remover um filme dos favoritos

## Documentação da API (Swagger)

O projeto inclui documentação da API usando Swagger/OpenAPI. Para acessar:

1. Inicie o servidor Laravel: `php artisan serve`
2. Acesse a documentação em: [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

A documentação permite:

-   Visualizar todos os endpoints disponíveis
-   Testar as requisições diretamente pela interface
-   Verificar os formatos de entrada e saída de dados
-   Entender os possíveis códigos de resposta

## Testes

Para executar os testes automatizados:

```bash
# Usando Docker
docker exec -it catalogo-filmes-app php artisan test

# Desenvolvimento local
php artisan test
```

## Integração com Frontend

Esta API foi projetada para se comunicar com um frontend Vue.js. Certifique-se de que o frontend esteja configurado para apontar para a URL correta da API.
