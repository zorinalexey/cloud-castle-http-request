# CloudCastle HTTP Request Library

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/Tests-145%20passed-brightgreen.svg)](tests)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%208-brightgreen.svg)](https://phpstan.org)

Современная PHP библиотека для работы с HTTP запросами, предоставляющая удобный и безопасный способ доступа к данным запроса через паттерн Singleton.

## 🚀 Возможности

- **Singleton Pattern** - единый экземпляр для всего приложения
- **Автоматическая обработка JSON/XML** - автоматическое декодирование данных
- **Безопасный доступ к данным** - валидация и санитизация входных данных
- **Поддержка файлов** - загрузка и обработка файлов
- **Управление сессиями** - автоматическое управление временем жизни
- **Полная типизация** - поддержка PHP 8.1+ с строгой типизацией
- **100% покрытие тестами** - надежность и стабильность
- **PHPStan Level 8** - высочайшее качество кода

## 📋 Требования

- PHP 8.1 или выше
- Composer

## 🔧 Установка

```bash
composer require cloudcastle/http-request
```

Или добавьте в `composer.json`:

```json
{
    "require": {
        "cloudcastle/http-request": "^1.0"
    }
}
```

## 🎯 Быстрый старт

```php
<?php

use CloudCastle\HttpRequest\Request;

// Получить экземпляр Request
$request = Request::getInstance();

// Доступ к данным POST
$name = $request->post->get('name');
$email = $request->post->email; // Магический геттер

// Доступ к данным GET
$page = $request->get->get('page', 1); // С значением по умолчанию

// Доступ к файлам
$file = $request->files->get('upload');
if ($file && $file->isUploaded()) {
    $file->save('/path/to/uploads/');
}

// Доступ к заголовкам
$userAgent = $request->headers->get('User-Agent');

// Доступ к сессии
$request->session->set('user_id', 123);
$userId = $request->session->get('user_id');
```

## 📚 Подробное руководство

### Основные классы

#### Request - Главный класс

```php
use CloudCastle\HttpRequest\Request;

// Инициализация с настройкой времени жизни
$request = Request::init(3600, 7200); // 1 час для сессии, 2 часа для cookie

// Получение экземпляра
$request = Request::getInstance();

// Доступ к данным через магические геттеры
$postData = $request->post;
$getData = $request->get;
$files = $request->files;
$headers = $request->headers;
$session = $request->session;
$cookies = $request->cookies;
$server = $request->server;
$env = $request->env;
```

#### Работа с POST данными

```php
use CloudCastle\HttpRequest\Http\Post;

$post = Post::getInstance();

// Получение данных
$name = $post->get('name');
$email = $post->get('email', 'default@example.com');

// Магические геттеры
$title = $post->title;
$content = $post->content;

// Проверка существования
if ($post->has('user_id')) {
    $userId = $post->get('user_id');
}

// Получение всех данных
$allData = $post->all();

// Установка данных
$post->set('status', 'active');
$post->status = 'active'; // Магический сеттер
```

#### Работа с GET данными

```php
use CloudCastle\HttpRequest\Http\Get;

$get = Get::getInstance();

// Получение параметров
$page = $get->get('page', 1);
$sort = $get->get('sort', 'asc');

// Магические геттеры
$category = $get->category;
$search = $get->search;

// Валидация числовых параметров
$limit = $get->get('limit', 10);
if (is_numeric($limit)) {
    $limit = (int) $limit;
}
```

#### Работа с файлами

```php
use CloudCastle\HttpRequest\Http\Files;
use CloudCastle\HttpRequest\Http\UploadFile;

$files = Files::getInstance();

// Получение файла
$file = $files->get('document');

if ($file instanceof UploadFile) {
    // Проверка загрузки
    if ($file->isUploaded()) {
        // Сохранение файла
        $saved = $file->save('/uploads/documents/');
        
        if ($saved) {
            echo "Файл сохранен: " . $file->getOriginalName();
        }
    }
    
    // Информация о файле
    echo "Размер: " . $file->getSize() . " байт";
    echo "Тип: " . $file->getMimeType();
    echo "Расширение: " . $file->getExtension();
}

// Работа с множественными файлами
$multipleFiles = $files->get('images');
if (is_array($multipleFiles)) {
    foreach ($multipleFiles as $file) {
        if ($file->isUploaded()) {
            $file->save('/uploads/images/');
        }
    }
}
```

#### Работа с заголовками

```php
use CloudCastle\HttpRequest\Http\Headers;

$headers = Headers::getInstance();

// Получение заголовков
$contentType = $headers->get('Content-Type');
$userAgent = $headers->get('User-Agent');

// Магические геттеры
$accept = $headers->Accept;
$authorization = $headers->Authorization;

// Установка заголовков
$headers->set('X-Custom-Header', 'value');
$headers->X_Custom_Header = 'value'; // Магический сеттер

// Получение всех заголовков
$allHeaders = $headers->all();
```

#### Работа с сессиями

```php
use CloudCastle\HttpRequest\Http\Session;

$session = Session::getInstance();

// Установка времени жизни (в секундах)
$session = Session::setExpire(3600); // 1 час

// Работа с данными
$session->set('user_id', 123);
$session->set('user_name', 'John Doe');

// Получение данных
$userId = $session->get('user_id');
$userName = $session->get('user_name', 'Guest'); // С значением по умолчанию

// Магические геттеры/сеттеры
$session->theme = 'dark';
$theme = $session->theme;

// Удаление данных
$session->delete('user_id');

// Очистка всех данных
$session->clear();

// Проверка существования
if ($session->has('user_id')) {
    // Данные существуют
}
```

#### Работа с cookies

```php
use CloudCastle\HttpRequest\Http\Cookie;

$cookies = Cookie::getInstance();

// Получение cookies
$theme = $cookies->get('theme', 'light');
$language = $cookies->get('lang', 'en');

// Установка cookies
$cookies->set('theme', 'dark');
$cookies->set('lang', 'ru', 86400); // С временем жизни

// Магические геттеры
$preferences = $cookies->preferences;
```

#### Работа с серверными переменными

```php
use CloudCastle\HttpRequest\Server\Server;

$server = Server::getInstance();

// Получение серверных данных
$method = $server->get('REQUEST_METHOD');
$uri = $server->get('REQUEST_URI');
$ip = $server->get('REMOTE_ADDR');

// Магические геттеры
$userAgent = $server->HTTP_USER_AGENT;
$accept = $server->HTTP_ACCEPT;
```

#### Работа с переменными окружения

```php
use CloudCastle\HttpRequest\Server\Env;

$env = Env::getInstance();

// Получение переменных окружения
$dbHost = $env->get('DB_HOST', 'localhost');
$appEnv = $env->get('APP_ENV', 'production');

// Магические геттеры
$debug = $env->APP_DEBUG;
$timezone = $env->APP_TIMEZONE;
```

### Автоматическая обработка данных

Библиотека автоматически обрабатывает JSON и XML данные:

```php
// JSON данные автоматически декодируются
$jsonData = $request->post->get('json_field');
// Если json_field содержит '{"name": "John", "age": 30}'
// $jsonData будет объектом stdClass

// XML данные также обрабатываются
$xmlData = $request->post->get('xml_field');
// Если xml_field содержит XML, он будет преобразован в объект
```

### Обработка ошибок

```php
use CloudCastle\HttpRequest\Exceptions\InputException;

try {
    $request = Request::getInstance();
    $data = $request->post->get('required_field');
    
    if (!$data) {
        throw new InputException('Required field is missing');
    }
} catch (InputException $e) {
    // Обработка ошибок ввода
    error_log($e->getMessage());
}
```

### Клонирование

По умолчанию клонирование экземпляров запрещено для обеспечения паттерна Singleton:

```php
$request1 = Request::getInstance();
$request2 = Request::getInstance();

// Это работает - один и тот же экземпляр
var_dump($request1 === $request2); // true

// Это вызовет исключение
try {
    $request3 = clone $request1;
} catch (Exception $e) {
    echo "Клонирование запрещено";
}
```

## 🧪 Тестирование

Библиотека имеет полное покрытие тестами:

```bash
# Запуск всех тестов
composer test

# Запуск только юнит-тестов
composer unit-test

# Запуск тестов с покрытием
composer test-coverage

# Статический анализ кода
composer phpstan-analyse
```

## 📊 Статистика качества

- **145 тестов** - полное покрытие функциональности
- **168 утверждений** - тщательная проверка логики
- **PHPStan Level 8** - высочайший уровень статического анализа
- **0 предупреждений** - чистый код без замечаний
- **100% покрытие** - все методы и ветки протестированы

## 🔒 Безопасность

- Автоматическая валидация входных данных
- Защита от XSS атак
- Безопасная обработка файлов
- Валидация MIME-типов
- Проверка загруженных файлов

## 📝 Примеры использования

### Простая форма

```php
<?php
use CloudCastle\HttpRequest\Request;

$request = Request::getInstance();

if ($request->server->get('REQUEST_METHOD') === 'POST') {
    $name = $request->post->get('name');
    $email = $request->post->get('email');
    
    // Валидация
    if (empty($name) || empty($email)) {
        $error = 'Все поля обязательны';
    } else {
        // Обработка данных
        $request->session->set('user_name', $name);
        $request->session->set('user_email', $email);
        
        header('Location: /success');
        exit;
    }
}
```

### API обработчик

```php
<?php
use CloudCastle\HttpRequest\Request;

$request = Request::getInstance();

// Проверка метода
if ($request->server->get('REQUEST_METHOD') !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Проверка Content-Type
$contentType = $request->headers->get('Content-Type');
if (strpos($contentType, 'application/json') === false) {
    http_response_code(400);
    exit('Invalid Content-Type');
}

// Получение JSON данных
$data = $request->post->all();

// Обработка данных
$response = [
    'status' => 'success',
    'data' => $data
];

header('Content-Type: application/json');
echo json_encode($response);
```

### Загрузка файлов

```php
<?php
use CloudCastle\HttpRequest\Request;

$request = Request::getInstance();

if ($request->server->get('REQUEST_METHOD') === 'POST') {
    $file = $request->files->get('upload');
    
    if ($file && $file->isUploaded()) {
        // Проверка типа файла
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            $error = 'Неподдерживаемый тип файла';
        } else {
            // Сохранение файла
            $filename = uniqid() . '.' . $file->getExtension();
            if ($file->save('/uploads/', $filename)) {
                $success = 'Файл успешно загружен';
            } else {
                $error = 'Ошибка при сохранении файла';
            }
        }
    }
}
```

### REST API

```php
<?php
use CloudCastle\HttpRequest\Request;

$request = Request::getInstance();
$method = $request->server->get('REQUEST_METHOD');
$path = $request->server->get('REQUEST_URI');

switch ($method) {
    case 'GET':
        $id = $request->get->get('id');
        // Получение данных
        break;
        
    case 'POST':
        $data = $request->post->all();
        // Создание данных
        break;
        
    case 'PUT':
        $data = $request->post->all();
        $id = $request->get->get('id');
        // Обновление данных
        break;
        
    case 'DELETE':
        $id = $request->get->get('id');
        // Удаление данных
        break;
}
```

## 🤝 Вклад в проект

Мы приветствуем вклад в развитие библиотеки! Пожалуйста:

1. Форкните репозиторий
2. Создайте ветку для новой функции
3. Внесите изменения
4. Добавьте тесты
5. Отправьте Pull Request

## 📄 Лицензия

Этот проект распространяется под лицензией MIT. См. файл [LICENSE](LICENSE) для получения дополнительной информации.

## 🆘 Поддержка

Если у вас есть вопросы или проблемы:

- Создайте Issue в GitHub
- Обратитесь к документации
- Проверьте примеры использования

---

**CloudCastle HTTP Request Library** - надежное решение для работы с HTTP запросами в PHP приложениях. 