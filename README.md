### 1. clone git repository

### 2. Uruchomiamy kontenery: `docker-compose up -d --build`
#### Bedą uruchomione kontenery: 
- `tasks_api_app` PHP-fpm
- `tasks_api_webserver` nginx
- `tasks_api_db` baza Postgres
- `tasks_api_redis` Redis

### 3. Dodajemy alias'y (opcjonalne):
- `alias tasks_api_app_runa="docker-compose run --rm tasks_api_app php artisan "`
- `alias tasks_api_app_run="docker-compose run --rm tasks_api_app "`

### 4. Instalujemy + aktualizujemy Laravel: `tasks_api_app_run composer install && tasks_api_app_run composer update`

### 5. Generujemy klucz: `tasks_api_app_runa key:generate`

### 6. Dodajemy + uzupełniamy plik .env: `cp .env.example .env && nano .env`
#### Jeżeli chcemy użyć defoltowych znaczeń z kontenerów:
- `DB_HOST=tasks_api_db`  
  `DB_PORT=5432`  
  `DB_DATABASE=postgres`  
  `DB_USERNAME=postgres`  
  `DB_PASSWORD=postgres`  
- `CACHE_DRIVER=redis`
- `REDIS_HOST=tasks_api_redis`  
  `REDIS_PASSWORD=redis`  
  `REDIS_PORT=6379`  
  `REDIS_CLIENT=predis`

### 7. Wczytujemy nową konfigurację: `tasks_api_app_runa config:clear`

### 8. Testy
- Dla testów jest stworzono osobną BD (postgres_test) + użytkownik (postgres_test). 
- Cała konfiguracja jest w pliku: `postgres_init/creating_database_for_running_tests.sh` + `phpunit.xml`

### Uruchomiamy testy: `tasks_api_app_runa test`
