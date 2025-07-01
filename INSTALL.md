# Руководство по установке CloudCastle HTTP Request

## 📋 Содержание

- [Системные требования](#системные-требования)
- [Установка](#установка)
- [Настройка](#настройка)
- [Проверка установки](#проверка-установки)
- [Конфигурация](#конфигурация)
- [Устранение неполадок](#устранение-неполадок)
- [Обновление](#обновление)
- [Удаление](#удаление)

## 🔧 Системные требования

### Минимальные требования

- **PHP**: 8.3 или выше
- **Расширения PHP**:
  - `ext-xml` - для работы с XML данными
  - `ext-simplexml` - для парсинга XML
  - `ext-json` - для работы с JSON (встроено в PHP 8.0+)
  - `ext-session` - для управления сессиями (обычно включено по умолчанию)

### Рекомендуемые требования

- **PHP**: 8.3 или выше
- **Composer**: 2.0 или выше
- **Веб-сервер**: Apache 2.4+ или Nginx 1.18+
- **Операционная система**: Linux, macOS, Windows

### Проверка требований

```bash
# Проверка версии PHP
php -v

# Проверка установленных расширений
php -m | grep -E "(xml|simplexml|json|session)"

# Проверка Composer
composer --version
```

## 📦 Установка

### Способ 1: Установка через Composer (рекомендуется)

```bash
# Установка в существующий проект
composer require cloud-castle/http-request

# Или с указанием версии
composer require cloud-castle/http-request:^1.0
```

### Способ 2: Ручная установка

```bash
# Клонирование репозитория
git clone https://github.com/zorinalexey/cloud-castle-http-request

# Переход в директорию
cd http-request

# Установка зависимостей
composer install
```

## ⚙️ Настройка

### 1. Автозагрузка

Убедитесь, что автозагрузчик Composer подключен в вашем проекте:

```php
<?php
// В начале файла
require_once 'vendor/autoload.php';

use CloudCastle\HttpRequest\Request;
```

### 2. Настройка веб-сервера

#### Apache (.htaccess)

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Настройка загрузки файлов
php_value upload_max_filesize 10M
php_value post_max_size 10M
php_value max_execution_time 300
php_value memory_limit 256M
```

#### Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/your/project;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Настройка загрузки файлов
    client_max_body_size 10M;
}
```

### 3. Настройка PHP

Добавьте в `php.ini` или `.htaccess`:

```ini
; Настройки для загрузки файлов
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
memory_limit = 256M

; Настройки сессий
session.gc_maxlifetime = 3600
session.cookie_lifetime = 0
session.use_strict_mode = 1

; Настройки безопасности
allow_url_fopen = Off
allow_url_include = Off
```

## ✅ Проверка установки

### 1. Создание тестового файла

Создайте файл `test_installation.php`:

```php
<?php
require_once 'vendor/autoload.php';

use CloudCastle\HttpRequest\Request;

try {
    // Получение экземпляра
    $request = Request::getInstance();
    
    // Проверка основных компонентов
    $components = [
        'session' => $request->session,
        'cookie' => $request->cookie,
        'server' => $request->server,
        'env' => $request->env,
        'headers' => $request->headers,
        'post' => $request->post,
        'get' => $request->get
    ];
    
    echo "✅ Установка прошла успешно!\n";
    echo "📦 Доступные компоненты:\n";
    
    foreach ($components as $name => $component) {
        echo "  - {$name}: " . get_class($component) . "\n";
    }
    
    // Проверка MIME-типов
    $mimeTypes = require 'src/inc/mime_types.php';
    echo "🎯 Поддерживаемых MIME-типов: " . count($mimeTypes) . "\n";
    
} catch (Exception $e) {
    echo "❌ Ошибка установки: " . $e->getMessage() . "\n";
}
```

### 2. Запуск теста

```bash
# Через командную строку
php test_installation.php

# Через веб-сервер
# Откройте http://your-domain.com/test_installation.php
```

### 3. Запуск статического анализа

```bash
# Проверка кода с PHPStan
composer run-script phpstan-analyse

# Запуск тестов
composer run-script unit-test
```

## 🔧 Конфигурация

### 1. Настройка времени жизни

```php
<?php
use CloudCastle\HttpRequest\Request;

// Настройка перед получением экземпляра
$request = Request::set(7200, 86400)->getInstance();
// 7200 секунд (2 часа) для сессий
// 86400 секунд (24 часа) для куки
```

### 2. Настройка директорий для загрузки

```php
<?php
// Создание директории для загрузок
$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Проверка прав доступа
if (!is_writable($uploadDir)) {
    chmod($uploadDir, 0755);
}
```

### 3. Настройка безопасности

```php
<?php
// Ограничение типов файлов
$allowedMimeTypes = [
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/pdf',
    'text/plain'
];

// Ограничение размера файлов (в байтах)
$maxFileSize = 5 * 1024 * 1024; // 5MB
```

## 🛠️ Устранение неполадок

### Частые проблемы

#### 1. Ошибка "Class not found"

```bash
# Решение: Переустановка автозагрузчика
composer dump-autoload
```

#### 2. Ошибка загрузки файлов

```bash
# Проверка прав доступа
ls -la uploads/
chmod 755 uploads/
chown www-data:www-data uploads/
```

#### 3. Ошибка сессий

```php
// Проверка настроек сессий
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.cookie_lifetime', 0);
```

#### 4. Ошибка MIME-типов

```php
// Проверка поддержки MIME-типов
$mimeTypes = require 'src/inc/mime_types.php';
if (!isset($mimeTypes['image/jpeg'])) {
    echo "Ошибка: MIME-типы не загружены";
}
```

### Логи ошибок

```bash
# Просмотр логов PHP
tail -f /var/log/php_errors.log

# Просмотр логов Apache
tail -f /var/log/apache2/error.log

# Просмотр логов Nginx
tail -f /var/log/nginx/error.log
```

### Отладка

```php
<?php
// Включение отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Логирование ошибок
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log');
```

## 🔄 Обновление

### Обновление через Composer

```bash
# Проверка доступных обновлений
composer outdated cloud-castle/http-request

# Обновление до последней версии
composer update cloud-castle/http-request

# Обновление до конкретной версии
composer require cloud-castle/http-request:^1.1
```

### Обновление вручную

```bash
# Создание резервной копии
cp -r vendor/cloud-castle/http-request vendor/cloud-castle/http-request.backup

# Обновление кода
git pull origin main

# Переустановка зависимостей
composer install
```

### Проверка после обновления

```bash
# Запуск тестов
composer run-script unit-test

# Статический анализ
composer run-script phpstan-analyse

# Проверка совместимости
php test_installation.php
```

## 🗑️ Удаление

### Удаление через Composer

```bash
# Удаление пакета
composer remove cloud-castle/http-request

# Очистка кэша
composer clear-cache
```

### Полное удаление

```bash
# Удаление директории
rm -rf vendor/cloud-castle/http-request

# Удаление из composer.json (если есть)
# Отредактируйте composer.json и удалите строку с "cloud-castle/http-request"

# Переустановка автозагрузчика
composer dump-autoload
```

## 📚 Дополнительные ресурсы

- [Документация PHP](https://www.php.net/docs.php)
- [Руководство Composer](https://getcomposer.org/doc/)
- [Документация Apache](https://httpd.apache.org/docs/)
- [Документация Nginx](https://nginx.org/en/docs/)
- [PSR-12 Coding Standards](https://www.php-fig.org/psr/psr-12/)

## 🆘 Поддержка

Если у вас возникли проблемы с установкой:

1. Проверьте раздел [Устранение неполадок](#устранение-неполадок)
2. Ознакомьтесь с [README.md](README.md)
3. Создайте issue в репозитории проекта
4. Обратитесь к автору: [zorinalexey59292@gmail.com](mailto:zorinalexey59292@gmail.com)

---

**Примечание**: Это руководство предполагает базовые знания PHP и веб-разработки. Если у вас нет опыта работы с PHP, рекомендуется сначала изучить основы языка. 