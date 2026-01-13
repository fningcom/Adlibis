# Документация по тестированию API в Postman

## Обзор проекта

Проект представляет собой Laravel-приложение с API для управления новостями, видео-постами и комментариями. Архитектура включает:

- Аутентификацию через Laravel Sanctum
- CRUD-операции для новостей и видео-постов
- Систему комментариев с поддержкой вложенности (до любого уровня)
- Полиморфные отношения для комментариев
- Курсорную пагинацию
- Обработку ошибок API

## Подготовка к тестированию

### 1. Запуск приложения

```bash
# Запустить контейнеры
docker-compose up -d

# Установить зависимости
docker-compose run composer install

# Запустить миграции
docker-compose run artisan migrate

```

Сервер будет доступен по адресу `http://localhost:8080`

## Коллекция Postman: Описание endpoint'ов

#### Все маршруты можно импортировать из файла `postman-collection.json`

### 1. Аутентификация

#### 1.1 Регистрация пользователя
- **POST** `/api/register`
- **Headers**: `Content-Type: application/json`
- **Body** (JSON):
```json
{
  "name": "Имя пользователя",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```
- **Пример успешного ответа**:
```json
{
  "user": {
    "id": 1,
    "name": "Имя пользователя",
    "email": "user@example.com",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  },
  "token": "1|example-token-here"
}
```

#### 1.2 Вход пользователя
- **POST** `/api/login`
- **Headers**: `Content-Type: application/json`
- **Body** (JSON):
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```
- **Пример успешного ответа**:
```json
{
  "user": {
    "id": 1,
    "name": "Имя пользователя",
    "email": "user@example.com",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  },
  "token": "1|example-token-here"
}
```

#### 1.3 Выход пользователя
- **POST** `/api/logout`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {токен}`
- **Пример успешного ответа**:
```json
{
  "message": "Logged out successfully"
}
```

### 2. Работа с новостями

#### 2.1 Получение списка новостей (публичный)
- **GET** `/api/news`
- **Headers**: `Content-Type: application/json`
- **Параметры запроса**:
  - `limit` (необязательно, по умолчанию 10) - количество элементов
  - `cursor` (необязательно) - курсор для пагинации
- **Пример успешного ответа**:
```json
{
  "data": [
    {
      "id": 1,
      "title": "Заголовок новости",
      "description": "Описание новости",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "next_cursor": 1,
  "has_more": true
}
```

#### 2.2 Получение конкретной новости (публичный)
- **GET** `/api/news/{id}`
- **Headers**: `Content-Type: application/json`
- **Параметры запроса**:
  - `limit` (необязательно, по умолчанию 10) - количество комментариев
  - `cursor` (необязательно) - курсор для пагинации комментариев
- **Пример успешного ответа**:
```json
{
  "data": {
    "id": 1,
    "title": "Заголовок новости",
    "description": "Описание новости",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  },
  "comments": [
    {
      "id": 1,
      "content": "Комментарий",
      "user": {
        "id": 1,
        "name": "Имя пользователя",
        "email": "user@example.com"
      },
      "parent_id": null,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "next_cursor": 1,
  "has_more": true
}
```

#### 2.3 Создание новости (требует аутентификации)
- **POST** `/api/news`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {токен}`
- **Body** (JSON):
```json
{
  "title": "Заголовок новости",
  "description": "Описание новости"
}
```
- **Пример успешного ответа**:
```json
{
  "id": 1,
  "title": "Заголовок новости",
  "description": "Описание новости",
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

#### 2.4 Обновление новости (требует аутентификации)
- **PUT** `/api/news/{id}`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {токен}`
- **Body** (JSON):
```json
{
  "title": "Обновленный заголовок",
  "description": "Обновленное описание"
}
```
- **Пример успешного ответа**:
```json
{
  "id": 1,
  "title": "Обновленный заголовок",
  "description": "Обновленное описание",
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

#### 2.5 Удаление новости (требует аутентификации)
- **DELETE** `/api/news/{id}`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {токен}`
- **Пример успешного ответа**:
```json
{
  "message": "News deleted successfully"
}
```

### 3. Работа с видео-постами

#### 3.1 Получение списка видео-постов (публичный)
- **GET** `/api/video-posts`
- **Headers**: `Content-Type: application/json`
- **Параметры запроса**:
  - `limit` (необязательно, по умолчанию 10) - количество элементов
  - `cursor` (необязательно) - курсор для пагинации
- **Пример успешного ответа**:
```json
{
  "data": [
    {
      "id": 1,
      "title": "Заголовок видео-поста",
      "description": "Описание видео-поста",
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "next_cursor": 1,
  "has_more": true
}
```

#### 3.2 Получение конкретного видео-поста (публичный)
- **GET** `/api/video-posts/{id}`
- **Headers**: `Content-Type: application/json`
- **Параметры запроса**:
  - `limit` (необязательно, по умолчанию 10) - количество комментариев
  - `cursor` (необязательно) - курсор для пагинации комментариев
- **Пример успешного ответа**:
```json
{
  "data": {
    "id": 1,
    "title": "Заголовок видео-поста",
    "description": "Описание видео-поста",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  },
  "comments": [
    {
      "id": 1,
      "content": "Комментарий",
      "user": {
        "id": 1,
        "name": "Имя пользователя",
        "email": "user@example.com"
      },
      "parent_id": null,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "next_cursor": 1,
  "has_more": true
}
```

#### 3.3 Создание видео-поста (требует аутентификации)
- **POST** `/api/video-posts`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {токен}`
- **Body** (JSON):
```json
{
  "title": "Заголовок видео-поста",
  "description": "Описание видео-поста"
}
```
- **Пример успешного ответа**:
```json
{
  "id": 1,
  "title": "Заголовок видео-поста",
  "description": "Описание видео-поста",
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

#### 3.4 Обновление видео-поста (требует аутентификации)
- **PUT** `/api/video-posts/{id}`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {токен}`
- **Body** (JSON):
```json
{
  "title": "Обновленный заголовок",
  "description": "Обновленное описание"
}
```
- **Пример успешного ответа**:
```json
{
  "id": 1,
  "title": "Обновленный заголовок",
  "description": "Обновленное описание",
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

#### 3.5 Удаление видео-поста (требует аутентификации)
- **DELETE** `/api/video-posts/{id}`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {токен}`
- **Пример успешного ответа**:
```json
{
  "message": "Video post deleted successfully"
}
```

### 4. Работа с комментариями

#### 4.1 Получение списка комментариев (публичный)
- **GET** `/api/comments`
- **Headers**: `Content-Type: application/json`
- **Параметры запроса**:
  - `limit` (необязательно, по умолчанию 10) - количество элементов
  - `cursor` (необязательно) - курсор для пагинации
  - `commentable_type` (необязательно) - тип сущности (App\Models\News или App\Models\VideoPost)
  - `commentable_id` (необязательно) - ID сущности
- **Пример успешного ответа**:
```json
{
  "data": [
    {
      "id": 1,
      "content": "Комментарий",
      "user": {
        "id": 1,
        "name": "Имя пользователя",
        "email": "user@example.com"
      },
      "parent_id": null,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "next_cursor": 1,
  "has_more": true
}
```

#### 4.2 Получение конкретного комментария (публичный)
- **GET** `/api/comments/{id}`
- **Headers**: `Content-Type: application/json`
- **Пример успешного ответа**:
```json
{
  "id": 1,
  "content": "Комментарий",
  "user": {
    "id": 1,
    "name": "Имя пользователя",
    "email": "user@example.com"
  },
  "parent_id": null,
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z",
  "replies": [
    {
      "id": 2,
      "content": "Ответ на комментарий",
      "user": {
        "id": 2,
        "name": "Имя другого пользователя",
        "email": "user2@example.com"
      },
      "parent_id": 1,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z",
      "replies": []
    }
  ]
}
```

#### 4.3 Создание комментария (требует аутентификации)
- **POST** `/api/comments`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {токен}`
- **Body** (JSON):
```json
{
  "content": "Текст комментария",
  "commentable_type": "App\\Models\\News",
  "commentable_id": 1,
  "parent_id": null
}
```
- **Пример успешного ответа**:
```json
{
  "id": 1,
  "content": "Текст комментария",
  "user": {
    "id": 1,
    "name": "Имя пользователя",
    "email": "user@example.com"
  },
  "parent_id": null,
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z",
  "replies": []
}
```

#### 4.4 Создание ответа на комментарий (требует аутентификации)
- **POST** `/api/comments`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {токен}`
- **Body** (JSON):
```json
{
  "content": "Ответ на комментарий",
  "commentable_type": "App\\Models\\News",
  "commentable_id": 1,
  "parent_id": 1
}
```

#### 4.5 Обновление комментария (требует аутентификации, только автор)
- **PUT** `/api/comments/{id}`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {токен}`
- **Body** (JSON):
```json
{
  "content": "Обновленный текст комментария"
}
```
- **Пример успешного ответа**:
```json
{
  "id": 1,
  "content": "Обновленный текст комментария",
  "user": {
    "id": 1,
    "name": "Имя пользователя",
    "email": "user@example.com"
  },
  "parent_id": null,
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z",
  "replies": []
}
```

#### 4.6 Удаление комментария (требует аутентификации, только автор)
- **DELETE** `/api/comments/{id}`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {токен}`
- **Пример успешного ответа**:
```json
{
  "message": "Comment deleted successfully"
}
```

## Коды ошибок

| Код | Описание |
|-----|----------|
| 200 | Успешный запрос |
| 201 | Ресурс успешно создан |
| 401 | Не авторизован (требуется токен) |
| 403 | Доступ запрещен (нет прав) |
| 404 | Ресурс не найден |
| 422 | Ошибка валидации |
| 500 | Внутренняя ошибка сервера |

## Проверка готовности в соответствии с руководством по реализации

### Выполненные этапы:

| Этап | Описание | Статус |
|------|----------|--------|
| 1 | Инициализация Laravel проекта | ✅ |
| 2 | Установка Sanctum | ✅ |
| 3 | Создание миграций | ✅ |
| 4 | Создание моделей | ✅ |
| 5 | Реализация моделей с relationships | ✅ |
| 6 | Создание контроллеров | ✅ |
| 7 | Реализация контроллеров | ✅ |
| 8 | Создание API Resources | ✅ |
| 9 | Реализация API Resources | ✅ |
| 10 | Создание Form Requests | ✅ |
| 11 | Реализация валидации | ✅ |
| 12 | Создание Policies | ✅ |
| 13 | Настройка маршрутов | ✅ |
| 14 | Курсорная пагинация | ✅ |
| 15 | Обработка ошибок | ✅ |
| 16 | Eager Loading | ✅ |

### Особенности реализации:

1. **Комментарии с поддержкой вложенности**: Реализована система комментариев с неограниченной вложенностью через поле `parent_id`.
2. **Polymorphic отношения**: Комментарии могут принадлежать как новостям, так и видео-постам через polymorphic связи.
3. **Курсорная пагинация**: Используется для эффективного получения большого количества данных без перегрузки системы.
4. **Рекурсивное дерево комментариев**: Через `CommentTreeResource` обеспечивается правильное отображение вложенной структуры комментариев.
5. **Проверка авторства**: В контроллерах и политиках реализована проверка, что пользователь является автором контента перед обновлением или удалением.

Все компоненты системы реализованы в соответствии с руководством по реализации, и проект готов к тестированию через Postman.
