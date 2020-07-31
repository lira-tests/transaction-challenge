# Desafio de criar e processar uma transação

## Objetivo

Temos 2 tipos de usuários, os comuns e lojistas, ambos têm carteira com dinheiro e realizam transferências entre eles. Vamos nos atentar somente ao fluxo de transferência entre dois usuários.

Requisitos:

- Para ambos tipos de usuário, precisamos do Nome Completo, CPF, e-mail e Senha. CPF/CNPJ e e-mails devem ser únicos no sistema. Sendo assim, seu sistema deve permitir apenas um cadastro com o mesmo CPF ou endereço de e-mail.

- Usuários podem enviar dinheiro (efetuar transferência) para lojistas e entre usuários.

- Lojistas só recebem transferências, não enviam dinheiro para ninguém.
Antes de finalizar a transferência, deve-se consultar um serviço autorizador externo, use este mock para simular (<https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6>).

- A operação de transferência deve ser uma transação (ou seja, revertida em qualquer caso de inconsistência) e o dinheiro deve voltar para a carteira do usuário que envia.

- No recebimento de pagamento, o usuário ou lojista precisa receber notificação enviada por um serviço de terceiro e eventualmente este serviço pode estar indisponível/instável. Use este mock para simular o envio (<https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04>)

## Como estruturei

- Criar a estrutura básica do projeto
    - Framework: Laravel
    - Docker (Nginx, PHP 7.4, MySQL 5.7, Redis).
- Foquei nas regras de negócio para criar as migrações e também as validações.
- Criar o processo a princípio síncrono e depois o tornar assíncrono.

## Rodando

1. `docker-compose up -d`
1. `docker-compose exec app composer install`
1. `docker-compose exec app php artisan migrate`
1. `docker-compose exec app php artisan db:seed`
1. `docker-compose exec app php artisan queue:listen --timeout=60 --sleep=3  --tries=3`
1. `docker-compose exec db mysql -proot -e 'DROP DATABASE IF EXISTS testing; CREATE DATABASE testing'`
1. `docker-compose exec app ./vendor/bin/phpunit`

## Endpoints

`http://localhost:8000/v1/users` **GET**

`http://localhost:8000/v1/transactions` **POST**

Raw:

```json
{
    "payer": "6763ecf4-7af8-4977-bac4-9daa091f6ef3",
    "payee": "38ac4663-8ca7-41db-835c-cb55820f131c",
    "amount": 10.99
}
```
## Fluxos

![Fluxos](https://i.ibb.co/8NfcTbk/fluxo-transaction-challenge.png)

## Referências

https://laravel.com/docs/7.x/

https://lumen.laravel.com/docs/7.x
