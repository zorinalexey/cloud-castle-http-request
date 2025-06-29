<?php

use CloudCastle\HttpRequest\Server\Env;

/**
 * Глобальная вспомогательная функция для доступа к переменным окружения
 * 
 * Предоставляет удобный способ работы с переменными окружения PHP.
 * Если передан ключ, возвращает значение конкретной переменной окружения,
 * иначе возвращает объект Env для работы со всеми переменными окружения.
 * 
 * Функция автоматически обрабатывает различные типы данных и предоставляет
 * безопасный доступ к конфигурации приложения, включая настройки базы данных,
 * API ключи, режимы работы и другие параметры окружения.
 *
 * @param string|null $key Имя переменной окружения (опционально)
 * @param mixed $default Значение по умолчанию, если переменная не найдена
 * @return mixed|Env Значение переменной или объект Env
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 *
 * @example
 * ```php
 * // Получение отдельных переменных окружения
 * $dbHost = env('DB_HOST');
 * $appEnv = env('APP_ENV', 'production');
 * $debug = env('APP_DEBUG', false);
 * $timezone = env('APP_TIMEZONE', 'UTC');
 * 
 * // Получение объекта env для работы со всеми переменными
 * $env = env();
 * 
 * // Работа с настройками базы данных
 * $dbConfig = [
 *     'host' => env('DB_HOST', 'localhost'),
 *     'port' => env('DB_PORT', 3306),
 *     'database' => env('DB_DATABASE'),
 *     'username' => env('DB_USERNAME'),
 *     'password' => env('DB_PASSWORD'),
 *     'charset' => env('DB_CHARSET', 'utf8mb4'),
 *     'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci')
 * ];
 * 
 * // Создание подключения к базе данных
 * $pdo = new PDO(
 *     "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}",
 *     $dbConfig['username'],
 *     $dbConfig['password']
 * );
 * 
 * // Работа с Redis
 * $redisConfig = [
 *     'host' => env('REDIS_HOST', '127.0.0.1'),
 *     'port' => env('REDIS_PORT', 6379),
 *     'password' => env('REDIS_PASSWORD'),
 *     'database' => env('REDIS_DB', 0)
 * ];
 * 
 * // Работа с почтовыми настройками
 * $mailConfig = [
 *     'driver' => env('MAIL_DRIVER', 'smtp'),
 *     'host' => env('MAIL_HOST'),
 *     'port' => env('MAIL_PORT', 587),
 *     'username' => env('MAIL_USERNAME'),
 *     'password' => env('MAIL_PASSWORD'),
 *     'encryption' => env('MAIL_ENCRYPTION', 'tls'),
 *     'from_address' => env('MAIL_FROM_ADDRESS'),
 *     'from_name' => env('MAIL_FROM_NAME')
 * ];
 * 
 * // Работа с API ключами
 * $apiKeys = [
 *     'google_maps' => env('GOOGLE_MAPS_API_KEY'),
 *     'stripe_public' => env('STRIPE_PUBLIC_KEY'),
 *     'stripe_secret' => env('STRIPE_SECRET_KEY'),
 *     'aws_access_key' => env('AWS_ACCESS_KEY_ID'),
 *     'aws_secret_key' => env('AWS_SECRET_ACCESS_KEY'),
 *     'aws_region' => env('AWS_DEFAULT_REGION', 'us-east-1')
 * ];
 * 
 * // Проверка режима работы приложения
 * $isProduction = env('APP_ENV') === 'production';
 * $isDevelopment = env('APP_ENV') === 'development';
 * $isTesting = env('APP_ENV') === 'testing';
 * 
 * // Настройка логирования в зависимости от окружения
 * if ($isProduction) {
 *     error_reporting(0);
 *     ini_set('display_errors', '0');
 *     ini_set('log_errors', '1');
 *     ini_set('error_log', env('LOG_PATH', '/var/log/app/error.log'));
 * } else {
 *     error_reporting(E_ALL);
 *     ini_set('display_errors', '1');
 * }
 * 
 * // Работа с URL и доменами
 * $appUrl = env('APP_URL', 'http://localhost');
 * $appName = env('APP_NAME', 'My Application');
 * $appVersion = env('APP_VERSION', '1.0.0');
 * 
 * // Настройки сессий
 * $sessionConfig = [
 *     'driver' => env('SESSION_DRIVER', 'file'),
 *     'lifetime' => env('SESSION_LIFETIME', 120),
 *     'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),
 *     'encrypt' => env('SESSION_ENCRYPT', false),
 *     'secure' => env('SESSION_SECURE_COOKIE', false),
 *     'http_only' => env('SESSION_HTTP_ONLY', true),
 *     'same_site' => env('SESSION_SAME_SITE', 'lax')
 * ];
 * 
 * // Работа с кэшированием
 * $cacheConfig = [
 *     'driver' => env('CACHE_DRIVER', 'file'),
 *     'prefix' => env('CACHE_PREFIX', 'app_cache'),
 *     'ttl' => env('CACHE_TTL', 3600)
 * ];
 * 
 * // Настройки очередей
 * $queueConfig = [
 *     'driver' => env('QUEUE_DRIVER', 'sync'),
 *     'connection' => env('QUEUE_CONNECTION', 'default'),
 *     'retry_after' => env('QUEUE_RETRY_AFTER', 90)
 * ];
 * 
 * // Работа с файловыми хранилищами
 * $storageConfig = [
 *     'driver' => env('FILESYSTEM_DRIVER', 'local'),
 *     'disk' => env('FILESYSTEM_DISK', 'local'),
 *     'visibility' => env('FILESYSTEM_VISIBILITY', 'public')
 * ];
 * 
 * // Настройки безопасности
 * $securityConfig = [
 *     'encryption_key' => env('APP_KEY'),
 *     'cipher' => env('APP_CIPHER', 'AES-256-CBC'),
 *     'csrf_token_lifetime' => env('CSRF_TOKEN_LIFETIME', 120),
 *     'password_timeout' => env('PASSWORD_TIMEOUT', 10800)
 * ];
 * 
 * // Работа с внешними сервисами
 * $services = [
 *     'google_analytics' => env('GOOGLE_ANALYTICS_ID'),
 *     'facebook_pixel' => env('FACEBOOK_PIXEL_ID'),
 *     'recaptcha_site_key' => env('RECAPTCHA_SITE_KEY'),
 *     'recaptcha_secret_key' => env('RECAPTCHA_SECRET_KEY'),
 *     'sentry_dsn' => env('SENTRY_DSN')
 * ];
 * 
 * // Настройки мониторинга
 * $monitoringConfig = [
 *     'enabled' => env('MONITORING_ENABLED', false),
 *     'endpoint' => env('MONITORING_ENDPOINT'),
 *     'interval' => env('MONITORING_INTERVAL', 60),
 *     'metrics' => explode(',', env('MONITORING_METRICS', 'cpu,memory,disk'))
 * ];
 * 
 * // Работа с локализацией
 * $localizationConfig = [
 *     'locale' => env('APP_LOCALE', 'en'),
 *     'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
 *     'timezone' => env('APP_TIMEZONE', 'UTC'),
 *     'date_format' => env('APP_DATE_FORMAT', 'Y-m-d'),
 *     'time_format' => env('APP_TIME_FORMAT', 'H:i:s')
 * ];
 * 
 * // Настройки оптимизации
 * $optimizationConfig = [
 *     'opcache_enabled' => env('OPCACHE_ENABLED', true),
 *     'memory_limit' => env('MEMORY_LIMIT', '256M'),
 *     'max_execution_time' => env('MAX_EXECUTION_TIME', 30),
 *     'upload_max_filesize' => env('UPLOAD_MAX_FILESIZE', '10M'),
 *     'post_max_size' => env('POST_MAX_SIZE', '10M')
 * ];
 * 
 * // Работа с WebSocket
 * $websocketConfig = [
 *     'enabled' => env('WEBSOCKET_ENABLED', false),
 *     'host' => env('WEBSOCKET_HOST', '0.0.0.0'),
 *     'port' => env('WEBSOCKET_PORT', 6001),
 *     'ssl' => env('WEBSOCKET_SSL', false)
 * ];
 * 
 * // Настройки API
 * $apiConfig = [
 *     'version' => env('API_VERSION', 'v1'),
 *     'rate_limit' => env('API_RATE_LIMIT', 60),
 *     'throttle' => env('API_THROTTLE', 1000),
 *     'cors_allowed_origins' => explode(',', env('API_CORS_ALLOWED_ORIGINS', '*'))
 * ];
 * 
 * // Работа с платежными системами
 * $paymentConfig = [
 *     'stripe' => [
 *         'public_key' => env('STRIPE_PUBLIC_KEY'),
 *         'secret_key' => env('STRIPE_SECRET_KEY'),
 *         'webhook_secret' => env('STRIPE_WEBHOOK_SECRET')
 *     ],
 *     'paypal' => [
 *         'client_id' => env('PAYPAL_CLIENT_ID'),
 *         'client_secret' => env('PAYPAL_CLIENT_SECRET'),
 *         'mode' => env('PAYPAL_MODE', 'sandbox')
 *     ]
 * ];
 * 
 * // Настройки социальных сетей
 * $socialConfig = [
 *     'facebook' => [
 *         'app_id' => env('FACEBOOK_APP_ID'),
 *         'app_secret' => env('FACEBOOK_APP_SECRET')
 *     ],
 *     'google' => [
 *         'client_id' => env('GOOGLE_CLIENT_ID'),
 *         'client_secret' => env('GOOGLE_CLIENT_SECRET')
 *     ],
 *     'twitter' => [
 *         'consumer_key' => env('TWITTER_CONSUMER_KEY'),
 *         'consumer_secret' => env('TWITTER_CONSUMER_SECRET')
 *     ]
 * ];
 * 
 * // Работа с уведомлениями
 * $notificationConfig = [
 *     'pusher' => [
 *         'app_id' => env('PUSHER_APP_ID'),
 *         'app_key' => env('PUSHER_APP_KEY'),
 *         'app_secret' => env('PUSHER_APP_SECRET'),
 *         'cluster' => env('PUSHER_APP_CLUSTER')
 *     ],
 *     'firebase' => [
 *         'project_id' => env('FIREBASE_PROJECT_ID'),
 *         'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID'),
 *         'private_key' => env('FIREBASE_PRIVATE_KEY'),
 *         'client_email' => env('FIREBASE_CLIENT_EMAIL')
 *     ]
 * ];
 * 
 * // Настройки поиска
 * $searchConfig = [
 *     'elasticsearch' => [
 *         'host' => env('ELASTICSEARCH_HOST', 'localhost'),
 *         'port' => env('ELASTICSEARCH_PORT', 9200),
 *         'index' => env('ELASTICSEARCH_INDEX', 'app_index')
 *     ],
 *     'algolia' => [
 *         'app_id' => env('ALGOLIA_APP_ID'),
 *         'search_key' => env('ALGOLIA_SEARCH_KEY'),
 *         'admin_key' => env('ALGOLIA_ADMIN_KEY')
 *     ]
 * ];
 * 
 * // Проверка обязательных переменных
 * $requiredEnvVars = [
 *     'DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD',
 *     'APP_KEY', 'APP_URL'
 * ];
 * 
 * foreach ($requiredEnvVars as $var) {
 *     if (empty(env($var))) {
 *         throw new Exception("Обязательная переменная окружения {$var} не установлена");
 *     }
 * }
 * 
 * // Создание конфигурации приложения
 * $config = [
 *     'app' => [
 *         'name' => env('APP_NAME', 'My Application'),
 *         'env' => env('APP_ENV', 'production'),
 *         'debug' => env('APP_DEBUG', false),
 *         'url' => env('APP_URL'),
 *         'timezone' => env('APP_TIMEZONE', 'UTC'),
 *         'locale' => env('APP_LOCALE', 'en'),
 *         'key' => env('APP_KEY'),
 *         'cipher' => env('APP_CIPHER', 'AES-256-CBC')
 *     ],
 *     'database' => $dbConfig,
 *     'redis' => $redisConfig,
 *     'mail' => $mailConfig,
 *     'session' => $sessionConfig,
 *     'cache' => $cacheConfig,
 *     'queue' => $queueConfig,
 *     'storage' => $storageConfig,
 *     'security' => $securityConfig,
 *     'services' => $services,
 *     'monitoring' => $monitoringConfig,
 *     'localization' => $localizationConfig,
 *     'optimization' => $optimizationConfig,
 *     'websocket' => $websocketConfig,
 *     'api' => $apiConfig,
 *     'payment' => $paymentConfig,
 *     'social' => $socialConfig,
 *     'notification' => $notificationConfig,
 *     'search' => $searchConfig
 * ];
 * 
 * // Условная логика на основе переменных окружения
 * if (env('APP_ENV') === 'production') {
 *     // Настройки для продакшена
 *     ini_set('memory_limit', '512M');
 *     ini_set('max_execution_time', 60);
 * } elseif (env('APP_ENV') === 'development') {
 *     // Настройки для разработки
 *     ini_set('display_errors', '1');
 *     ini_set('log_errors', '0');
 * }
 * 
 * // Работа с версиями
 * $version = env('APP_VERSION', '1.0.0');
 * $build = env('APP_BUILD', '1');
 * $commit = env('APP_COMMIT', 'unknown');
 * 
 * // Логирование конфигурации
 * $configLog = [
 *     'timestamp' => date('Y-m-d H:i:s'),
 *     'environment' => env('APP_ENV'),
 *     'version' => env('APP_VERSION'),
 *     'debug' => env('APP_DEBUG'),
 *     'database_host' => env('DB_HOST'),
 *     'cache_driver' => env('CACHE_DRIVER'),
 *     'queue_driver' => env('QUEUE_DRIVER')
 * ];
 * ```
 */
function env(string|null $key = null, mixed $default = null): mixed
{
    $env = Env::getInstance();
    
    if($key){
        return $env->get($key, $default);
    }
    
    return $env;
}