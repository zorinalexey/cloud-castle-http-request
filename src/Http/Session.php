<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use CloudCastle\HttpRequest\Traits\SetExpireTrait;

/**
 * Class Session
 * 
 * Управляет HTTP сессиями пользователя. Предоставляет удобный интерфейс для работы
 * с глобальным массивом $_SESSION через методы и магические свойства. Реализует
 * паттерн Singleton для обеспечения единственного экземпляра класса.
 * 
 * Класс автоматически управляет временем жизни сессии, сериализует/десериализует
 * данные, отслеживает активность пользователя и очищает устаревшие сессии.
 * 
 * @package CloudCastle\HttpRequest\Http
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * 
 * @example
 * ```php
 * use CloudCastle\HttpRequest\Http\Session;
 * 
 * // Получение экземпляра сессии
 * $session = Session::getInstance();
 * 
 * // Установка значений
 * $session->set('user_id', 123);
 * $session->set('user_data', ['name' => 'John', 'email' => 'john@example.com']);
 * 
 * // Получение значений
 * $userId = $session->get('user_id', 0);
 * $userData = $session->get('user_data', []);
 * 
 * // Использование магических методов
 * $session->user_id = 456;
 * $session['theme'] = 'dark';
 * $currentTheme = $session->theme;
 * $currentUserId = $session['user_id'];
 * 
 * // Удаление значений
 * $session->delete('user_id');
 * 
 * // Очистка всех данных
 * $session->clear();
 * 
 * // Настройка времени жизни cookie
 * Session::setCookieExpire(3600); // 1 час
 * ```
 */
final class Session
{
    use SetExpireTrait;
    
    /**
     * @var array<string, mixed> Массив данных сессии
     * 
     * Хранит все данные сессии в виде ассоциативного массива.
     * Значения автоматически сериализуются при сохранении и десериализуются при получении.
     * 
     * @example
     * ```php
     * // Внутренняя структура массива
     * $this->data = [
     *     'user_id' => 'i:123;', // сериализованное значение
     *     'user_data' => 'a:2:{s:4:"name";s:4:"John";s:5:"email";s:15:"john@example.com";}',
     *     'last_active' => 1640995200
     * ];
     * ```
     */
    private array $data = [];
    
    /**
     * @var int|null Время последней активности пользователя
     * 
     * Отслеживает время последнего обращения к сессии для управления
     * временем жизни сессии и автоматической очистки устаревших данных.
     * 
     * @example
     * ```php
     * // Пример значения
     * $this->last_active = 1640995200; // Unix timestamp
     * 
     * // Проверка активности
     * if ($this->last_active && (time() - $this->last_active) > self::$expire) {
     *     // Сессия устарела, нужно очистить
     * }
     * ```
     */
    private ?int $last_active = null;
    
    /**
     * Создает экземпляр класса для тестирования
     * 
     * Позволяет создать новый экземпляр класса в обход singleton паттерна
     * для целей тестирования. Не предназначен для использования в продакшене.
     * 
     * @return static Новый экземпляр класса Session
     * @since 1.0.0
     * @internal Метод предназначен только для тестирования
     * 
     * @example
     * ```php
     * // В тестах
     * $session1 = Session::createForTesting();
     * $session2 = Session::createForTesting();
     * 
     * // Это будут разные экземпляры
     * $session1->set('test', 'value1');
     * $session2->set('test', 'value2');
     * 
     * assert($session1->get('test') === 'value1');
     * assert($session2->get('test') === 'value2');
     * ```
     */
    public static function createForTesting(): static
    {
        return new static();
    }
    
    /**
     * Защищенный конструктор класса Session
     * 
     * Инициализирует сессию, загружает существующие данные, управляет временем жизни
     * и отслеживает активность пользователя. Автоматически очищает устаревшие сессии.
     * 
     * Процесс инициализации:
     * 1. Проверяет статус сессии
     * 2. Устанавливает максимальное время жизни сессии
     * 3. Запускает сессию если необходимо
     * 4. Загружает и десериализует данные из $_SESSION
     * 5. Проверяет время последней активности
     * 6. Очищает устаревшие сессии или обновляет время активности
     * 
     * @throws \Exception Если произошла ошибка при работе с сессией
     * @since 1.0.0
     * 
     * @example
     * ```php
     * // Конструктор вызывается автоматически при получении экземпляра
     * $session = Session::getInstance();
     * 
     * // В этот момент происходит:
     * // 1. Проверка статуса сессии
     * // 2. Установка session.gc_maxlifetime
     * // 3. Запуск сессии если нужно
     * // 4. Загрузка данных из $_SESSION
     * // 5. Проверка времени активности
     * // 6. Очистка устаревших данных или обновление времени
     * ```
     */
    protected function __construct()
    {
        $sessionStatus = session_status();
        
        if($sessionStatus !== PHP_SESSION_DISABLED && $sessionStatus !== PHP_SESSION_ACTIVE) {
            ini_set('session.gc_maxlifetime', self::$expire);
            session_start();
            
            foreach ($_SESSION as $key => $value){
                if(is_string($value)){
                    $this->data[$key] = unserialize($value);
                } else {
                    $this->data[$key] = $value;
                }
            }
            
            $this->last_active = isset($this->data['last_active']) && is_int($this->data['last_active']) ? $this->data['last_active'] : null;
            
            if($this->last_active !== null && (time() - $this->last_active) > self::$expire) {
                session_unset();
                session_destroy();
            }else{
                $this->last_active = time();
                $this->data['last_active'] = $this->last_active;
            }
        }
    }
    
    /**
     * Получает значение из сессии по ключу
     * 
     * Возвращает значение из сессии с указанным именем. Если значение не существует,
     * возвращает значение по умолчанию. Автоматически десериализует сохраненные данные.
     * 
     * @param string $key Имя ключа для получения значения
     * @param mixed $default Значение по умолчанию, если ключ не найден
     * @return mixed Значение из сессии или значение по умолчанию
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $session = Session::getInstance();
     * 
     * // Получение простых значений
     * $userId = $session->get('user_id', 0);
     * $theme = $session->get('theme', 'light');
     * 
     * // Получение массивов
     * $userData = $session->get('user_data', []);
     * $preferences = $session->get('preferences', ['language' => 'en']);
     * 
     * // Получение объектов
     * $user = $session->get('user', null);
     * 
     * // Получение несуществующих значений
     * $nonExistent = $session->get('non_existent', 'default_value');
     * echo $nonExistent; // 'default_value'
     * 
     * // Получение без значения по умолчанию
     * $value = $session->get('some_key'); // null если не существует
     * ```
     */
    public function get(string $key, $default = null): mixed
    {
        if(array_key_exists($key, $this->data)){
            return unserialize($this->data[$key]);
        }
        
        return $default;
    }
    
    /**
     * Устанавливает значение в сессию по ключу
     * 
     * Сохраняет значение в сессию с указанным именем. Автоматически сериализует
     * данные перед сохранением. Обновляет глобальный массив $_SESSION.
     * 
     * @param string $key Имя ключа для сохранения значения
     * @param mixed $value Значение для сохранения (будет сериализовано)
     * @return self Возвращает текущий экземпляр для цепочки методов
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $session = Session::getInstance();
     * 
     * // Установка простых значений
     * $session->set('user_id', 123);
     * $session->set('username', 'john_doe');
     * $session->set('is_logged_in', true);
     * 
     * // Установка массивов
     * $session->set('user_data', [
     *     'name' => 'John Doe',
     *     'email' => 'john@example.com',
     *     'role' => 'admin'
     * ]);
     * 
     * // Установка объектов
     * $user = new User('John', 'john@example.com');
     * $session->set('user_object', $user);
     * 
     * // Цепочка методов
     * $session->set('key1', 'value1')
     *         ->set('key2', 'value2')
     *         ->set('key3', 'value3');
     * 
     * // Перезапись существующих значений
     * $session->set('user_id', 456); // Перезаписывает предыдущее значение
     * ```
     */
    public function set(string $key, mixed $value): self
    {
        $this->data[$key] = serialize($value);
        $_SESSION = $this->data;
        
        return $this;
    }
    
    /**
     * Магический метод для установки значения в сессию через свойство
     * 
     * Позволяет устанавливать значения в сессию через обращение к объекту как к свойству
     * или массиву. Внутренне вызывает метод set().
     * 
     * @param string $key Имя ключа для сохранения значения
     * @param mixed $value Значение для сохранения
     * @return void
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $session = Session::getInstance();
     * 
     * // Установка через свойство
     * $session->user_id = 123;
     * $session->username = 'john_doe';
     * $session->theme = 'dark';
     * 
     * // Установка через массив
     * $session['user_data'] = ['name' => 'John', 'email' => 'john@example.com'];
     * $session['preferences'] = ['language' => 'en', 'timezone' => 'UTC'];
     * 
     * // Установка сложных данных
     * $session->user_object = new User('John', 'john@example.com');
     * $session['settings'] = (object)['theme' => 'dark', 'notifications' => true];
     * 
     * // Перезапись значений
     * $session->user_id = 456; // Перезаписывает предыдущее значение
     * ```
     */
    public function __set(string $key, mixed $value): void
    {
        $this->set($key, $value);
    }
    
    /**
     * Магический метод для получения значения из сессии через свойство
     * 
     * Позволяет получать значения из сессии через обращение к объекту как к свойству
     * или массиву. Внутренне вызывает метод get().
     * 
     * @param string $key Имя ключа для получения значения
     * @return mixed Значение из сессии или null, если не существует
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $session = Session::getInstance();
     * 
     * // Получение через свойство
     * $userId = $session->user_id;
     * $username = $session->username;
     * $theme = $session->theme;
     * 
     * // Получение через массив
     * $userData = $session['user_data'];
     * $preferences = $session['preferences'];
     * 
     * // Получение сложных данных
     * $userObject = $session->user_object;
     * $settings = $session['settings'];
     * 
     * // Проверка существования перед получением
     * if (isset($session->user_id)) {
     *     $userId = $session->user_id;
     * } else {
     *     $userId = 0; // значение по умолчанию
     * }
     * 
     * // Получение несуществующих значений
     * $nonExistent = $session->non_existent; // null
     * $anotherNonExistent = $session['another_non_existent']; // null
     * ```
     */
    public function __get(string $key): mixed
    {
        return $this->get($key);
    }
    
    /**
     * Удаляет значение из сессии по ключу
     * 
     * Удаляет значение с указанным именем из внутреннего массива данных
     * и обновляет глобальный массив $_SESSION.
     * 
     * @param string $key Имя ключа для удаления
     * @return self Возвращает текущий экземпляр для цепочки методов
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $session = Session::getInstance();
     * 
     * // Удаление отдельных значений
     * $session->delete('user_id');
     * $session->delete('username');
     * $session->delete('theme');
     * 
     * // Удаление массивов и объектов
     * $session->delete('user_data');
     * $session->delete('user_object');
     * 
     * // Цепочка методов
     * $session->delete('key1')
     *         ->delete('key2')
     *         ->delete('key3');
     * 
     * // Удаление несуществующих ключей (безопасно)
     * $session->delete('non_existent_key'); // Ничего не происходит
     * 
     * // Проверка после удаления
     * $session->set('test', 'value');
     * echo $session->get('test'); // 'value'
     * 
     * $session->delete('test');
     * echo $session->get('test'); // null
     * ```
     */
    public function delete(string $key): self
    {
        unset($this->data[$key]);
        $_SESSION = $this->data;
        
        return $this;
    }
    
    /**
     * Очищает все данные сессии
     * 
     * Удаляет все значения из внутреннего массива данных и очищает
     * глобальный массив $_SESSION. Сессия остается активной, но пустой.
     * 
     * @return self Возвращает текущий экземпляр для цепочки методов
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $session = Session::getInstance();
     * 
     * // Установка нескольких значений
     * $session->set('user_id', 123);
     * $session->set('username', 'john_doe');
     * $session->set('theme', 'dark');
     * $session->set('user_data', ['name' => 'John']);
     * 
     * // Проверка что значения существуют
     * echo $session->get('user_id'); // 123
     * echo $session->get('username'); // 'john_doe'
     * 
     * // Очистка всех данных
     * $session->clear();
     * 
     * // Проверка что все значения удалены
     * echo $session->get('user_id'); // null
     * echo $session->get('username'); // null
     * echo $session->get('theme'); // null
     * 
     * // Сессия остается активной, можно добавлять новые данные
     * $session->set('new_key', 'new_value');
     * echo $session->get('new_key'); // 'new_value'
     * 
     * // Использование в цепочке методов
     * $session->clear()->set('fresh_start', true);
     * ```
     */
    public function clear(): self
    {
        $this->data = [];
        $_SESSION = $this->data;
        
        return $this;
    }
    
    /**
     * Устанавливает время жизни cookie сессии
     * 
     * Настраивает время жизни cookie, используемого для идентификации сессии.
     * Влияет на то, как долго браузер будет хранить идентификатор сессии.
     * 
     * @param int $time Время жизни cookie в секундах
     * @return string Возвращает имя класса
     * @since 1.0.0
     * 
     * @example
     * ```php
     * // Установка времени жизни cookie
     * Session::setCookieExpire(3600); // 1 час
     * Session::setCookieExpire(86400); // 24 часа
     * Session::setCookieExpire(604800); // 1 неделя
     * Session::setCookieExpire(2592000); // 30 дней
     * 
     * // Использование в настройках приложения
     * class AppConfig {
     *     public static function setupSession() {
     *         // Сессия на 2 часа
     *         Session::setCookieExpire(7200);
     *         
     *         // Другие настройки сессии
     *         ini_set('session.gc_maxlifetime', 7200);
     *     }
     * }
     * 
     * // Использование в зависимости от окружения
     * if ($_SERVER['HTTP_HOST'] === 'localhost') {
     *     Session::setCookieExpire(3600); // 1 час для разработки
     * } else {
     *     Session::setCookieExpire(86400); // 24 часа для продакшена
     * }
     * 
     * // Использование в зависимости от роли пользователя
     * if ($user->isAdmin()) {
     *     Session::setCookieExpire(1800); // 30 минут для админов
     * } else {
     *     Session::setCookieExpire(7200); // 2 часа для обычных пользователей
     * }
     * ```
     */
    public static function setCookieExpire(int $time): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            ini_set('session.cookie_lifetime', $time);
        }
        
        return self::class;
    }
}