Проект laravel с использованием docker

## Установка

1. Склонировать репозиторий

2. Создать файл .env

3. Запустить контейнеры

```bash
docker-compose up -d
```

4. Установить зависимости

```bash
docker exec -it laravel-app composer install
```

5. Создать ключ приложения

```bash
docker exec -it laravel-app php artisan key:generate
```

6. Запустить миграции

```bash
docker exec -it laravel-app php artisan migrate
```

7. Запустить сервер

```bash
docker exec -it laravel-app php artisan serve --host=0.0.0.0 --port=8000
```

8. Открыть в браузере http://localhost:8000


