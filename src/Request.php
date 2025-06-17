<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest;

use CloudCastle\HttpRequest\Common\Cookie;
use CloudCastle\HttpRequest\Common\Env;
use CloudCastle\HttpRequest\Common\Files;
use CloudCastle\HttpRequest\Common\Get;
use CloudCastle\HttpRequest\Common\Headers;
use CloudCastle\HttpRequest\Common\Post;
use CloudCastle\HttpRequest\Common\Server;
use CloudCastle\HttpRequest\Common\Session;
use CloudCastle\HttpRequest\Interfaces\HttpRequestInterface;
use Exception;
use stdClass;

/**
 * Класс для работы с HTTP запросами
 * 
 * Предоставляет единую точку доступа к данным HTTP запроса через паттерн Singleton.
 * Автоматически парсит JSON и XML данные из тела запроса, управляет сессиями и куками.
 * 
 * @package CloudCastle\HttpRequest
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 * 
 * @example
 * // Получение экземпляра
 * $request = Request::getInstance();
 * 
 * // Настройка времени жизни
 * $request = Request::set(7200, 86400)->getInstance();
 * 
 * // Доступ к данным
 * $postData = $request->post;
 * $getData = $request->get;
 * $session = $request->session;
 * $cookies = $request->cookie;
 * 
 * @property-read Session $session Объект для работы с сессиями
 * @property-read Cookie $cookie Объект для работы с куками
 * @property-read Server $server Объект с серверными переменными
 * @property-read Env $env Объект с переменными окружения
 * @property-read Headers $headers Объект с HTTP заголовками
 * @property-read Post $post Объект с POST данными
 * @property-read Get $get Объект с GET данными
 * @property-read array<string, mixed> $files Массив с загруженными файлами (только для POST/PUT/PATCH)
 */
final class Request extends stdClass implements HttpRequestInterface
{
    /**
     * Единственный экземпляр класса (Singleton)
     * 
     * @var Request|null
     */
    private static self|null $instance = null;
    
    /**
     * Настройки времени жизни для сессий и куки
     * 
     * @var array<string, int> Массив с временем жизни в секундах
     */
    private static array $expire = [
        'session' => 3600,  // 1 час для сессий
        'cookie' => 43200,  // 12 часов для куки
    ];
    
    /**
     * Приватный конструктор для реализации паттерна Singleton
     * 
     * Инициализирует объект, загружая все данные HTTP запроса
     * и создавая объекты для работы с сессиями, куками и другими данными.
     * 
     * @throws Exception При ошибке парсинга данных запроса
     */
    private function __construct()
    {
        foreach ($this->getRequestData() as $key => $value) {
            $this->{$key} = $value;
        }
    }
    
    /**
     * Собирает все данные HTTP запроса
     * 
     * Создает массив со всеми доступными данными запроса:
     * - GET параметры
     * - Объекты для работы с сессиями, куками, сервером, окружением, заголовками
     * - POST данные и файлы (для POST/PUT/PATCH запросов)
     * - Парсированные данные из тела запроса (JSON/XML)
     * 
     * @return array<string, mixed> Массив со всеми данными запроса
     * 
     * @throws Exception При ошибке создания объектов данных
     */
    private function getRequestData(): array
    {
        $data = $this->getRequest();
        $default = [
            ...$_GET,
            'session' => Session::setExpire(self::$expire['session'])::getInstance(),
            'cookie' => Cookie::setExpire(self::$expire['cookie'])::getInstance(),
            'server' => Server::getInstance(),
            'env' => Env::getInstance(),
            'headers' => Headers::getInstance(),
            'post' => Post::getInstance(),
            'get' => Get::getInstance(),
        ];
        
        return match ($_SERVER['REQUEST_METHOD'] ?? 'GET') {
            'POST', 'PUT', 'PATCH' => [...$default, ...$data, ...$_POST, 'files' => Files::getInstance()],
            'DELETE' => [...$default, ...$data],
            default => $default,
        };
    }
    
    /**
     * Парсит данные из тела HTTP запроса
     * 
     * Читает и парсит данные из тела запроса в зависимости от Content-Type:
     * - application/json: парсит JSON данные
     * - application/xml или text/xml: парсит XML данные
     * 
     * @return array<mixed> Массив с парсированными данными из тела запроса
     * 
     * @throws Exception При ошибке чтения или парсинга данных
     */
    private function getRequest(): array
    {
        $data = [];
        $headers = Headers::getInstance();
        
        $contentType = $headers->{'Content-Type'} ?? ($_SERVER['CONTENT_TYPE'] ?? null);
        $input = file_get_contents('php://input');
        
        if ($input !== false) {
            if ($contentType === 'application/json' && function_exists('json_validate') && json_validate($input)) {
                $data = json_decode($input, true) ?? [];
            }
            
            if (($contentType === 'application/xml' || $contentType === 'text/xml')) {
                $xml = simplexml_load_string($input);
                
                if ($xml !== false) {
                    $jsonString = json_encode($xml);
                    if ($jsonString !== false) {
                        $data = json_decode($jsonString, true) ?? [];
                    }
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Получает единственный экземпляр класса (Singleton)
     * 
     * Создает новый экземпляр класса, если он еще не существует,
     * или возвращает существующий экземпляр.
     * 
     * @return static Экземпляр класса Request
     * 
     * @example
     * $request = Request::getInstance();
     * $postData = $request->post;
     */
    public static function getInstance(): static
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Установить время жизни для сессии и куки
     * 
     * Позволяет настроить время жизни сессий и куки перед получением экземпляра.
     * Возвращает экземпляр класса для использования в цепочке вызовов.
     *
     * @param int $secondsSession Время жизни сессии в секундах (по умолчанию 3600)
     * @param int $secondCookie Время жизни куки в секундах (по умолчанию 3600)
     * 
     * @return static Возвращает экземпляр класса для цепочки вызовов
     * 
     * @example
     * // Настройка времени жизни и получение экземпляра
     * $request = Request::set(7200, 86400);
     * 
     * // Или в цепочке
     * $session = Request::set(3600, 43200)->session;
     */
    public static function set(int $secondsSession = 3600, int $secondCookie = 3600): static
    {
        self::$expire = [
            'session' => $secondsSession,
            'cookie' => $secondCookie,
        ];
        
        return self::getInstance();
    }
    
    /**
     * Магический метод для доступа к свойствам объекта
     * 
     * Позволяет обращаться к данным запроса как к свойствам объекта.
     * Возвращает null, если свойство не существует.
     * 
     * @param string $name Имя свойства для получения
     * 
     * @return mixed Значение свойства или null, если свойство не существует
     * 
     * @example
     * $request = Request::getInstance();
     * $postData = $request->post;        // Объект Post
     * $getData = $request->get;          // Объект Get
     * $session = $request->session;      // Объект Session
     * $cookies = $request->cookie;       // Объект Cookie
     * $headers = $request->headers;      // Объект Headers
     * $server = $request->server;        // Объект Server
     * $env = $request->env;              // Объект Env
     * $files = $request->files;          // Массив файлов (если есть)
     */
    public function __get(string $name): mixed
    {
        return $this->{$name} ?? null;
    }
    
    /**
     * Запрещает клонирование объекта (Singleton)
     * 
     * Выбрасывает исключение при попытке клонирования объекта,
     * что обеспечивает единственность экземпляра класса.
     * 
     * @return void
     * 
     * @throws Exception При попытке клонирования
     */
    private function __clone(): void
    {
        throw new Exception('Клонирование объекта Request запрещено');
    }
}