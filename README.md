# CloudCastle HttpRequest

**CloudCastle HttpRequest** — современная PHP-библиотека для удобной, безопасной и расширяемой работы с HTTP-запросами, сессиями, cookie, файлами, заголовками, серверными переменными и окружением. Поддерживает автоматический разбор JSON и XML, паттерн Singleton, магические методы, глобальные вспомогательные функции и полностью покрыта тестами.

---

## 📋 Содержание
- [Возможности](#возможности)
- [Установка](#установка)
- [Быстрый старт](#быстрый-старт)
- [Архитектура и компоненты](#архитектура-и-компоненты)
- [Подробное API](#подробное-api)
- [Примеры использования](#примеры-использования)
- [Вспомогательные функции](#вспомогательные-функции)
- [Интеграция с фреймворками](#интеграция-с-фреймворками)
- [Тестирование](#тестирование)
- [FAQ](#faq)
- [Лицензия и контакты](#лицензия-и-контакты)

---

## 🚀 Возможности
- Универсальный доступ к данным запроса: GET, POST, COOKIE, SESSION, FILES, HEADERS, SERVER, ENV
- Автоматический разбор JSON и XML тела запроса
- Удобная работа с cookie и сессиями (Singleton, цепочки вызовов, сериализация)
- Безопасная работа с загруженными файлами
- Глобальные функции для быстрого доступа к данным
- Гибкая настройка времени жизни сессий и cookie
- Совместимость с современными стандартами PHP (8.1+)
- Полное покрытие тестами (PHPUnit)
- Расширяемость и интеграция с любыми фреймворками

---

## 📦 Установка

### Через Composer (рекомендуется)
```bash
composer require cloud-castle/http-request
```

### Ручная установка
```bash
git clone https://github.com/zorinalexey/cloud-castle-http-request
cd http-request
composer install
```

---

## ⚡ Быстрый старт

```php
<?php
require_once 'vendor/autoload.php';

use CloudCastle\HttpRequest\Request;

// Получить singleton Request
$request = Request::getInstance();

// Получить параметры запроса
$userId = $request->get('user_id');
$session = $request->session;
$all = $request->all();

// Получить POST/GET/COOKIE/FILES/HEADERS
$post = $request->post;
$get = $request->get;
$cookie = $request->cookie;
$files = $request->files;
$headers = $request->headers;
```

---

## 🏗️ Архитектура и компоненты

- **Request** — основной фасад для доступа ко всем данным запроса.
- **Get/Post** — доступ к GET/POST данным.
- **Cookie** — управление cookie (установка, получение, удаление, очистка).
- **Session** — управление сессиями.
- **Files/UploadFile** — работа с загруженными файлами.
- **Headers** — работа с HTTP-заголовками.
- **Server/Env** — доступ к серверным переменным и окружению.
- **Вспомогательные функции**: `request()`, `cookies()`, `session()`, `files()`, `headers()`, `get()`, `post()`, `env()`.

---

## 📚 Подробное API

### Request
| Метод                | Описание                                      |
|----------------------|-----------------------------------------------|
| getInstance()        | Получить singleton Request                    |
| init($sess, $cook)   | Инициализация с TTL для сессии и cookie       |
| get($key, $def)      | Получить параметр по ключу                    |
| all()                | Получить все данные запроса                   |
| __get($name)         | Магический доступ к компонентам               |

### Cookie
| Метод                | Описание                                      |
|----------------------|-----------------------------------------------|
| set($key, $val)      | Установить cookie                             |
| get($key, $def)      | Получить cookie                               |
| delete($key)         | Удалить cookie                                |
| clear()              | Очистить все cookie                           |

### Session
| Метод                | Описание                                      |
|----------------------|-----------------------------------------------|
| set($key, $val)      | Установить значение в сессию                  |
| get($key, $def)      | Получить значение из сессии                   |
| delete($key)         | Удалить значение из сессии                    |
| clear()              | Очистить сессию                               |

### Files/UploadFile
| Метод                | Описание                                      |
|----------------------|-----------------------------------------------|
| get($name)           | Получить файл по имени                        |
| all()                | Получить все файлы                            |
| isUploaded()         | Проверить, был ли файл загружен               |
| save($path)          | Сохранить файл                                |
| getOriginalName()    | Оригинальное имя файла                        |
| getSize()            | Размер файла                                  |
| getMimeType()        | MIME-тип файла                                |
| getExtension()       | Расширение файла                              |

### Headers
| Метод                | Описание                                      |
|----------------------|-----------------------------------------------|
| get($name, $def)     | Получить заголовок                            |
| all()                | Получить все заголовки                        |
| __set($name, $val)   | Установить заголовок                          |

### Server/Env
| Метод                | Описание                                      |
|----------------------|-----------------------------------------------|
| get($name, $def)     | Получить переменную сервера/окружения         |
| all()                | Получить все переменные                       |

---

## 💡 Примеры использования

### Глобальная функция `request()`
```php
// Получить параметр из любого источника (GET, POST, ...), либо объект Request
$id = request('id');
$name = request('name', 'default_name');
$request = request();

// Получить все данные запроса
$all = request()->all();

// Работа с файлами
$avatar = request()->files('avatar');
if ($avatar && $avatar->isUploaded()) {
    $avatar->save('/uploads/avatars/');
}

// Работа с cookie
request()->cookie->set('token', 'abc123');
$token = request()->cookie->get('token');

// Работа с сессией
request()->session->set('user_id', 42);
$userId = request()->session->get('user_id');
```

### Работа с заголовками
```php
$headers = request()->headers;
$userAgent = $headers->get('User-Agent');
$headers->X_Custom_Header = 'custom_value';
```

### Работа с JSON и XML
```php
// Если Content-Type: application/json
$data = request()->all(); // автоматически преобразуется в массив

// Если Content-Type: application/xml
$data = request()->all(); // автоматически преобразуется в массив
```

### Работа с GET/POST
```php
$get = request()->get;
$post = request()->post;

$name = $get->get('name');
$email = $post->get('email');
```

### Работа с серверными переменными и окружением
```php
$server = request()->server;
$env = request()->env;

$host = $server->get('HTTP_HOST');
$phpVersion = $env->get('PHP_VERSION');
```

### Работа с UploadFile
```php
$file = request()->files('avatar');
if ($file && $file->isUploaded()) {
    $file->save('/uploads/avatars/');
    echo $file->getOriginalName();
    echo $file->getSize();
    echo $file->getMimeType();
}
```

---

## 🛠️ Вспомогательные функции

- `request($key = null, $default = null)` — универсальный доступ к данным запроса
- `cookies($key = null, $default = null)` — доступ к cookie
- `session($key = null, $default = null)` — доступ к сессии
- `files($key = null)` — доступ к загруженным файлам
- `headers($key = null, $default = null)` — доступ к заголовкам
- `get($key = null, $default = null)` — доступ к GET
- `post($key = null, $default = null)` — доступ к POST
- `env($key = null, $default = null)` — доступ к переменным окружения

---

## 🔌 Интеграция с фреймворками

Библиотека не зависит от конкретного фреймворка и может быть использована в любом проекте на PHP (Laravel, Symfony, Yii, Slim, Zend и др.).

**Пример для Laravel:**
```php
// В контроллере
use CloudCastle\HttpRequest\Request;

public function index()
{
    $request = Request::getInstance();
    $userId = $request->get('user_id');
    // ...
}
```

**Пример для Slim:**
```php
// В middleware
use CloudCastle\HttpRequest\Request;

$app->add(function ($request, $handler) {
    $ccRequest = Request::getInstance();
    // ...
    return $handler->handle($request);
});
```

---

## 🧪 Тестирование

```bash
composer install
vendor/bin/phpunit --testdox
```

---

## ❓ FAQ

**Q: Как получить все параметры запроса?**
A: `$all = request()->all();`

**Q: Как получить заголовок?**
A: `$userAgent = request()->headers->get('User-Agent');`

**Q: Как загрузить файл?**
A: `request()->files('avatar')->save('/uploads/');`

**Q: Как работать с cookie?**
A: `request()->cookie->set('token', 'abc');`

**Q: Как сбросить singleton для тестов?**
A: `Request::resetInstance();`

**Q: Как добавить свой компонент?**
A: Создайте класс и добавьте его в Request через расширение.

---

## 📝 Лицензия и контакты

MIT © Алексей Зорин ([zorinalexey59292@gmail.com](mailto:zorinalexey59292@gmail.com))

- [GitHub проекта](https://github.com/zorinalexey/cloud-castle-http-request)
- [Вопросы и предложения](mailto:zorinalexey59292@gmail.com)

---

**CloudCastle HttpRequest** — ваш универсальный инструмент для работы с HTTP в PHP! 