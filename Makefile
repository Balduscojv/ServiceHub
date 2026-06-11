
up: 
	docker compose up -d

down: 
	docker compose down -v --remove-orphans
	docker image prune -f

build:
	docker compose build --no-cache

restart: down up 

logs:
	docker compose logs -f

# ─── App ─────────────────────────────────────────────────────────────────────

shell: ## Abre shell no container da aplicação
	docker compose exec app bash

tinker: ## Inicia o Laravel Tinker
	docker compose exec app php artisan tinker

# ─── Banco de dados ──────────────────────────────────────────────────────────

migrate: ## Executa as migrations
	docker compose exec app php artisan migrate

seed: ## Executa todos os seeders
	docker compose exec app php artisan db:seed

fresh: ## Recria o banco e executa seeders
	docker compose exec app php artisan migrate:fresh --seed

# ─── Fila ────────────────────────────────────────────────────────────────────

queue: ## Inicia o worker de fila manualmente
	docker compose exec app php artisan queue:work --tries=3

# ─── Testes ──────────────────────────────────────────────────────────────────

test: ## Executa a suite de testes com Pest
	docker compose exec app ./vendor/bin/pest

test-coverage: ## Executa testes com relatório de cobertura
	docker compose exec app ./vendor/bin/pest --coverage

# ─── Qualidade de código ─────────────────────────────────────────────────────

lint: ## Formata o código com Laravel Pint
	docker compose exec app ./vendor/bin/pint

# ─── Assets ──────────────────────────────────────────────────────────────────

npm-install: ## Instala dependências Node
	docker compose run --rm node npm install

npm-dev: npm-install ## Inicia Vite em modo desenvolvimento
	docker compose run --rm --service-ports node npm run dev

npm-build: npm-install ## Gera build de produção dos assets
	docker compose run --rm node npm run build

# ─── Setup inicial ───────────────────────────────────────────────────────────

setup: build up ## Configura o ambiente do zero
	docker compose exec app composer install
	docker compose exec app cp .env.example .env
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan migrate --seed
	@echo "\n\033[32mServiceHub pronto em http://localhost:8000\033[0m"
