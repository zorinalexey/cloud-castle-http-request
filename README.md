# CloudCastle HTTP Request

[![PHP Version](https://img.shields.io/badge/PHP-8.3+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Static Analysis](https://img.shields.io/badge/PHPStan-Level%208-brightgreen.svg)](https://phpstan.org)

Библиотека для удобной работы с HTTP запросами в PHP. Предоставляет единую точку доступа к данным запроса через паттерн Singleton с автоматическим парсингом JSON и XML данных.

## 🚀 Особенности

- **Singleton паттерн** - единая точка доступа к данным запроса
- **Автоматический парсинг** JSON и XML данных из тела запроса
- **Управление сессиями и куками** с настраиваемым временем жизни
- **Поддержка загрузки файлов** с автоматическим определением MIME-типов
- **Полная поддержка HTTP методов** (GET, POST, PUT, PATCH, DELETE)
- **Типизированные интерфейсы** для безопасной работы с данными
- **Современный PHP 8.3+** с строгой типизацией
- **Статический анализ** с PHPStan Level 8

## 📦 Установка

### Через Composer

```bash
composer require cloud-castle/http-request
```

### Требования

- PHP 8.3 или выше
- Расширение XML
- Расширение SimpleXML

## 🔧 Быстрый старт

```php
<?php

use CloudCastle\HttpRequest\Request;

// Получение экземпляра запроса
$request = Request::getInstance();

// Доступ к данным
$postData = $request->post;
$getData = $request->get;
$session = $request->session;
$cookies = $request->cookie;
$files = $request->files;
$headers = $request->headers;
```

## 📖 Подробное использование

### Настройка времени жизни

```php
// Настройка времени жизни сессий и куки
$request = Request::set(7200, 86400);
// 7200 секунд (2 часа) для сессий
// 86400 секунд (24 часа) для куки
```

### Работа с GET параметрами

```php
$request = Request::getInstance();

// Получение GET параметра
$id = $request->get->get('id');
$name = $request->get->get('name', 'default_value');

// Получение всех GET параметров
$allParams = $request->get->all();

// Проверка существования параметра
if ($request->get->has('search')) {
    $search = $request->get->get('search');
}
```

### Работа с POST данными

```php
$request = Request::getInstance();

// Получение POST параметра
$username = $request->post->get('username');
$email = $request->post->get('email', '');

// Получение всех POST данных
$allData = $request->post->all();

// Работа с JSON данными (автоматически парсится)
$jsonData = $request->post->get('data');
```

### Управление сессиями

```php
$request = Request::getInstance();

// Установка значения в сессию
$request->session->set('user_id', 123);
$request->session->set('user_name', 'John Doe');

// Получение значения из сессии
$userId = $request->session->get('user_id');
$userName = $request->session->get('user_name', 'Guest');

// Удаление значения
$request->session->delete('user_id');

// Очистка всей сессии
$request->session->clear();

// Проверка существования
if ($request->session->has('user_id')) {
    // Пользователь авторизован
}
```

### Работа с куками

```php
$request = Request::getInstance();

// Установка куки
$request->cookie->set('theme', 'dark');
$request->cookie->set('language', 'ru', 86400); // с временем жизни

// Получение куки
$theme = $request->cookie->get('theme');
$language = $request->cookie->get('language', 'en');

// Удаление куки
$request->cookie->delete('theme');

// Получение всех куки
$allCookies = $request->cookie->all();
```

### Загрузка файлов

```php
$request = Request::getInstance();

// Получение загруженного файла
$uploadedFile = $request->files['userfile'];

// Проверка загрузки
if ($uploadedFile->isUploaded()) {
    // Сохранение файла
    $uploadedFile->save('/path/to/uploads/');
    
    // Получение информации о файле
    $originalName = $uploadedFile->getOriginalName();
    $mimeType = $uploadedFile->getMimeType();
    $size = $uploadedFile->getSize();
    $extension = $uploadedFile->getExtension();
}
```

### Работа с заголовками

```php
$request = Request::getInstance();

// Получение заголовка
$userAgent = $request->headers->get('User-Agent');
$contentType = $request->headers->get('Content-Type');

// Получение всех заголовков
$allHeaders = $request->headers->all();

// Проверка существования заголовка
if ($request->headers->has('Authorization')) {
    $token = $request->headers->get('Authorization');
}
```

### Серверные переменные

```php
$request = Request::getInstance();

// Получение серверных переменных
$method = $request->server->get('REQUEST_METHOD');
$uri = $request->server->get('REQUEST_URI');
$ip = $request->server->get('REMOTE_ADDR');

// Получение всех серверных переменных
$allServerVars = $request->server->all();
```

### Переменные окружения

```php
$request = Request::getInstance();

// Получение переменной окружения
$dbHost = $request->env->get('DB_HOST');
$appEnv = $request->env->get('APP_ENV', 'production');

// Получение всех переменных окружения
$allEnvVars = $request->env->all();
```

## 🗂️ Структура проекта

```
src/
├── Common/                 # Общие классы
│   ├── AbstractSingleton.php
│   ├── AbstractStorage.php
│   ├── Cookie.php
│   ├── Env.php
│   ├── Files.php
│   ├── Get.php
│   ├── Headers.php
│   ├── Post.php
│   ├── Server.php
│   ├── Session.php
│   └── UploadFile.php
├── Exceptions/             # Исключения
│   └── StorageException.php
├── Interfaces/             # Интерфейсы
│   ├── GetterInterface.php
│   ├── HttpRequestInterface.php
│   ├── SingletonInterface.php
│   └── StorageInterface.php
├── inc/                    # Вспомогательные файлы
│   └── mime_types.php      # MIME-типы файлов
└── Request.php             # Основной класс
```

## 🎯 Поддерживаемые MIME-типы

Библиотека поддерживает широкий спектр MIME-типов для автоматического определения расширений файлов:

### Изображения
- **Стандартные**: JPEG, PNG, GIF, WebP, SVG, BMP, TIFF, ICO
- **RAW форматы**: CR2, NEF, ARW, RAF, ORF, PEF, SRW, DCR
- **Профессиональные**: Cineon, DPX, OpenEXR, HDR, JPEG 2000

### Документы
- **Microsoft Office**: DOC, DOCX, XLS, XLSX, PPT, PPTX, RTF
- **OpenDocument**: ODT, ODS, ODP, ODG, ODF
- **Apple iWork**: Pages, Numbers, Keynote
- **Google Workspace**: GDoc, GSheet, GSlides

### Мультимедиа
- **Аудио**: MP3, WAV, OGG, FLAC, AAC, MIDI
- **Видео**: MP4, AVI, MOV, WMV, WebM, MKV

### Архивы и другие
- **Архивы**: ZIP, RAR, 7Z, TAR, GZ, BZ2
- **Шрифты**: TTF, OTF, WOFF, WOFF2, EOT
- **Программные файлы**: PHP, Python, Java, C#, VB

## 🧪 Тестирование

### Запуск тестов

```bash
composer run-script unit-test
```

### Статический анализ

```bash
composer run-script phpstan-analyse
```

### Генерация документации

```bash
composer run-script documentation-generate
```

## 📝 Примеры использования

### Обработка формы

```php
<?php

use CloudCastle\HttpRequest\Request;

$request = Request::getInstance();

if ($request->server->get('REQUEST_METHOD') === 'POST') {
    $username = $request->post->get('username');
    $email = $request->post->get('email');
    $password = $request->post->get('password');
    
    // Валидация
    if (empty($username) || empty($email)) {
        $error = 'Все поля обязательны для заполнения';
    } else {
        // Сохранение в сессию
        $request->session->set('user', [
            'username' => $username,
            'email' => $email
        ]);
        
        // Установка куки
        $request->cookie->set('last_login', date('Y-m-d H:i:s'));
        
        $success = 'Данные успешно сохранены';
    }
}
```

### API обработчик

```php
<?php

use CloudCastle\HttpRequest\Request;

$request = Request::getInstance();

// Проверка метода запроса
switch ($request->server->get('REQUEST_METHOD')) {
    case 'GET':
        $id = $request->get->get('id');
        $data = ['id' => $id, 'method' => 'GET'];
        break;
        
    case 'POST':
        // Автоматический парсинг JSON
        $data = $request->post->all();
        break;
        
    case 'PUT':
        $data = $request->post->all();
        break;
        
    case 'DELETE':
        $id = $request->get->get('id');
        $data = ['id' => $id, 'method' => 'DELETE'];
        break;
        
    default:
        http_response_code(405);
        exit('Method Not Allowed');
}

// Отправка JSON ответа
header('Content-Type: application/json');
echo json_encode($data);
```

### Загрузка файлов

```php
<?php

use CloudCastle\HttpRequest\Request;

$request = Request::getInstance();

if ($request->server->get('REQUEST_METHOD') === 'POST') {
    $uploadedFile = $request->files['document'];
    
    if ($uploadedFile->isUploaded()) {
        // Проверка типа файла
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        
        if (in_array($uploadedFile->getMimeType(), $allowedTypes)) {
            // Сохранение с уникальным именем
            $filename = uniqid() . '.' . $uploadedFile->getExtension();
            $uploadedFile->save('/uploads/', $filename);
            
            $success = 'Файл успешно загружен: ' . $filename;
        } else {
            $error = 'Неподдерживаемый тип файла';
        }
    } else {
        $error = 'Ошибка загрузки файла';
    }
}
```

## 🤝 Вклад в проект

Мы приветствуем вклад в развитие библиотеки! Пожалуйста, ознакомьтесь с [CONTRIBUTING.md](CONTRIBUTING.md) для получения подробной информации о процессе разработки.

### Требования к коду

- Следуйте стандартам PSR-12
- Добавляйте тесты для новой функциональности
- Обновляйте документацию при необходимости
- Используйте строгую типизацию PHP 8.3+

## 📄 Лицензия

Этот проект распространяется под лицензией MIT. См. файл [LICENSE](LICENSE) для получения подробной информации.

## 👨‍💻 Автор

**Алексей Зорин** - [zorinalexey59292@gmail.com](mailto:zorinalexey59292@gmail.com)

## 🔗 Полезные ссылки

- [Документация PHP](https://www.php.net/docs.php)
- [PSR-12: Extended Coding Style](https://www.php-fig.org/psr/psr-12/)
- [PHPStan - Static Analysis Tool](https://phpstan.org/)
- [PHPUnit - Testing Framework](https://phpunit.de/)

---

⭐ Если этот проект оказался полезным, поставьте звезду на GitHub! # cloud-castle-http-request
