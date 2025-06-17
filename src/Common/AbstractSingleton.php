<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Common;

use CloudCastle\HttpRequest\Interfaces\GetterInterface;
use CloudCastle\HttpRequest\Interfaces\SingletonInterface;
use stdClass;

/**
 * Абстрактный базовый класс для реализации паттерна Singleton
 * 
 * Этот абстрактный класс предоставляет основу для реализации паттерна Singleton
 * с дополнительными возможностями для работы с динамическими свойствами. Он
 * расширяет stdClass для поддержки динамических свойств и реализует интерфейсы
 * GetterInterface и SingletonInterface для обеспечения единообразного API.
 * 
 * Класс обеспечивает единственный экземпляр для каждого наследующего класса,
 * ленивую инициализацию с проверкой условий запуска, и удобный доступ к
 * свойствам через магические методы.
 * 
 * Основные возможности:
 * - Реализация паттерна Singleton для каждого наследующего класса
 * - Ленивая инициализация экземпляров
 * - Динамические свойства через наследование от stdClass
 * - Магический доступ к свойствам (регистронезависимый)
 * - Кэширование свойств для оптимизации производительности
 * - Абстрактные методы для настройки инициализации
 * 
 * @package CloudCastle\HttpRequest\Common
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 * @since 1.0.0
 * 
 * @example
 * ```php
 * class MySingleton extends AbstractSingleton
 * {
 *     protected static function checkRun(): array
 *     {
 *         // Проверка условий для инициализации
 *         return ['initialized' => true];
 *     }
 *     
 *     protected static function createInstance(): static
 *     {
 *         return new static();
 *     }
 * }
 * 
 * $instance1 = MySingleton::getInstance();
 * $instance2 = MySingleton::getInstance();
 * // $instance1 === $instance2 (true)
 * ```
 * 
 * @see GetterInterface
 * @see SingletonInterface
 * @see stdClass
 */
abstract class AbstractSingleton extends stdClass implements GetterInterface, SingletonInterface
{
    /**
     * Реестр экземпляров singleton для каждого класса
     * 
     * Этот статический массив хранит единственные экземпляры для каждого
     * наследующего класса. Ключом является полное имя класса, а значением -
     * экземпляр этого класса. Это обеспечивает, что каждый наследующий класс
     * имеет свой собственный singleton экземпляр.
     *
     * @var array<string, static> Карта имен классов к их singleton экземплярам
     * 
     * @example
     * ```php
     * [
     *     'CloudCastle\HttpRequest\Common\Session' => SessionInstance,
     *     'CloudCastle\HttpRequest\Common\Cookie' => CookieInstance,
     *     'MyApp\CustomStorage' => CustomStorageInstance
     * ]
     * ```
     * 
     * @note Каждый наследующий класс получает свой собственный экземпляр,
     *       что позволяет иметь разные singleton'ы для разных типов.
     * 
     * @see AbstractSingleton::getInstance()
     */
    protected static array $instance = [];
    
    /**
     * Получить единственный экземпляр класса
     * 
     * Этот метод реализует паттерн Singleton, обеспечивая создание только
     * одного экземпляра для каждого наследующего класса. При первом вызове
     * выполняется проверка условий запуска через checkRun(), создается
     * экземпляр через createInstance(), и инициализируются свойства.
     * 
     * Последующие вызовы возвращают уже созданный экземпляр без повторной
     * инициализации.
     *
     * @return static Единственный экземпляр текущего класса
     * 
     * @example
     * ```php
     * // Первый вызов - создает экземпляр
     * $session1 = Session::getInstance();
     * 
     * // Второй вызов - возвращает тот же экземпляр
     * $session2 = Session::getInstance();
     * 
     * // Проверка идентичности
     * var_dump($session1 === $session2); // bool(true)
     * 
     * // Разные классы имеют разные экземпляры
     * $cookie = Cookie::getInstance();
     * var_dump($session1 === $cookie); // bool(false)
     * ```
     * 
     * @note Метод помечен как final для предотвращения переопределения
     *       логики singleton в наследующих классах.
     * 
     * @see AbstractSingleton::checkRun()
     * @see AbstractSingleton::createInstance()
     */
    final public static function getInstance(): static
    {
        if (!isset(self::$instance[static::class])) {
            $data = static::checkRun();
            self::$instance[static::class] = static::createInstance();
            
            foreach ($data as $key => $value) {
                self::$instance[static::class]->{$key} = $value;
            }
        }
        
        return self::$instance[static::class];
    }
    
    /**
     * Проверка условий для инициализации экземпляра
     * 
     * Этот абстрактный метод должен быть реализован в наследующих классах
     * для проверки условий, которые должны быть выполнены перед созданием
     * экземпляра. Возвращаемые данные используются для инициализации
     * свойств созданного экземпляра.
     * 
     * Метод вызывается только при первом создании экземпляра класса,
     * что позволяет выполнять дорогостоящие проверки только один раз.
     *
     * @return array<string, mixed> Массив данных для инициализации экземпляра
     * 
     * @example
     * ```php
     * protected static function checkRun(): array
     * {
     *     // Проверка, что сессия запущена
     *     if (session_status() === PHP_SESSION_NONE) {
     *         session_start();
     *     }
     *     
     *     // Возвращаем данные для инициализации
     *     return [
     *         'session_id' => session_id(),
     *         'started_at' => time(),
     *         'is_active' => true
     *     ];
     * }
     * ```
     * 
     * @note Этот метод должен быть реализован в каждом наследующем классе.
     *       Возвращаемые данные будут присвоены как свойства экземпляра.
     * 
     * @see AbstractSingleton::getInstance()
     */
    abstract protected static function checkRun(): array;
    
    /**
     * Создать новый экземпляр класса
     * 
     * Этот абстрактный метод должен быть реализован в наследующих классах
     * для создания нового экземпляра. Метод вызывается только один раз
     * при первом обращении к getInstance().
     *
     * @return static Новый экземпляр текущего класса
     * 
     * @example
     * ```php
     * protected static function createInstance(): static
     * {
     *     // Создаем экземпляр с дополнительной логикой
     *     $instance = new static();
     *     
     *     // Можно выполнить дополнительную инициализацию
     *     $instance->initialize();
     *     
     *     return $instance;
     * }
     * ```
     * 
     * @note Этот метод должен быть реализован в каждом наследующем классе.
     *       Используйте new static() для создания экземпляра текущего класса.
     * 
     * @see AbstractSingleton::getInstance()
     */
    abstract protected static function createInstance(): static;
    
    /**
     * Кэш для ленивой загрузки свойств
     * 
     * Этот приватный массив используется для кэширования результатов
     * поиска свойств. Ключи хранятся в нижнем регистре для обеспечения
     * регистронезависимого поиска. Это оптимизирует производительность
     * при повторных обращениях к одним и тем же свойствам.
     * 
     * @var array<string, mixed> Кэш найденных свойств
     * 
     * @example
     * ```php
     * [
     *     'user_id' => 123,
     *     'session_data' => ['key' => 'value'],
     *     'preferences' => ['theme' => 'dark']
     * ]
     * ```
     * 
     * @note Ключи автоматически преобразуются в нижний регистр для
     *       обеспечения регистронезависимого поиска.
     * 
     * @see AbstractSingleton::has()
     * @see AbstractSingleton::get()
     */
    private array $lazy = [];
    
    /**
     * Магический геттер для доступа к свойствам
     * 
     * Этот метод обеспечивает удобный доступ к свойствам объекта через
     * синтаксис свойств. Он делегирует вызов методу get() для получения
     * значения с поддержкой регистронезависимого поиска.
     *
     * @param string $key Имя свойства для получения
     *
     * @return mixed Значение свойства или null, если свойство не найдено
     * 
     * @example
     * ```php
     * $session = Session::getInstance();
     * $session->user_id = 123;
     * 
     * // Магический доступ к свойствам
     * $userId = $session->user_id; // Возвращает 123
     * $userId = $session->USER_ID; // То же самое (регистронезависимо)
     * 
     * // Эквивалентно вызову get()
     * $userId = $session->get('user_id'); // Тот же результат
     * ```
     * 
     * @see AbstractSingleton::get()
     */
    public function __get(string $key): mixed
    {
        return $this->get($key);
    }
    
    /**
     * Проверить существование свойства по ключу
     * 
     * Этот метод проверяет, существует ли свойство с указанным ключом.
     * Поиск выполняется регистронезависимо, и найденные свойства кэшируются
     * для оптимизации последующих обращений.
     * 
     * Метод перебирает все свойства объекта и сравнивает их с искомым
     * ключом в нижнем регистре. При первом нахождении свойство кэшируется.
     *
     * @param string $key Ключ для поиска (поиск регистронезависимый)
     *
     * @return bool true, если свойство найдено, false в противном случае
     * 
     * @example
     * ```php
     * $session = Session::getInstance();
     * $session->user_id = 123;
     * $session->preferences = ['theme' => 'dark'];
     * 
     * // Проверка существования свойств
     * var_dump($session->has('user_id')); // bool(true)
     * var_dump($session->has('USER_ID')); // bool(true) - регистронезависимо
     * var_dump($session->has('missing_key')); // bool(false)
     * var_dump($session->has('preferences')); // bool(true)
     * ```
     * 
     * @note Поиск выполняется регистронезависимо. Найденные свойства
     *       кэшируются для улучшения производительности.
     * 
     * @see AbstractSingleton::get()
     * @see AbstractSingleton::$lazy
     */
    public function has(string $key): bool
    {
        if (!isset($this->lazy[mb_strtolower($key)])) {
            foreach ((array)$this as $propKey => $value) {
                if (mb_strtolower($propKey) === mb_strtolower($key)) {
                    $this->lazy[mb_strtolower($key)] = $value;
                    
                    return true;
                }
            }
            
            return false;
        }
        
        return true;
    }
    
    /**
     * Получить значение свойства с опциональным значением по умолчанию
     * 
     * Этот метод извлекает значение свойства по указанному ключу. Если
     * свойство найдено, его значение возвращается. Если свойство не
     * существует, возвращается предоставленное значение по умолчанию.
     * 
     * Поиск выполняется регистронезависимо, и результаты кэшируются для
     * оптимизации производительности при повторных обращениях.
     *
     * @param string $key Ключ для поиска (поиск регистронезависимый)
     * @param mixed $default Значение по умолчанию, если свойство не найдено
     *
     * @return mixed Значение свойства или значение по умолчанию
     * 
     * @example
     * ```php
     * $session = Session::getInstance();
     * $session->user_id = 123;
     * $session->preferences = ['theme' => 'dark'];
     * 
     * // Получение существующих свойств
     * $userId = $session->get('user_id', 0); // Возвращает 123
     * $prefs = $session->get('preferences', []); // Возвращает ['theme' => 'dark']
     * 
     * // Получение несуществующих свойств
     * $missing = $session->get('missing_key', 'default'); // Возвращает 'default'
     * $null = $session->get('another_missing'); // Возвращает null
     * 
     * // Регистронезависимый поиск
     * $userId = $session->get('USER_ID', 0); // Возвращает 123
     * ```
     * 
     * @note Поиск выполняется регистронезависимо. Результаты кэшируются
     *       для улучшения производительности при повторных обращениях.
     * 
     * @see AbstractSingleton::has()
     * @see AbstractSingleton::__get()
     * @see AbstractSingleton::$lazy
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->has($key)) {
            return $this->lazy[mb_strtolower($key)];
        }
        
        return $default;
    }
    
    /**
     * Получить все свойства объекта в виде массива
     * 
     * Этот метод возвращает все динамические свойства объекта в виде
     * ассоциативного массива. Метод использует приведение к массиву
     * для получения всех свойств, унаследованных от stdClass.
     *
     * @return array<string, mixed> Ассоциативный массив всех свойств объекта
     * 
     * @example
     * ```php
     * $session = Session::getInstance();
     * $session->user_id = 123;
     * $session->username = 'john_doe';
     * $session->preferences = ['theme' => 'dark', 'language' => 'ru'];
     * 
     * // Получение всех свойств
     * $allProperties = $session->all();
     * 
     * // Результат:
     * // [
     * //     'user_id' => 123,
     * //     'username' => 'john_doe',
     * //     'preferences' => ['theme' => 'dark', 'language' => 'ru']
     * // ]
     * 
     * // Использование в циклах
     * foreach ($session->all() as $key => $value) {
     *     echo "{$key}: " . (is_array($value) ? json_encode($value) : $value) . "\n";
     * }
     * ```
     * 
     * @note Метод возвращает только динамические свойства, добавленные
     *       к объекту. Приватные и защищенные свойства класса не включаются.
     * 
     * @see stdClass
     * @see AbstractSingleton::get()
     * @see AbstractSingleton::has()
     */
    public function all(): array
    {
        return (array)$this;
    }
}