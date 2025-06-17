<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Common;

/**
 * Класс для работы с серверными переменными
 * 
 * Предоставляет удобный интерфейс для доступа к серверным переменным ($_SERVER)
 * через паттерн Singleton. Позволяет получать информацию о сервере, запросе,
 * клиенте и других серверных параметрах в объектно-ориентированном стиле.
 * 
 * Поддерживаемые данные:
 * - Информация о сервере (SERVER_NAME, SERVER_PORT, SERVER_SOFTWARE)
 * - Информация о запросе (REQUEST_METHOD, REQUEST_URI, QUERY_STRING)
 * - Информация о клиенте (REMOTE_ADDR, HTTP_USER_AGENT, HTTP_ACCEPT)
 * - Заголовки запроса (HTTP_*)
 * - Переменные окружения сервера
 * 
 * @package CloudCastle\HttpRequest\Common
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 * 
 * @example
 * // Получение экземпляра
 * $server = Server::getInstance();
 * 
 * // Доступ к серверным переменным
 * $method = $server->REQUEST_METHOD;        // GET, POST, PUT, DELETE
 * $uri = $server->REQUEST_URI;              // /path/to/resource
 * $userAgent = $server->HTTP_USER_AGENT;    // User-Agent заголовок
 * $ip = $server->REMOTE_ADDR;               // IP адрес клиента
 * 
 * // Проверка существования
 * if ($server->has('HTTPS')) {
 *     echo "Secure connection";
 * }
 * 
 * // Получение с значением по умолчанию
 * $port = $server->get('SERVER_PORT', '80');
 * $protocol = $server->get('SERVER_PROTOCOL', 'HTTP/1.1');
 * 
 * @property-read string $REQUEST_METHOD HTTP метод запроса
 * @property-read string $REQUEST_URI URI запроса
 * @property-read string $QUERY_STRING Строка запроса
 * @property-read string $REMOTE_ADDR IP адрес клиента
 * @property-read string $HTTP_USER_AGENT User-Agent заголовок
 * @property-read string $HTTP_ACCEPT Accept заголовок
 * @property-read string $SERVER_NAME Имя сервера
 * @property-read string $SERVER_PORT Порт сервера
 * @property-read string $SERVER_SOFTWARE Информация о серверном ПО
 * @property-read string $SERVER_PROTOCOL Протокол (HTTP/1.1)
 * @property-read string $HTTPS Индикатор HTTPS соединения
 */
final class Server extends AbstractSingleton
{
    /**
     * Инициализация и получение серверных переменных
     * 
     * Возвращает массив всех доступных серверных переменных из глобального
     * массива $_SERVER. Эти переменные содержат информацию о сервере,
     * запросе, клиенте и других параметрах окружения.
     *
     * @return array<string, mixed> Массив серверных переменных
     * 
     * @example
     * // Метод вызывается автоматически при getInstance()
     * $server = Server::getInstance();
     * // Все серверные переменные будут доступны как свойства объекта
     * 
     * // Доступ к переменным
     * $method = $server->REQUEST_METHOD;
     * $uri = $server->REQUEST_URI;
     * $ip = $server->REMOTE_ADDR;
     */
    protected static function checkRun(): array
    {
        return $_SERVER;
    }
    
    /**
     * Создать новый экземпляр класса Server
     * 
     * Фабричный метод для создания экземпляра класса.
     * Используется в AbstractSingleton для безопасного создания объекта.
     *
     * @return static Новый экземпляр класса Server
     * 
     * @example
     * // Метод вызывается автоматически в AbstractSingleton::getInstance()
     * $server = Server::getInstance();
     */
    protected static function createInstance(): static
    {
        return new self();
    }
}