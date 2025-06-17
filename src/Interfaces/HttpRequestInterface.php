<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Interfaces;

/**
 * Интерфейс для работы с HTTP запросами
 * 
 * Определяет контракт для классов, которые предоставляют доступ к данным HTTP запроса.
 * Расширяет SingletonInterface для обеспечения единственного экземпляра объекта
 * и GetterInterface для удобного доступа к данным запроса.
 * 
 * @package CloudCastle\HttpRequest\Interfaces
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 * 
 * @example
 * // Реализация в классе
 * class Request implements HttpRequestInterface
 * {
 *     private static ?self $instance = null;
 *     private array $data = [];
 *     
 *     public static function getInstance(): static
 *     {
 *         if (!self::$instance) {
 *             self::$instance = new self();
 *         }
 *         return self::$instance;
 *     }
 *     
 *     public function __get(string $name): mixed
 *     {
 *         return $this->data[$name] ?? null;
 *     }
 * }
 * 
 * // Использование
 * $request = Request::getInstance();
 * $postData = $request->post;
 * $getData = $request->get;
 * $session = $request->session;
 */
interface HttpRequestInterface extends SingletonInterface
{
    /**
     * Магический метод для доступа к данным HTTP запроса
     * 
     * Позволяет обращаться к различным компонентам HTTP запроса как к свойствам объекта.
     * Должен предоставлять доступ к таким данным как:
     * - POST данные
     * - GET параметры
     * - Сессии
     * - Куки
     * - Заголовки
     * - Серверные переменные
     * - Переменные окружения
     * - Загруженные файлы
     * 
     * @param string $name Имя свойства для получения данных
     * 
     * @return mixed Данные запроса по указанному имени или null, если данные не найдены
     * 
     * @example
     * $request = Request::getInstance();
     * 
     * // Доступ к различным данным запроса
     * $postData = $request->post;        // POST данные
     * $getParams = $request->get;        // GET параметры
     * $session = $request->session;      // Объект сессии
     * $cookies = $request->cookie;       // Объект куки
     * $headers = $request->headers;      // HTTP заголовки
     * $server = $request->server;        // Серверные переменные
     * $env = $request->env;              // Переменные окружения
     * $files = $request->files;          // Загруженные файлы
     * 
     * // Доступ к конкретным значениям
     * $userId = $request->post->user_id;
     * $page = $request->get->page;
     * $userName = $request->session->user_name;
     */
    public function __get(string $name): mixed;
}