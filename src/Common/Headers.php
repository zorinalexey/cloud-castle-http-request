<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Common;

/**
 * Класс для работы с HTTP заголовками
 * 
 * Предоставляет удобный интерфейс для доступа к HTTP заголовкам запроса
 * через паттерн Singleton. Позволяет получать и обрабатывать заголовки,
 * отправленные клиентом в HTTP запросе, в объектно-ориентированном стиле.
 * 
 * Поддерживаемые заголовки:
 * - Content-Type: Тип содержимого запроса
 * - User-Agent: Информация о браузере/клиенте
 * - Accept: Принимаемые типы контента
 * - Authorization: Данные авторизации
 * - Cookie: Куки клиента
 * - Referer: Источник запроса
 * - Host: Хост запроса
 * - Content-Length: Размер содержимого
 * - Accept-Language: Предпочитаемые языки
 * - Accept-Encoding: Поддерживаемые кодировки
 * 
 * Особенности:
 * - Автоматическое получение заголовков
 * - Поддержка функции getallheaders()
 * - Fallback для окружений без getallheaders()
 * - Магические методы для доступа к заголовкам
 * - Проверка существования заголовков
 * - Безопасный доступ к данным
 * 
 * @package CloudCastle\HttpRequest\Common
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 * 
 * @example
 * // Получение экземпляра
 * $headers = Headers::getInstance();
 * 
 * // Доступ к заголовкам
 * $contentType = $headers->{'Content-Type'};     // Через магический метод
 * $userAgent = $headers->get('User-Agent');      // Через метод get
 * $accept = $headers->get('Accept', 'any');      // С значением по умолчанию
 * 
 * // Проверка существования
 * if ($headers->has('Authorization')) {
 *     $auth = $headers->Authorization;
 * }
 * 
 * // Определение типа контента
 * $contentType = $headers->{'Content-Type'};
 * if (str_contains($contentType, 'application/json')) {
 *     // JSON данные
 * } elseif (str_contains($contentType, 'application/xml')) {
 *     // XML данные
 * }
 * 
 * // Проверка авторизации
 * if ($headers->has('Authorization')) {
 *     $token = $headers->Authorization;
 *     // Обработка токена
 * }
 * 
 * @property-read string $ContentType Тип содержимого запроса
 * @property-read string $UserAgent Информация о браузере/клиенте
 * @property-read string $Accept Принимаемые типы контента
 * @property-read string $Authorization Данные авторизации
 * @property-read string $Cookie Куки клиента
 * @property-read string $Referer Источник запроса
 * @property-read string $Host Хост запроса
 * @property-read string $ContentLength Размер содержимого
 * @property-read string $AcceptLanguage Предпочитаемые языки
 * @property-read string $AcceptEncoding Поддерживаемые кодировки
 */
final class Headers extends AbstractSingleton
{
    /**
     * Инициализация и получение HTTP заголовков
     * 
     * Получает все доступные HTTP заголовки из запроса. Использует функцию
     * getallheaders() если она доступна, иначе возвращает пустой массив.
     * Заголовки содержат информацию о клиенте, типе контента, авторизации
     * и других параметрах HTTP запроса.
     *
     * @return array<string, mixed> Массив HTTP заголовков
     * 
     * @example
     * // Метод вызывается автоматически при getInstance()
     * $headers = Headers::getInstance();
     * // Все заголовки будут доступны как свойства объекта
     * 
     * // Доступ к заголовкам
     * $contentType = $headers->{'Content-Type'};
     * $userAgent = $headers->{'User-Agent'};
     * $accept = $headers->{'Accept'};
     * 
     * // Проверка типа контента
     * if ($headers->has('Content-Type')) {
     *     $type = $headers->{'Content-Type'};
     *     if (str_contains($type, 'application/json')) {
     *         // Обработка JSON
     *     }
     * }
     */
    protected static function checkRun(): array
    {
        $headers = [];
        
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        }
        
        return $headers;
    }
    
    /**
     * Создать новый экземпляр класса Headers
     * 
     * Фабричный метод для создания экземпляра класса.
     * Используется в AbstractSingleton для безопасного создания объекта.
     *
     * @return static Новый экземпляр класса Headers
     * 
     * @example
     * // Метод вызывается автоматически в AbstractSingleton::getInstance()
     * $headers = Headers::getInstance();
     */
    protected static function createInstance(): static
    {
        return new self();
    }
}