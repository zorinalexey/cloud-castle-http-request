<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Interfaces;

/**
 * Interface HttpRequestInterface
 *
 * Интерфейс для классов, реализующих работу с HTTP-запросом в виде Singleton.
 * Определяет методы для получения экземпляра, инициализации, а также магического доступа к данным запроса.
 *
 * Пример использования:
 * <code>
 * class MyRequest implements HttpRequestInterface {
 *     public static function getInstance(): static { ... }
 *     public static function init(int $secondsSession = 3600, int $secondCookie = 3600): static { ... }
 *     public function __get(string $name): mixed { ... }
 * }
 * </code>
 *
 * @package CloudCastle\HttpRequest\Interfaces
 */
interface HttpRequestInterface
{
    /**
     * Получить экземпляр класса (Singleton).
     *
     * @return static Экземпляр класса
     */
    public static function getInstance(): static;
    
    /**
     * Инициализация класса с параметрами времени жизни сессии и cookie.
     *
     * @param int $secondsSession Время жизни сессии в секундах
     * @param int $secondCookie Время жизни cookie в секундах
     * @return static Экземпляр класса
     */
    public static function init(int $secondsSession = 3600, int $secondCookie = 3600): static;
    
    /**
     * Магический геттер для доступа к данным запроса.
     *
     * @param string $name Имя свойства
     * @return mixed Значение свойства
     */
    public function __get(string $name): mixed;
}