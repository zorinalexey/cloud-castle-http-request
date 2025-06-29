<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Server;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;
use stdClass;

/**
 * Class Server
 * 
 * Управляет данными сервера из суперглобального массива $_SERVER. Предоставляет удобный
 * интерфейс для доступа к информации о сервере, HTTP запросе и окружении через паттерн Singleton.
 * Автоматически обрабатывает JSON данные и приводит ключи к нижнему регистру.
 * 
 * Класс предоставляет доступ к важной информации о сервере, включая HTTP заголовки,
 * информацию о клиенте, пути запроса, методы HTTP и другие серверные переменные.
 * 
 * @package CloudCastle\HttpRequest\Server
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * @extends GetDataTrait<mixed>
 * 
 * @example
 * ```php
 * use CloudCastle\HttpRequest\Server\Server;
 * 
 * // Получение экземпляра (Singleton)
 * $server = Server::getInstance();
 * 
 * // Получение базовой информации о сервере
 * $host = $server->get('HTTP_HOST');
 * $userAgent = $server->http_user_agent;
 * $method = $server->request_method;
 * $uri = $server->request_uri;
 * 
 * // Получение всех данных сервера
 * $allData = $server->all();
 * 
 * // Работа с HTTP заголовками
 * $accept = $server->get('HTTP_ACCEPT');
 * $acceptLanguage = $server->get('HTTP_ACCEPT_LANGUAGE');
 * $acceptEncoding = $server->get('HTTP_ACCEPT_ENCODING');
 * $connection = $server->get('HTTP_CONNECTION');
 * 
 * // Информация о клиенте
 * $clientIP = $server->get('REMOTE_ADDR');
 * $clientPort = $server->get('REMOTE_PORT');
 * $forwardedFor = $server->get('HTTP_X_FORWARDED_FOR');
 * $realIP = $server->get('HTTP_X_REAL_IP');
 * 
 * // Информация о сервере
 * $serverName = $server->get('SERVER_NAME');
 * $serverPort = $server->get('SERVER_PORT');
 * $serverProtocol = $server->get('SERVER_PROTOCOL');
 * $serverSoftware = $server->get('SERVER_SOFTWARE');
 * 
 * // Пути и файлы
 * $documentRoot = $server->get('DOCUMENT_ROOT');
 * $scriptName = $server->get('SCRIPT_NAME');
 * $scriptFilename = $server->get('SCRIPT_FILENAME');
 * $pathInfo = $server->get('PATH_INFO');
 * 
 * // Проверка типа запроса
 * if ($server->get('REQUEST_METHOD') === 'POST') {
 *     // Обработка POST запроса
 * }
 * 
 * if ($server->get('REQUEST_METHOD') === 'GET') {
 *     // Обработка GET запроса
 * }
 * 
 * // Проверка AJAX запроса
 * if ($server->get('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') {
 *     // Это AJAX запрос
 * }
 * 
 * // Определение протокола
 * $isSecure = $server->get('HTTPS') === 'on';
 * $protocol = $isSecure ? 'https' : 'http';
 * 
 * // Получение полного URL
 * $fullUrl = $protocol . '://' . $server->get('HTTP_HOST') . $server->get('REQUEST_URI');
 * 
 * // Работа с заголовками авторизации
 * $authorization = $server->get('HTTP_AUTHORIZATION');
 * $bearerToken = null;
 * if ($authorization && strpos($authorization, 'Bearer ') === 0) {
 *     $bearerToken = substr($authorization, 7);
 * }
 * 
 * // Проверка мобильного устройства
 * $userAgent = $server->get('HTTP_USER_AGENT');
 * $isMobile = preg_match('/Mobile|Android|iPhone|iPad/', $userAgent);
 * 
 * // Логирование запросов
 * $logData = [
 *     'timestamp' => date('Y-m-d H:i:s'),
 *     'ip' => $server->get('REMOTE_ADDR'),
 *     'method' => $server->get('REQUEST_METHOD'),
 *     'uri' => $server->get('REQUEST_URI'),
 *     'user_agent' => $server->get('HTTP_USER_AGENT'),
 *     'referer' => $server->get('HTTP_REFERER')
 * ];
 * 
 * // Создание конфигурации для приложения
 * $config = [
 *     'server' => [
 *         'name' => $server->get('SERVER_NAME'),
 *         'port' => $server->get('SERVER_PORT'),
 *         'protocol' => $server->get('SERVER_PROTOCOL'),
 *         'software' => $server->get('SERVER_SOFTWARE')
 *     ],
 *     'request' => [
 *         'method' => $server->get('REQUEST_METHOD'),
 *         'uri' => $server->get('REQUEST_URI'),
 *         'query_string' => $server->get('QUERY_STRING'),
 *         'is_secure' => $server->get('HTTPS') === 'on'
 *     ],
 *     'client' => [
 *         'ip' => $server->get('REMOTE_ADDR'),
 *         'port' => $server->get('REMOTE_PORT'),
 *         'user_agent' => $server->get('HTTP_USER_AGENT'),
 *         'language' => $server->get('HTTP_ACCEPT_LANGUAGE')
 *     ]
 * ];
 * 
 * // Проверка окружения разработки
 * $isDevelopment = in_array($server->get('HTTP_HOST'), ['localhost', '127.0.0.1', 'dev.example.com']);
 * 
 * // Определение типа контента
 * $contentType = $server->get('CONTENT_TYPE');
 * $contentLength = $server->get('CONTENT_LENGTH');
 * 
 * // Работа с сессиями
 * $sessionId = $server->get('HTTP_COOKIE');
 * if ($sessionId) {
 *     // Извлечение ID сессии из cookies
 *     preg_match('/PHPSESSID=([^;]+)/', $sessionId, $matches);
 *     $sessionId = $matches[1] ?? null;
 * }
 * 
 * // Проверка прокси
 * $isBehindProxy = !empty($server->get('HTTP_X_FORWARDED_FOR')) || 
 *                  !empty($server->get('HTTP_X_REAL_IP')) ||
 *                  !empty($server->get('HTTP_X_FORWARDED_PROTO'));
 * 
 * // Получение реального IP при использовании прокси
 * $realIP = $server->get('HTTP_X_FORWARDED_FOR') ?: 
 *           $server->get('HTTP_X_REAL_IP') ?: 
 *           $server->get('REMOTE_ADDR');
 * 
 * // Проверка WebSocket запроса
 * $isWebSocket = $server->get('HTTP_UPGRADE') === 'websocket' &&
 *                $server->get('HTTP_CONNECTION') === 'Upgrade';
 * 
 * // Определение типа браузера
 * $userAgent = $server->get('HTTP_USER_AGENT');
 * $browser = 'Unknown';
 * if (strpos($userAgent, 'Chrome') !== false) $browser = 'Chrome';
 * elseif (strpos($userAgent, 'Firefox') !== false) $browser = 'Firefox';
 * elseif (strpos($userAgent, 'Safari') !== false) $browser = 'Safari';
 * elseif (strpos($userAgent, 'Edge') !== false) $browser = 'Edge';
 * elseif (strpos($userAgent, 'MSIE') !== false) $browser = 'Internet Explorer';
 * 
 * // Проверка API запроса
 * $isApiRequest = strpos($server->get('REQUEST_URI'), '/api/') === 0 ||
 *                 $server->get('HTTP_ACCEPT') === 'application/json' ||
 *                 $server->get('CONTENT_TYPE') === 'application/json';
 * 
 * // Создание уникального идентификатора запроса
 * $requestId = uniqid('req_', true);
 * $server->REQUEST_ID = $requestId;
 * 
 * // Проверка CORS запроса
 * $origin = $server->get('HTTP_ORIGIN');
 * $isCorsRequest = !empty($origin);
 * 
 * // Определение типа устройства
 * $userAgent = $server->get('HTTP_USER_AGENT');
 * $deviceType = 'desktop';
 * if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
 *     $deviceType = 'mobile';
 * } elseif (preg_match('/Tablet|iPad/', $userAgent)) {
 *     $deviceType = 'tablet';
 * }
 * 
 * // Проверка бота
 * $isBot = preg_match('/bot|crawler|spider|crawling/', strtolower($userAgent));
 * 
 * // Получение информации о времени запроса
 * $requestTime = $server->get('REQUEST_TIME');
 * $requestTimeFloat = $server->get('REQUEST_TIME_FLOAT');
 * 
 * // Создание метрик для мониторинга
 * $metrics = [
 *     'request_id' => $requestId,
 *     'timestamp' => $requestTime,
 *     'method' => $server->get('REQUEST_METHOD'),
 *     'uri' => $server->get('REQUEST_URI'),
 *     'status_code' => 200, // Будет установлен позже
 *     'response_time' => 0, // Будет рассчитан позже
 *     'client_ip' => $realIP,
 *     'user_agent' => $userAgent,
 *     'is_mobile' => $isMobile,
 *     'is_bot' => $isBot,
 *     'device_type' => $deviceType,
 *     'browser' => $browser
 * ];
 * ```
 */
final class Server extends stdClass
{
    /**
     * @var array<string, mixed>
     */
    protected array $data = [];
    use GetDataTrait, GetInstanceTrait;
    
    /**
     * Конструктор Server
     * 
     * Инициализирует объект Server, загружая все данные из суперглобального массива $_SERVER.
     * Автоматически обрабатывает JSON данные, преобразуя их в объекты или массивы.
     * Все ключи приводятся к нижнему регистру для единообразия доступа.
     * 
     * Конструктор вызывается только внутри класса (паттерн Singleton).
     * 
     * @since 1.0.0
     * @internal Конструктор предназначен для внутреннего использования
     * 
     * @example
     * ```php
     * // Внутреннее использование (не рекомендуется)
     * $server = new Server();
     * 
     * // Правильное использование через Singleton
     * $server = Server::getInstance();
     * 
     * // Примеры данных $_SERVER, которые будут обработаны:
     * 
     * // HTTP заголовки
     * // HTTP_HOST=example.com
     * // HTTP_USER_AGENT=Mozilla/5.0 (Windows NT 10.0; Win64; x64)...
     * // HTTP_ACCEPT=text/html,application/xhtml+xml,application/xml;q=0.9
     * // HTTP_ACCEPT_LANGUAGE=en-US,en;q=0.9
     * // HTTP_ACCEPT_ENCODING=gzip, deflate, br
     * // HTTP_CONNECTION=keep-alive
     * // HTTP_UPGRADE_INSECURE_REQUESTS=1
     * // HTTP_CACHE_CONTROL=max-age=0
     * 
     * // Информация о запросе
     * // REQUEST_METHOD=GET
     * // REQUEST_URI=/api/users/123
     * // QUERY_STRING=page=1&limit=10
     * // REQUEST_TIME=1640995200
     * // REQUEST_TIME_FLOAT=1640995200.1234
     * 
     * // Информация о сервере
     * // SERVER_NAME=example.com
     * // SERVER_PORT=80
     * // SERVER_PROTOCOL=HTTP/1.1
     * // SERVER_SOFTWARE=Apache/2.4.41 (Ubuntu)
     * // SERVER_ADDR=192.168.1.100
     * 
     * // Информация о клиенте
     * // REMOTE_ADDR=192.168.1.50
     * // REMOTE_PORT=54321
     * // REMOTE_USER=john
     * // REMOTE_HOST=client.example.com
     * 
     * // Пути и файлы
     * // DOCUMENT_ROOT=/var/www/html
     * // SCRIPT_NAME=/index.php
     * // SCRIPT_FILENAME=/var/www/html/index.php
     * // PATH_INFO=/api/users/123
     * // PATH_TRANSLATED=/var/www/html/api/users/123
     * 
     * // Безопасность и прокси
     * // HTTPS=on
     * // HTTP_X_FORWARDED_FOR=203.0.113.1, 192.168.1.50
     * // HTTP_X_REAL_IP=203.0.113.1
     * // HTTP_X_FORWARDED_PROTO=https
     * // HTTP_X_FORWARDED_HOST=example.com
     * 
     * // JSON данные в заголовках
     * // HTTP_X_CUSTOM_DATA={"user_id":123,"preferences":{"theme":"dark"}}
     * 
     * // После инициализации:
     * // $server->get('http_host') => 'example.com'
     * // $server->get('request_method') => 'GET'
     * // $server->get('http_user_agent') => 'Mozilla/5.0...'
     * // $server->get('http_x_custom_data') => (object)['user_id' => 123, ...]
     * // $server->get('https') => 'on'
     * // $server->get('remote_addr') => '192.168.1.50'
     * ```
     */
    private function __construct()
    {
        foreach ($_SERVER as $key => $value) {
            if(is_string($value) && json_validate($value)){
                $value = json_decode($value);
            }
            
            $this->data[mb_strtolower($key)] = $value;
        }
    }
    
}