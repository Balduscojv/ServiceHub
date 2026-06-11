# ServiceHub

Gestão de ordens de serviço de clientes — desafio técnico.

## Stack

- **Backend:** Laravel 12, PHP 8.3
- **Frontend:** Vue 3 + Inertia.js + TailwindCSS
- **Banco:** MySQL 8.4
- **Fila:** Redis
- **Testes:** Pest 3
- **Infra:** Docker (PHP-FPM, Nginx, MySQL, Redis)

## Domínio

```
Company (1:N) → Project (1:N) → Ticket (1:1) → TicketDetail
User (1:1) → UserProfile
User (1:N) → Tickets
```

## Estrutura

```
servicehub/
├── app/              # Backend PHP (Models, Controllers, Jobs, Services)
├── front/            # Frontend (Vue 3, Inertia, Tailwind)
│   ├── js/
│   │   ├── Components/
│   │   ├── Layouts/
│   │   ├── Pages/
│   │   ├── app.js
│   │   └── bootstrap.js
│   ├── css/app.css
│   ├── vite.config.js
│   ├── tailwind.config.js
│   ├── postcss.config.js
│   └── package.json
├── database/         # Migrations, Factories, Seeders
├── resources/views/  # Blade entry point (app.blade.php)
├── routes/           # web.php, console.php
└── tests/            # Pest (Unit + Feature)
```

## Subir o ambiente

```bash
# Primeira vez
make setup

# Usos comuns
make up          # sobe os containers
make down        # derruba
make migrate     # php artisan migrate
make fresh       # migrate:fresh --seed
make test        # roda o Pest
make queue       # inicia o worker de fila
make shell       # acessa o container
```

Acesse: [http://localhost:8000](http://localhost:8000)

Usuário de teste (após `make fresh`):
- **Email:** admin@servicehub.local
- **Senha:** password

## Funcionalidades

- Autenticação (registro, login, logout)
- **2FA com Google Authenticator** — ative no perfil; usa TOTP (RFC 6238) sem dependência externa
- **Tema claro/escuro** — toggle na navbar, preferência salva no localStorage
- CRUD completo: Empresas, Projetos, Tickets
- Upload de anexo (JSON/TXT) processado em fila (Redis)
- Job `ProcessTicketAttachment` — enriquece `TicketDetail`, notifica responsável por e-mail
- **Tabela `login_sessions`** — rastreia logins com IP/user-agent e revoga sessão no logout
- Perfil de usuário com cargo e telefone

## Rodar testes

```bash
make test
# ou dentro do container:
./vendor/bin/pest --coverage
```
