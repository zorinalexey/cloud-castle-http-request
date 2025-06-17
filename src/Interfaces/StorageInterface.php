<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Interfaces;

use CloudCastle\HttpRequest\Exceptions\StorageException;

/**
 * Интерфейс для работы с хранилищами данных
 * 
 * Определяет контракт для классов, которые предоставляют функциональность
 * хранения и управления данными с поддержкой времени жизни. Расширяет
 * SingletonInterface для обеспечения единственного экземпляра и GetterInterface
 * для удобного доступа к данным.
 * 
 * Поддерживает операции:
 * - Установка и получение значений
 * - Удаление отдельных значений
 * - Очистка всего хранилища
 * - Настройка времени жизни данных
 * - Магические методы для доступа к данным
     *
 * @package CloudCastle\HttpRequest\Interfaces
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 * 
 * @example
 * // Реализация в классе
 * class MyStorage implements StorageInterface
 * {
 *     private static ?self $instance = null;
 *     private static int $expire = 3600;
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
 *     public static function setExpire(int $seconds): static
 *     {
 *         self::$expire = $seconds;
 *         return self::getInstance();
 *     }
 *     
 *     public function set(string $key, mixed $value): StorageInterface
 *     {
 *         $this->data[$key] = $value;
 *         return $this;
 *     }
 *     
 *     public function get(string $key, mixed $default = null): mixed
 *     {
 *         return $this->data[$key] ?? $default;
 *     }
     *
 *     public function has(string $key): bool
 *     {
 *         return array_key_exists($key, $this->data);
 *     }
     *
 *     public function remove(string $key): StorageInterface
 *     {
 *         unset($this->data[$key]);
 *         return $this;
 *     }
 *     
 *     public function clear(): StorageInterface
 *     {
 *         $this->data = [];
 *         return $this;
 *     }
 *     
 *     public function __get(string $key): mixed
 *     {
 *         return $this->get($key);
 *     }
 *     
 *     public function __set(string $key, mixed $value): void
 *     {
 *         $this->set($key, $value);
 *     }
 * }
 * 
 * // Использование
 * $storage = MyStorage::setExpire(7200)->getInstance();
 * $storage->set('user_id', 123);
 * $userId = $storage->get('user_id');
 * $storage->user_name = 'John';  // Через магический метод
 */
interface StorageInterface extends SingletonInterface, GetterInterface
{
    /**
     * Установить время жизни для данных в хранилище
     * 
     * Позволяет настроить время жизни данных перед получением экземпляра.
     * Возвращает экземпляр класса для использования в цепочке вызовов.
     * Время жизни применяется ко всем последующим операциям записи.
     *
     * @param int $seconds Время жизни данных в секундах
     * 
     * @return static Экземпляр класса для цепочки вызовов
     *
     * @example
     * // Настройка времени жизни и получение экземпляра
     * $storage = MyStorage::setExpire(7200)->getInstance();
     * 
     * // Использование в цепочке
     * $session = Session::setExpire(3600)->getInstance();
     * $cookies = Cookie::setExpire(86400)->getInstance();
     */
    public static function setExpire(int $seconds): static;
    
    /**
     * Установить значение в хранилище
     * 
     * Сохраняет значение по указанному ключу в хранилище.
     * Возвращает текущий объект для использования в цепочке вызовов.
     * Значение будет доступно до истечения времени жизни или явного удаления.
     *
     * @param string $key Ключ для сохранения значения
     * @param mixed $value Значение для сохранения
     *
     * @return StorageInterface Текущий объект для цепочки вызовов
     * 
     * @throws StorageException При ошибке сохранения значения
     * 
     * @example
     * $storage = MyStorage::getInstance();
     * 
     * // Простая установка значения
     * $storage->set('user_id', 123);
     * 
     * // Использование в цепочке
     * $storage->set('name', 'John')
     *         ->set('email', 'john@example.com')
     *         ->set('role', 'admin');
     */
    public function set(string $key, mixed $value): StorageInterface;
    
    /**
     * Удалить значение из хранилища
     * 
     * Удаляет значение по указанному ключу из хранилища.
     * Если ключ не существует, операция завершается успешно.
     * Возвращает текущий объект для использования в цепочке вызовов.
     *
     * @param string $key Ключ для удаления значения
     *
     * @return StorageInterface Текущий объект для цепочки вызовов
     * 
     * @throws StorageException При ошибке удаления значения
     * 
     * @example
     * $storage = MyStorage::getInstance();
     * 
     * // Удаление значения
     * $storage->remove('user_id');
     * 
     * // Использование в цепочке
     * $storage->remove('temp_data')
     *         ->remove('cache_key')
     *         ->set('new_data', 'value');
     */
    public function remove(string $key): StorageInterface;
    
    /**
     * Очистить все данные в хранилище
     * 
     * Удаляет все данные из хранилища, оставляя его пустым.
     * Возвращает текущий объект для использования в цепочке вызовов.
     * Операция необратима - все данные будут потеряны.
     *
     * @return StorageInterface Текущий объект для цепочки вызовов
     * 
     * @example
     * $storage = MyStorage::getInstance();
     * 
     * // Очистка всего хранилища
     * $storage->clear();
     * 
     * // Использование в цепочке
     * $storage->clear()
     *         ->set('new_session', true)
     *         ->set('user_data', $userData);
     */
    public function clear(): StorageInterface;
    
    /**
     * Магический метод для установки значений
     * 
     * Позволяет устанавливать значения в хранилище через синтаксис свойств объекта.
     * Делегирует вызов методу set() для сохранения значения.
     *
     * @param string $key Ключ для сохранения значения
     * @param mixed $value Значение для сохранения
     *
     * @return void
     * 
     * @example
     * $storage = MyStorage::getInstance();
     * 
     * // Установка значений через магический метод
     * $storage->user_id = 123;
     * $storage->user_name = 'John';
     * $storage->user_email = 'john@example.com';
     * 
     * // Эквивалентно вызовам set()
     * $storage->set('user_id', 123);
     * $storage->set('user_name', 'John');
     * $storage->set('user_email', 'john@example.com');
     */
    public function __set(string $key, mixed $value): void;
}