<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Common;

use CloudCastle\HttpRequest\Exceptions\StorageException;
use CloudCastle\HttpRequest\Interfaces\StorageInterface;
use Throwable;

/**
 * Класс для работы с PHP сессиями
 * 
 * Предоставляет удобный интерфейс для работы с PHP сессиями через паттерн Singleton.
 * Автоматически инициализирует сессию при первом обращении, управляет временем жизни
 * и предоставляет методы для установки, получения и удаления данных сессии.
 * 
 * Особенности:
 * - Автоматическая инициализация сессии
 * - Настройка времени жизни сессии
 * - Сериализация/десериализация данных
 * - Поддержка цепочки вызовов
 * - Обработка ошибок с исключениями
 * 
 * @package CloudCastle\HttpRequest\Common
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 * 
 * @example
 * // Получение экземпляра с настройкой времени жизни
 * $session = Session::setExpire(7200)->getInstance();
 * 
 * // Установка данных сессии
 * $session->set('user_id', 123);
 * $session->set('user_name', 'John');
 * 
 * // Получение данных сессии
 * $userId = $session->get('user_id');
 * $userName = $session->user_name;  // Через магический метод
 * 
 * // Проверка существования
 * if ($session->has('user_id')) {
 *     // Данные существуют
 * }
 * 
 * // Удаление данных
 * $session->remove('temp_data');
 * 
 * // Очистка всей сессии
 * $session->clear();
 */
final class Session extends AbstractStorage
{
    /**
     * Инициализация и проверка состояния сессии
     * 
     * Проверяет текущее состояние сессии и инициализирует её при необходимости.
     * Настраивает параметры сессии (время жизни, cookie lifetime) перед запуском.
     * Возвращает массив с текущими данными сессии.
     *
     * @return array<string, mixed> Массив с данными сессии
     * 
     * @throws StorageException При ошибке инициализации сессии
     * 
     * @example
     * // Метод вызывается автоматически при getInstance()
     * $session = Session::getInstance();
     * // Сессия будет инициализирована автоматически
     */
    protected static function checkRun(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            $expireTime = self::$expire[static::class] ?? 3600;
            ini_set('session.gc_maxlifetime', (string) $expireTime);
            ini_set('session.cookie_lifetime', (string) $expireTime);
            session_start();
        }
        
        return $_SESSION;
    }
    
    /**
     * Создать новый экземпляр класса Session
     * 
     * Фабричный метод для создания экземпляра класса.
     * Используется в AbstractStorage для безопасного создания объекта.
     *
     * @return static Новый экземпляр класса Session
     * 
     * @example
     * // Метод вызывается автоматически в AbstractStorage::getInstance()
     * $session = Session::getInstance();
     */
    protected static function createInstance(): static
    {
        return new self();
    }
    
    /**
     * Установить значение в сессию
     * 
     * Сохраняет значение в сессии по указанному ключу. Значение сериализуется
     * перед сохранением для поддержки сложных типов данных. Возвращает
     * текущий объект для использования в цепочке вызовов.
     *
     * @param string $key Ключ для сохранения значения
     * @param mixed $value Значение для сохранения (будет сериализовано)
     *
     * @return StorageInterface Текущий объект для цепочки вызовов
     * 
     * @throws StorageException При ошибке сериализации или сохранения
     * 
     * @example
     * $session = Session::getInstance();
     * 
     * // Установка простых значений
     * $session->set('user_id', 123);
     * $session->set('user_name', 'John');
     * 
     * // Установка сложных объектов
     * $userData = ['id' => 123, 'name' => 'John', 'email' => 'john@example.com'];
     * $session->set('user_data', $userData);
     * 
     * // Использование в цепочке
     * $session->set('login_time', time())
     *         ->set('last_activity', time())
     *         ->set('preferences', $preferences);
     */
    public function set(string $key, mixed $value): StorageInterface
    {
        try {
            $this->{$key} = $value;
            $_SESSION[$key] = serialize($value);
        } catch (Throwable $e) {
            throw new StorageException($e->getMessage(), $e->getCode(), null);
        }
        return $this;
    }
    
    /**
     * Проверить существование значения в сессии
     * 
     * Проверяет, существует ли значение по указанному ключу как в объекте,
     * так и в глобальном массиве $_SESSION. Возвращает true только если
     * значение существует в обоих местах.
     *
     * @param string $key Ключ для проверки существования
     *
     * @return bool true, если значение существует, false в противном случае
     * 
     * @example
     * $session = Session::getInstance();
     * 
     * // Проверка существования
     * if ($session->has('user_id')) {
     *     $userId = $session->get('user_id');
     *     echo "User ID: $userId";
     * } else {
     *     echo "User not logged in";
     * }
     * 
     * // Проверка нескольких значений
     * if ($session->has('user_id') && $session->has('user_name')) {
     *     // Пользователь полностью авторизован
     * }
     */
    public function has(string $key): bool
    {
        return property_exists($this, $key) && array_key_exists($key, $_SESSION);
    }
    
    /**
     * Удалить значение из сессии
     * 
     * Удаляет значение по указанному ключу как из объекта, так и из
     * глобального массива $_SESSION. Если ключ не существует, операция
     * завершается успешно. Возвращает текущий объект для цепочки вызовов.
     *
     * @param string $key Ключ для удаления значения
     *
     * @return StorageInterface Текущий объект для цепочки вызовов
     * 
     * @example
     * $session = Session::getInstance();
     * 
     * // Удаление отдельных значений
     * $session->remove('temp_data');
     * $session->remove('cache_key');
     * 
     * // Использование в цепочке
     * $session->remove('old_session_data')
     *         ->remove('temporary_flags')
     *         ->set('new_data', 'value');
     * 
     * // Удаление после использования
     * $session->set('flash_message', 'Operation completed')
     *         ->remove('processing_flag');
     */
    public function remove(string $key): StorageInterface
    {
        if(property_exists($this, $key) && array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key], $this->{$key});
        }
        
        return $this;
    }

    private function deserializeValue(mixed $value): mixed
    {
        if (is_string($value)) {
            $unserialized = @unserialize($value);
            if ($unserialized !== false || $value === 'b:0;') {
                return $unserialized;
            }
        }
        return $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (!isset($this->{$key})) {
            return $default;
        }
        return $this->deserializeValue($this->{$key});
    }

    public function all(): array
    {
        $result = [];
        foreach ((array)$this as $key => $value) {
            if ($key === 'instance' || $key === 'expire' || $key === 'lazy') {
                continue;
            }
            $result[$key] = $this->deserializeValue($value);
        }
        return $result;
    }
}