<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Common;

use CloudCastle\HttpRequest\Exceptions\StorageException;
use CloudCastle\HttpRequest\Interfaces\StorageInterface;
use Throwable;

/**
 * Класс для работы с HTTP cookie
 *
 * Предоставляет объектно-ориентированный интерфейс для управления cookie пользователя.
 * Позволяет безопасно устанавливать, получать, проверять и удалять cookie с поддержкой времени жизни.
 * Использует паттерн Singleton для единого доступа в рамках запроса.
 *
 * Особенности:
 * - Автоматический доступ к cookie через магические методы
 * - Установка и удаление cookie с учетом времени жизни и безопасности
 * - Проверка существования cookie
 * - Обработка ошибок через исключения
 * - Поддержка цепочки вызовов
 *
 * @package CloudCastle\HttpRequest\Common
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 *
 * @example
 * // Получение экземпляра
 * $cookie = Cookie::setExpire(86400)->getInstance();
 *
 * // Установка cookie
 * $cookie->set('user_id', 123);
 * $cookie->user_name = 'John'; // Через магический метод
 *
 * // Получение cookie
 * $userId = $cookie->get('user_id');
 * $userName = $cookie->user_name;
 *
 * // Проверка существования
 * if ($cookie->has('user_id')) {
 *     // Cookie существует
 * }
 *
 * // Удаление cookie
 * $cookie->remove('user_id');
 * unset($cookie->user_name); // Через магический метод
 *
 * @property-read mixed $user_id Идентификатор пользователя
 * @property-read mixed $user_name Имя пользователя
 * @property-read mixed $token Токен авторизации
 * @property-read mixed $session_id Идентификатор сессии
 */
final class Cookie extends AbstractStorage
{
    /**
     * Инициализация и получение cookie
     *
     * Возвращает массив всех cookie из глобального массива $_COOKIE.
     * Используется для инициализации объекта при первом обращении.
     *
     * @return array<string, mixed> Массив cookie
     *
     * @example
     * $cookie = Cookie::getInstance();
     * $userId = $cookie->user_id;
     */
    protected static function checkRun (): array
    {
        return $_COOKIE;
    }
    
    /**
     * Создать новый экземпляр класса Cookie
     *
     * Фабричный метод для создания экземпляра класса.
     * Используется в AbstractStorage для безопасного создания объекта.
     *
     * @return static Новый экземпляр класса Cookie
     *
     * @example
     * $cookie = Cookie::getInstance();
     */
    protected static function createInstance (): static
    {
        return new self();
    }
    
    /**
     * Установить cookie по ключу
     *
     * Сохраняет значение в cookie с учетом времени жизни и безопасности.
     * Значение сериализуется для поддержки сложных типов данных.
     * Возвращает текущий объект для поддержки цепочки вызовов.
     *
     * @param string $key Ключ cookie
     * @param mixed $value Значение для сохранения (будет сериализовано)
     *
     * @return StorageInterface Текущий объект для цепочки вызовов
     * @throws StorageException Если заголовки уже отправлены или произошла ошибка
     *
     * @example
     * $cookie->set('user_id', 123);
     * $cookie->set('token', 'abc123');
     */
    public function set (string $key, mixed $value): StorageInterface
    {
        if (headers_sent()) {
            throw new StorageException('Headers already sent. Unable to set cookie.');
        }
        
        try {
            $this->{$key} = $_COOKIE[$key] = serialize($value);
            $expireTime = self::$expire[static::class] ?? 3600;
            setcookie($key, $this->{$key}, time() + $expireTime, '/', $_SERVER['HTTP_HOST'] ?? 'cli', $this->checkHttps(), true);
        } catch (Throwable $e) {
            throw new StorageException($e->getMessage(), $e->getCode(), null);
        }
        
        return $this;
    }
    
    /**
     * Проверить, используется ли HTTPS для соединения
     *
     * @return bool true, если соединение защищено (HTTPS), иначе false
     *
     * @example
     * if ($cookie->checkHttps()) {
     *     // Установить secure cookie
     * }
     */
    private function checkHttps (): bool
    {
        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (int) $_SERVER['SERVER_PORT'] === 443) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Проверить существование cookie по ключу
     *
     * Проверяет, существует ли cookie с указанным ключом как в объекте, так и в $_COOKIE.
     *
     * @param string $key Ключ cookie
     *
     * @return bool true, если cookie существует, иначе false
     *
     * @example
     * if ($cookie->has('user_id')) {
     *     // Cookie существует
     * }
     */
    public function has (string $key): bool
    {
        return property_exists($this, $key) && array_key_exists($key, $_COOKIE);
    }
    
    /**
     * Удалить cookie по ключу
     *
     * Удаляет cookie с указанным ключом как из объекта, так и из $_COOKIE.
     * Устанавливает cookie с истекшим временем жизни для удаления в браузере.
     * Возвращает текущий объект для поддержки цепочки вызовов.
     *
     * @param string $key Ключ cookie
     *
     * @return StorageInterface Текущий объект для цепочки вызовов
     * @throws StorageException Если заголовки уже отправлены или произошла ошибка
     *
     * @example
     * $cookie->remove('user_id');
     * unset($cookie->user_name);
     */
    public function remove (string $key): StorageInterface
    {
        if (headers_sent()) {
            throw new StorageException('Headers already sent. Unable to set cookie.');
        }
        
        unset($this->{$key}, $_COOKIE[$key]);
        setcookie($key, '', time() - 3600);
        
        return $this;
    }
}