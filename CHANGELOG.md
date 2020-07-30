# Changelog

## 2020-07-28 - Estrutura

- Projeto criado com Laravel Lumen 7
- Criados arquivos para 'conteinerização' da aplicação
  - PHP 7.4
  - MySQK 5.7
  - NGINX 1.16
- Estrutura de diretórios
  - Criada pasta para Modelos
  - Criadas pastas de testes, baseadas no Laravel
- Migração
  - Criada migração para usuários com seus dados
  - Criada Trait para usar UUID

## 2020-07-29 - Desenvolvimento

- Criação das migrações, models e relacionamentos
- Criação das validações no request inicial
- Criação hardcoded da transação, usado bcadd e bcsub

## 2020-07-30 - Desenvolvimento

- Inserido controle de transações para transactions
- Criado estrutura dos serviços de Autorização e Notificação
