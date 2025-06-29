<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;
use stdClass;

/**
 * Class Headers
 * 
 * Управляет HTTP-заголовками запроса и ответа. Предоставляет удобный интерфейс для работы
 * с HTTP-заголовками через методы и магические свойства. Реализует паттерн Singleton
 * для обеспечения единственного экземпляра класса.
 * 
 * Класс автоматически обрабатывает различные источники заголовков (Apache, Nginx, $_SERVER),
 * нормализует имена заголовков и поддерживает установку заголовков в HTTP-ответе.
 * Автоматически обрабатывает JSON-данные в заголовках, преобразуя их в объекты PHP.
 * 
 * @package CloudCastle\HttpRequest\Http
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * 
 * @example
 * ```php
 * use CloudCastle\HttpRequest\Http\Headers;
 * 
 * // Получение экземпляра заголовков
 * $headers = Headers::getInstance();
 * 
 * // Получение заголовков запроса
 * $contentType = $headers->get('Content-Type');
 * $userAgent = $headers->get('User-Agent', 'Unknown');
 * 
 * // Использование магических методов для получения
 * $accept = $headers->accept;
 * $authorization = $headers->authorization;
 * 
 * // Получение всех заголовков
 * $allHeaders = $headers->all();
 * 
 * // Установка заголовков в ответе
 * $headers->Content_Type = 'application/json';
 * $headers->X_Custom_Header = 'custom_value';
 * $headers->Cache_Control = 'no-cache';
 * 
 * // Установка JSON-заголовков
 * $headers->X_User_Data = ['id' => 123, 'name' => 'John'];
 * ```
 */
final class Headers extends stdClass
{
    /**
     * @var array<string, mixed> Массив HTTP-заголовков
     * 
     * Хранит все HTTP-заголовки в виде ассоциативного массива.
     * Ключи автоматически приводятся к нижнему регистру, JSON-данные
     * преобразуются в объекты PHP.
     * 
     * @example
     * ```php
     * // Внутренняя структура массива
     * $this->data = [
     *     'content_type' => 'application/json',
     *     'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
     *     'accept' => 'text/html,application/xhtml+xml,application/xml',
     *     'authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...',
     *     'x_custom_data' => (object)['user_id' => 123, 'role' => 'admin'], // JSON преобразован в объект
     *     'x_api_version' => 'v2.1'
     * ];
     * 
     * // Исходные заголовки:
     * // Content-Type: application/json
     * // User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)
     * // X-Custom-Data: {"user_id":123,"role":"admin"}
     * ```
     */
    protected array $data = [];
    
    use GetDataTrait, GetInstanceTrait;
    
    /**
     * Создает экземпляр класса для тестирования
     * 
     * Позволяет создать новый экземпляр класса в обход singleton паттерна
     * для целей тестирования. Не предназначен для использования в продакшене.
     * 
     * @return static Новый экземпляр класса Headers
     * @since 1.0.0
     * @internal Метод предназначен только для тестирования
     * 
     * @example
     * ```php
     * // В тестах
     * $headers1 = Headers::createForTesting();
     * $headers2 = Headers::createForTesting();
     * 
     * // Это будут разные экземпляры
     * $headers1->X_Test_Header = 'value1';
     * $headers2->X_Test_Header = 'value2';
     * 
     * assert($headers1->get('X_Test_Header') === 'value1');
     * assert($headers2->get('X_Test_Header') === 'value2');
     * ```
     */
    public static function createForTesting(): static
    {
        return new static();
    }
    
    /**
     * Защищенный конструктор класса Headers
     * 
     * Инициализирует объект Headers, загружая все HTTP-заголовки из различных
     * источников. Автоматически обрабатывает JSON-данные и нормализует имена заголовков.
     * 
     * Источники заголовков (в порядке приоритета):
     * 1. apache_request_headers() - для Apache серверов
     * 2. getallheaders() - для других серверов
     * 3. $_SERVER - парсинг HTTP_* переменных
     * 
     * Процесс инициализации:
     * 1. Попытка получить заголовки через apache_request_headers()
     * 2. Если недоступно, попытка через getallheaders()
     * 3. Если недоступно, парсинг $_SERVER для HTTP_* переменных
     * 4. Обработка каждого заголовка (JSON валидация, нормализация)
     * 5. Сохранение в $this->data и установка как свойства объекта
     * 
     * @since 1.0.0
     * 
     * @example
     * ```php
     * // Конструктор вызывается автоматически при получении экземпляра
     * $headers = Headers::getInstance();
     * 
     * // В этот момент происходит обработка заголовков:
     * 
     * // Если входящие заголовки:
     * // Content-Type: application/json
     * // User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)
     * // Authorization: Bearer token123
     * // X-Custom-Data: {"user_id":123,"role":"admin"}
     * // Accept-Language: en-US,en;q=0.9
     * 
     * // То в $this->data будет:
     * // $this->data['content_type'] = 'application/json'
     * // $this->data['user_agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
     * // $this->data['authorization'] = 'Bearer token123'
     * // $this->data['x_custom_data'] = (object)['user_id' => 123, 'role' => 'admin']
     * // $this->data['accept_language'] = 'en-US,en;q=0.9'
     * 
     * // И как свойства объекта:
     * // $headers->content_type = 'application/json'
     * // $headers->user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
     * // $headers->authorization = 'Bearer token123'
     * // $headers->x_custom_data = (object)['user_id' => 123, 'role' => 'admin']
     * // $headers->accept_language = 'en-US,en;q=0.9'
     * 
     * // Примеры различных источников заголовков:
     * 
     * // Apache (apache_request_headers):
     * // ['Content-Type' => 'application/json', 'User-Agent' => '...']
     * 
     * // Nginx (getallheaders):
     * // ['Content-Type' => 'application/json', 'User-Agent' => '...']
     * 
     * // $_SERVER парсинг:
     * // $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json'
     * // $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0...'
     * // Результат: ['CONTENT_TYPE' => 'application/json', 'USER_AGENT' => '...']
     * ```
     */
    protected function __construct()
    {
        $headers = [];
        
        if(function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        }
        
        if(!$headers && function_exists('getallheaders')) {
            $headers = getallheaders();
        }
        
        if(!$headers) {
            foreach ($_SERVER as $key => $value) {
                $key = mb_strtoupper($key);
                
                if (str_starts_with($key, 'HTTP_')) {
                    $key = str_replace('HTTP_', '', $key);
                    $headers[$key] = $value;
                }
            }
        }
        
        foreach ($headers as $key => $value) {
            if(json_validate($value)){
                $value = json_decode($value);
            }
            
            $this->{$key} = $value;
        }
    }
    
    /**
     * Магический метод для установки HTTP-заголовков
     * 
     * Позволяет устанавливать HTTP-заголовки в ответе через присваивание свойству.
     * Автоматически преобразует различные типы данных в строки для отправки
     * и вызывает функцию header() для установки заголовка.
     * 
     * Поддерживаемые типы данных:
     * - Строки: отправляются как есть
     * - Числа: преобразуются в строки
     * - Булевы значения: преобразуются в 'true'/'false'
     * - Массивы и объекты: преобразуются в JSON
     * 
     * @param string $key Имя заголовка
     * @param mixed $value Значение заголовка
     * @return void
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $headers = Headers::getInstance();
     * 
     * // Установка простых заголовков
     * $headers->Content_Type = 'application/json';
     * $headers->X_Custom_Header = 'custom_value';
     * $headers->Cache_Control = 'no-cache, no-store, must-revalidate';
     * $headers->Access_Control_Allow_Origin = '*';
     * 
     * // Установка числовых значений
     * $headers->X_User_ID = 123;
     * $headers->X_Api_Version = 2.1;
     * 
     * // Установка булевых значений
     * $headers->X_Is_Authenticated = true; // Отправит: X-Is-Authenticated: true
     * $headers->X_Is_Admin = false; // Отправит: X-Is-Admin: false
     * 
     * // Установка массивов (преобразуются в JSON)
     * $headers->X_User_Data = [
     *     'id' => 123,
     *     'name' => 'John Doe',
     *     'role' => 'admin'
     * ];
     * // Отправит: X-User-Data: {"id":123,"name":"John Doe","role":"admin"}
     * 
     * // Установка объектов (преобразуются в JSON)
     * $user = new stdClass();
     * $user->id = 123;
     * $user->name = 'John Doe';
     * $headers->X_User_Object = $user;
     * // Отправит: X-User-Object: {"id":123,"name":"John Doe"}
     * 
     * // Установка сложных структур
     * $headers->X_Response_Meta = [
     *     'timestamp' => time(),
     *     'version' => '1.0.0',
     *     'features' => ['feature1', 'feature2'],
     *     'config' => [
     *         'debug' => false,
     *         'cache' => true
     *     ]
     * ];
     * 
     * // Установка заголовков авторизации
     * $headers->Authorization = 'Bearer ' . $token;
     * $headers->X_API_Key = $apiKey;
     * 
     * // Установка заголовков кэширования
     * $headers->ETag = '"abc123"';
     * $headers->Last_Modified = gmdate('D, d M Y H:i:s') . ' GMT';
     * 
     * // Установка CORS заголовков
     * $headers->Access_Control_Allow_Methods = 'GET, POST, PUT, DELETE';
     * $headers->Access_Control_Allow_Headers = 'Content-Type, Authorization';
     * 
     * // Цепочка установки заголовков
     * $headers->Content_Type = 'application/json'
     *         ->X_Custom_Header = 'value'
     *         ->Cache_Control = 'no-cache';
     * 
     * // Перезапись заголовков
     * $headers->Content_Type = 'text/html'; // Перезаписывает предыдущее значение
     * 
     * // Установка заголовков в зависимости от условий
     * if ($user->isAdmin()) {
     *     $headers->X_User_Role = 'admin';
     *     $headers->X_Access_Level = 'full';
     * } else {
     *     $headers->X_User_Role = 'user';
     *     $headers->X_Access_Level = 'limited';
     * }
     * 
     * // Установка заголовков для API ответов
     * $headers->Content_Type = 'application/json';
     * $headers->X_API_Version = 'v2.1';
     * $headers->X_Request_ID = uniqid();
     * $headers->X_Response_Time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
     * ```
     */
    public function __set(string $key, mixed $value): void
    {
        $this->data[mb_strtolower($key)] = $value;
        
        // Преобразуем значение в строку для header()
        if (is_object($value) || is_array($value)) {
            $headerValue = json_encode($value);
        }elseif(is_bool($value)) {
            $headerValue = $value ? 'true' : 'false';
        }else{
            $headerValue = (string) $value;
        }
        
        header($key.': '.$headerValue);
    }
}