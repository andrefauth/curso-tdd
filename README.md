# Curso TDD.

## Ambiente de desenvolvimento do projeto

### Instalação

#### Requisitos

- php 5.6x
- xdebug
- Apache2
- MySQL/MariaDB
- PHPMyAdmin

#### Instalar o composer

```sh
https://getcomposer.org/download/
```

#### Clonar o repositório e executar o composer

```sh
git clone git@bitbucket.org:diogocavilha/curso-tdd.git
cd curso-tdd
composer install
```

#### Importar o banco de dados.

- Acessar o PHPMyAdmin
- Criar o banco de dados `curso_tdd`
- Importar o arquivo `curso-tdd/docs/curso_tdd.sql`

#### Executar os testes

```sh
cd curso-tdd
./vendor/bin/phpunit --colors tests/

```

## Ambiente de desenvolvimento do curso

### Criar o projeto

```sh
composer create-project piano/mvc curso-tdd dev-project
```

### Executar os testes

```sh
./vendor/bin/phpunit --colors tests/modules/application/models/CaixaTest.php
```