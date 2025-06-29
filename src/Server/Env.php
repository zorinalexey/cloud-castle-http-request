<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Server;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;
use stdClass;

/**
 * Class Env
 * 
 * Управляет переменными окружения в PHP приложении. Предоставляет удобный интерфейс
 * для работы с переменными окружения через паттерн Singleton. Автоматически обрабатывает
 * различные типы данных, включая JSON, булевы значения, числа и строки.
 * 
 * Класс автоматически загружает все переменные окружения при инициализации,
 * преобразует их в соответствующие типы данных и предоставляет методы для
 * безопасного доступа и установки новых переменных.
 * 
 * @package CloudCastle\HttpRequest\Server
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * @extends GetDataTrait<mixed>
 * 
 * @example
 * ```php
 * use CloudCastle\HttpRequest\Server\Env;
 * 
 * // Получение экземпляра (Singleton)
 * $env = Env::getInstance();
 * 
 * // Получение переменных окружения
 * $dbHost = $env->get('DB_HOST');
 * $appEnv = $env->app_env;
 * $debug = $env->debug;
 * 
 * // Получение всех переменных
 * $allVars = $env->all();
 * 
 * // Установка новых переменных
 * $env->NEW_VAR = 'value';
 * $env->DEBUG_MODE = true;
 * $env->PORT = 8080;
 * 
 * // Работа с конфигурацией приложения
 * $config = [
 *     'database' => [
 *         'host' => $env->get('DB_HOST', 'localhost'),
 *         'port' => $env->get('DB_PORT', 3306),
 *         'name' => $env->get('DB_NAME'),
 *         'user' => $env->get('DB_USER'),
 *         'pass' => $env->get('DB_PASS')
 *     ],
 *     'app' => [
 *         'name' => $env->get('APP_NAME', 'My App'),
 *         'env' => $env->get('APP_ENV', 'production'),
 *         'debug' => $env->get('APP_DEBUG', false)
 *     ]
 * ];
 * 
 * // Проверка окружения
 * if ($env->get('APP_ENV') === 'production') {
 *     error_reporting(0);
 *     ini_set('display_errors', '0');
 * }
 * 
 * // Условная логика на основе переменных окружения
 * if ($env->get('CACHE_ENABLED', false)) {
 *     // Включить кэширование
 * }
 * 
 * if ($env->get('LOG_LEVEL', 'info') === 'debug') {
 *     // Включить детальное логирование
 * }
 * ```
 */
final class Env extends stdClass
{
    /**
     * @var array<string, mixed>
     */
    protected array $data = [];
    use GetDataTrait, GetInstanceTrait;
    
    /**
     * Создает экземпляр для тестирования
     * 
     * Внутренний метод для создания экземпляра класса в тестах,
     * обходя паттерн Singleton для изоляции тестов.
     * 
     * @return static Экземпляр Env
     * @since 1.0.0
     * @internal Метод предназначен для тестирования
     * 
     * @example
     * ```php
     * // В тестах
     * $env = Env::createForTesting();
     * $env->TEST_VAR = 'test_value';
     * 
     * // Проверка
     * $this->assertEquals('test_value', $env->get('TEST_VAR'));
     * 
     * // Изоляция тестов
     * $env1 = Env::createForTesting();
     * $env2 = Env::createForTesting();
     * 
     * $env1->VAR1 = 'value1';
     * $env2->VAR2 = 'value2';
     * 
     * // Каждый экземпляр независим
     * $this->assertNull($env2->get('VAR1'));
     * $this->assertNull($env1->get('VAR2'));
     * ```
     */
    public static function createForTesting(): static
    {
        return new static();
    }
    
    /**
     * Конструктор Env
     * 
     * Инициализирует объект Env, загружая все переменные окружения из $_ENV.
     * Автоматически обрабатывает JSON данные, преобразуя их в объекты или массивы.
     * Все ключи приводятся к нижнему регистру для единообразия.
     * 
     * Конструктор вызывается только внутри класса (паттерн Singleton).
     * 
     * @since 1.0.0
     * @internal Конструктор предназначен для внутреннего использования
     * 
     * @example
     * ```php
     * // Внутреннее использование (не рекомендуется)
     * $env = new Env();
     * 
     * // Правильное использование через Singleton
     * $env = Env::getInstance();
     * 
     * // Примеры переменных окружения, которые будут обработаны:
     * 
     * // Простые строки
     * // APP_NAME=My Application
     * // DB_HOST=localhost
     * 
     * // JSON данные
     * // CONFIG={"debug":true,"port":8080,"features":["cache","log"]}
     * // DATABASE={"host":"localhost","port":3306,"name":"mydb"}
     * 
     * // Булевы значения
     * // DEBUG=true
     * // CACHE_ENABLED=on
     * // MAINTENANCE_MODE=yes
     * 
     * // Числовые значения
     * // PORT=8080
     * // TIMEOUT=30.5
     * 
     * // После инициализации:
     * // $env->get('app_name') => 'My Application'
     * // $env->get('config') => (object)['debug' => true, 'port' => 8080, ...]
     * // $env->get('debug') => true
     * // $env->get('port') => 8080
     * ```
     */
    protected function __construct()
    {
        foreach ($_ENV as $key => $value) {
            if(json_validate($value)){
                $value = json_decode($value);
            }
            
            $this->data[mb_strtolower($key)] = $value;
        }
    }
    
    /**
     * Магический сеттер для установки переменных окружения
     * 
     * Позволяет устанавливать переменные окружения через присваивание свойств.
     * Автоматически обрабатывает различные типы данных и преобразует их
     * в соответствующие форматы для системных переменных окружения.
     * 
     * Поддерживаемые преобразования:
     * - Булевы значения: true/false, on/off, yes/no, 1/0
     * - Числовые значения: автоматическое определение int/float
     * - Пустые строки: преобразуются в null
     * - Массивы и объекты: сериализуются в JSON
     * - Строки: сохраняются как есть
     * 
     * @param string $name Имя переменной окружения
     * @param mixed $value Значение переменной
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $env = Env::getInstance();
     * 
     * // Установка простых строк
     * $env->APP_NAME = 'My Application';
     * $env->DB_HOST = 'localhost';
     * 
     * // Установка булевых значений
     * $env->DEBUG = true;
     * $env->CACHE_ENABLED = 'on';
     * $env->MAINTENANCE_MODE = 'yes';
     * $env->FEATURE_FLAG = 1;
     * 
     * // Установка числовых значений
     * $env->PORT = 8080;
     * $env->TIMEOUT = 30.5;
     * $env->MAX_CONNECTIONS = '100';
     * 
     * // Установка пустых значений
     * $env->EMPTY_VAR = '';
     * $env->NULL_VAR = null;
     * 
     * // Установка массивов и объектов
     * $env->CONFIG = [
     *     'debug' => true,
     *     'port' => 8080,
     *     'features' => ['cache', 'log', 'api']
     * ];
     * 
     * $env->DATABASE = (object)[
     *     'host' => 'localhost',
     *     'port' => 3306,
     *     'name' => 'mydb'
     * ];
     * 
     * // Проверка установленных значений
     * echo $env->get('app_name'); // 'My Application'
     * echo $env->get('debug'); // true
     * echo $env->get('port'); // 8080
     * echo $env->get('empty_var'); // null
     * echo $env->get('config')->debug; // true
     * 
     * // Установка переменных для разных окружений
     * if ($env->get('app_env') === 'development') {
     *     $env->LOG_LEVEL = 'debug';
     *     $env->CACHE_TTL = 0;
     *     $env->ERROR_REPORTING = 'E_ALL';
     * } else {
     *     $env->LOG_LEVEL = 'error';
     *     $env->CACHE_TTL = 3600;
     *     $env->ERROR_REPORTING = 'E_ERROR';
     * }
     * 
     * // Установка конфигурации для внешних сервисов
     * $env->REDIS_CONFIG = [
     *     'host' => '127.0.0.1',
     *     'port' => 6379,
     *     'database' => 0,
     *     'timeout' => 2.5
     * ];
     * 
     * $env->SMTP_CONFIG = [
     *     'host' => 'smtp.gmail.com',
     *     'port' => 587,
     *     'encryption' => 'tls',
     *     'username' => 'user@gmail.com',
     *     'password' => 'password'
     * ];
     * 
     * // Установка флагов функций
     * $env->FEATURES = [
     *     'api_v2' => true,
     *     'websocket' => false,
     *     'caching' => true,
     *     'monitoring' => 'on'
     * ];
     * 
     * // Установка переменных для безопасности
     * $env->JWT_SECRET = 'your-secret-key';
     * $env->ENCRYPTION_KEY = '32-character-encryption-key';
     * $env->SESSION_SECURE = true;
     * $env->CSRF_PROTECTION = 'on';
     * 
     * // Установка переменных для производительности
     * $env->OPCACHE_ENABLED = true;
     * $env->MEMORY_LIMIT = '256M';
     * $env->MAX_EXECUTION_TIME = 30;
     * $env->UPLOAD_MAX_FILESIZE = '10M';
     * 
     * // Установка переменных для логирования
     * $env->LOG_CONFIG = [
     *     'level' => 'info',
     *     'path' => '/var/log/app',
     *     'max_files' => 30,
     *     'format' => 'json'
     * ];
     * 
     * // Установка переменных для мониторинга
     * $env->MONITORING = [
     *     'enabled' => true,
     *     'endpoint' => 'https://monitoring.example.com',
     *     'interval' => 60,
     *     'metrics' => ['cpu', 'memory', 'disk', 'network']
     * ];
     * ```
     */
    public function __set(string $name, mixed $value): void
    {
        $_ENV[$name] = $value;
        
        if(is_string($value) && in_array($value, ['true', 'on', 'yes', '1'])){
            $value = true;
        }
        
        if(is_string($value) && in_array($value, ['false', 'off', 'no', '0'])){
            $value = false;
        }
        
        if(is_string($value) && empty($value)){
            $value = null;
        }
        
        if(is_numeric($value)){
            if(is_float($value)){
                $value = (float)$value;
            }else{
                $value = (int)$value;
            }
        }
        
        if(is_array($value) || is_object($value) || is_bool($value)){
            $value = json_encode($value);
        }
        
        putenv("$name=".escapeshellarg($value));
        $this->data[mb_strtolower($name)] = $value;
    }
}