<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest;

use CloudCastle\HttpRequest\Exceptions\CloneException;
use CloudCastle\HttpRequest\Exceptions\InputException;
use CloudCastle\HttpRequest\Http\Cookie;
use CloudCastle\HttpRequest\Http\Files;
use CloudCastle\HttpRequest\Http\Get;
use CloudCastle\HttpRequest\Http\Headers;
use CloudCastle\HttpRequest\Http\Post;
use CloudCastle\HttpRequest\Http\Session;
use CloudCastle\HttpRequest\Interfaces\HttpRequestInterface;
use CloudCastle\HttpRequest\Server\Env;
use CloudCastle\HttpRequest\Server\Server;
use stdClass;

/**
 * Class Request
 *
 * Основной класс для обработки HTTP-запросов. Реализует паттерн Singleton и предоставляет доступ к данным запроса,
 * таким как GET, POST, COOKIE, SESSION, FILES, заголовки, серверные переменные и переменные окружения.
 * Поддерживает автоматический разбор JSON и XML тела запроса.
 *
 * Пример использования:
 * <code>
 * use CloudCastle\HttpRequest\Request;
 * $request = Request::getInstance();
 * $userId = $request->get('user_id');
 * $session = $request->session;
 * $all = $request->all();
 * </code>
 *
 * @package CloudCastle\HttpRequest
 * @author  CloudCastle
 * @version 1.0.0
 *
 * @property-read Session $session Экземпляр сессии
 * @property-read Cookie $cookie Экземпляр cookie
 * @property-read Server $server Экземпляр серверных переменных
 * @property-read Env $env Экземпляр переменных окружения
 * @property-read Headers $headers Экземпляр заголовков
 * @property-read Post $post Экземпляр POST-данных
 * @property-read Get $get Экземпляр GET-данных
 * @property-read Files|null $files Экземпляр файлов (только для POST, PUT, PATCH)
 */
final class Request extends stdClass implements HttpRequestInterface
{
    /**
     * Список поддерживаемых Content-Type для разбора тела запроса
     *
     * @var string[]
     *
     * Пример:
     * <code>
     * // Проверка поддерживается ли тип
     * if (in_array('application/json', Request::$contentTypes)) {
     *     // ...
     * }
     * </code>
     */
    private static array $contentTypes = [
        'application/json',
        'application/xml',
        'text/html',
        'application/x-www-form-urlencoded',
        'multipart/form-data',
    ];
    
    /**
     * Экземпляр класса Request (Singleton)
     *
     * @var self|null
     *
     * Пример:
     * <code>
     * $request = Request::getInstance();
     * </code>
     */
    private static self|null $instance = null;
    
    /**
     * Время жизни для сессии и cookie (в секундах)
     *
     * @var array{session: int, cookie: int}
     *
     * Пример:
     * <code>
     * Request::init(1800, 7200); // 30 минут для сессии, 2 часа для cookie
     * </code>
     */
    private static array $expire = [
        'session' => 3600,
        'cookie' => 43200,
    ];
    
    /**
     * Конструктор Request. Заполняет объект данными запроса.
     * Вызывается только внутри класса (Singleton).
     *
     * Пример внутреннего использования:
     * <code>
     * $request = new self();
     * </code>
     */
    private function __construct()
    {
        foreach ($this->getRequestData() as $key => $value) {
            $this->{$key} = $value;
        }
    }
    
    /**
     * Формирует массив данных запроса с учетом типа Content-Type и метода запроса.
     * Используется внутри конструктора для инициализации свойств объекта.
     *
     * Пример внутреннего использования:
     * <code>
     * $data = $this->getRequestData();
     * </code>
     *
     * @return array Массив данных запроса
     * @throws InputException Если Content-Type не поддерживается
     */
    private function getRequestData(): array
    {
        $headers = Headers::getInstance();
        $default = [
            ...$_GET,
            'session' => Session::setExpire(self::$expire['session'])::getInstance(),
            'cookie' => Cookie::setExpire(self::$expire['cookie'])::getInstance(),
            'server' => Server::getInstance(),
            'env' => Env::getInstance(),
            'headers' => $headers,
            'post' => Post::getInstance(),
            'get' => Get::getInstance(),
        ];
        
        if(in_array($headers->{'Content-Type'}, self::$contentTypes)) {
            $data = $this->getRequest($headers);
        }else{
            throw new InputException('Content type not supported');
        }
        
        return match ($_SERVER['REQUEST_METHOD'] ?? 'GET') {
            'POST', 'PUT', 'PATCH' => [...$default, ...$data, ...$_POST, 'files' => Files::getInstance()],
            'DELETE' => [...$default, ...$data],
            default => $default,
        };
    }
    
    /**
     * Разбирает тело запроса в зависимости от Content-Type (JSON, XML).
     * Используется внутри getRequestData().
     *
     * Пример внутреннего использования:
     * <code>
     * $parsed = $this->getRequest($headers);
     * </code>
     *
     * @param Headers $headers Заголовки запроса
     * @return array Массив разобранных данных
     */
    private function getRequest(Headers $headers): array
    {
        $data = [];
        $contentType = ($headers->{'Content-Type'} ?? ($_SERVER['CONTENT_TYPE'] ?? null))?? null;
        $input = file_get_contents('php://input');
        
        if ($input !== false && in_array($contentType, self::$contentTypes)) {
            if ($contentType === 'application/json' && function_exists('json_validate') && json_validate($input)) {
                $data = json_decode($input, true) ?? [];
            }
            
            if (($contentType === 'application/xml' || $contentType === 'text/xml')) {
                if (($xml = simplexml_load_string($input)) !== false && ($jsonString = json_encode($xml))) {
                    $data = json_decode($jsonString, true) ?? [];
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Получить экземпляр класса Request (Singleton)
     *
     * @return static Экземпляр Request
     *
     * Пример:
     * <code>
     * $request = Request::getInstance();
     * </code>
     */
    public static function getInstance(): static
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Инициализация Request с указанием времени жизни сессии и cookie
     *
     * @param int $secondsSession Время жизни сессии в секундах
     * @param int $secondCookie Время жизни cookie в секундах
     * @return static Экземпляр Request
     *
     * Пример:
     * <code>
     * $request = Request::init(600, 3600); // 10 минут для сессии, 1 час для cookie
     * </code>
     */
    public static function init(int $secondsSession = 3600, int $secondCookie = 3600): static
    {
        self::$expire = [
            'session' => $secondsSession,
            'cookie' => $secondCookie,
        ];
        
        return self::getInstance();
    }
    
    /**
     * Магический геттер для получения свойств запроса
     *
     * @param string $name Имя свойства
     * @return mixed Значение свойства или null, если не найдено
     *
     * Пример:
     * <code>
     * $session = $request->session;
     * $cookie = $request->cookie;
     * </code>
     */
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }
    
    /**
     * Получить значение свойства запроса по имени
     *
     * @param string $name Имя свойства
     * @param mixed $default Значение по умолчанию, если свойство не найдено
     * @return mixed Значение свойства или $default
     *
     * Пример:
     * <code>
     * $userId = $request->get('user_id', 0);
     * </code>
     */
    public function get(string $name, $default = null): mixed
    {
        return $this->{$name} ?? $default;
    }
    
    /**
     * Запрещает клонирование Singleton-объекта Request.
     *
     * Пример внутреннего использования:
     * <code>
     * // Вызовет исключение CloneException
     * $clone = clone $request;
     * </code>
     *
     * @throws CloneException
     */
    private function __clone(): void
    {
        throw new CloneException('Клонирование объекта '.$this::class.' запрещено');
    }
    
    /**
     * Получить все свойства запроса в виде ассоциативного массива
     *
     * @return array Ассоциативный массив всех свойств запроса
     *
     * Пример:
     * <code>
     * $all = $request->all();
     * print_r($all);
     * </code>
     */
    public function all(): array
    {
        return get_object_vars($this);
    }
}