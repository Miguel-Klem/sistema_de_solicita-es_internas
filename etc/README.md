# Case Fácil - Etapa 1: Cadastro e Login

Esta primeira etapa cobre apenas usuários e autenticação. Solicitações,
categorias e histórico entram nas próximas etapas.

## Estrutura de pastas

```
case-facil/
├── config/
│   └── database.php      → conexão com o banco
├── includes/
│   ├── auth.php           → sessão e controle de acesso
│   └── functions.php      → funções utilitárias
├── public/                → pasta que o Apache deve servir
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   └── logout.php
└── sql/
    └── schema.sql          → script para criar o banco e as tabelas
```

## Passo a passo

1. **Criar o banco de dados**
   Abra o phpMyAdmin (ou seu cliente MySQL) e rode o conteúdo do arquivo
   `sql/schema.sql`. Isso cria o banco `case_facil` e as tabelas `users`
   e `departments`, já com 3 departamentos de exemplo.

2. **Configurar a conexão**
   Abra `config/database.php` e confira se `DB_USER` e `DB_PASS` batem
   com o usuário e senha que você configurou no seu MySQL. Se o MySQL
   não tiver senha para o usuário root, deixe `DB_PASS` como está (vazio).

3. **Colocar o projeto no Apache**
   Copie a pasta `case-facil` inteira para dentro da pasta que o Apache
   serve (geralmente `htdocs` ou `www`, dependendo de como você instalou).
   Importante: só a pasta `public/` deveria ser acessível pelo navegador
   em um projeto "de verdade", mas por simplicidade nesta fase vamos
   acessar tudo dentro de `case-facil/public/` mesmo.

4. **Acessar**
   No navegador, acesse algo como:
   `http://localhost/case-facil/public/register.php`
   Cadastre um usuário, depois faça login em `login.php`.

## O que já funciona

- Cadastro de usuário com senha protegida por hash (`password_hash`)
- Login com verificação de senha (`password_verify`) e criação de sessão
- Página inicial protegida (só acessa quem está logado)
- Logout

## Próximos passos (não incluídos ainda)

- Cadastro de categorias
- Criação de solicitações com número sequencial (SOL-000001)
- Listagem de solicitações com filtros
- Histórico de alterações de status/responsável
- Promoção de usuário para "agent" (atendente) ou "admin"
  (por enquanto, isso só é possível editando direto no banco:
  `UPDATE users SET profile = 'admin' WHERE email = 'seu@email.com';`)