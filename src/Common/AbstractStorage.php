<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Common;

use CloudCastle\HttpRequest\Exceptions\StorageException;
use CloudCastle\HttpRequest\Interfaces\StorageInterface;

/**
 * Абстрактный базовый класс для реализации хранилищ
 * 
 * Этот абстрактный класс предоставляет основу для реализации различных механизмов
 * хранения данных, таких как сессии, куки и другие постоянные хранилища данных.
 * Расширяет AbstractSingleton для обеспечения единственного экземпляра на тип
 * хранилища и реализует StorageInterface для предоставления единообразного API
 * для операций с хранилищем.
 * 
 * Класс предоставляет автоматическую сериализацию/десериализацию сохраняемых значений,
 * магический доступ к свойствам через методы __get и __set, и настраиваемое время
 * жизни для хранимых данных.
     *
 * Основные возможности:
 * - Реализация паттерна Singleton
 * - Автоматическая сериализация сохраняемых значений
 * - Магический доступ к свойствам (синтаксис object->key)
 * - Настраиваемое время жизни данных
 * - Массовая очистка хранилища
 * - Типобезопасные операции с хранилищем
 * 
 * @package CloudCastle\HttpRequest\Common
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 * @since 1.0.0
 * 
 * @example
 * ```php
 * class MyStorage extends AbstractStorage
 * {
 *     // Реализация, специфичная для вашего типа хранилища
 * }
 * 
 * $storage = MyStorage::getInstance();
 * $storage->set('user_id', 123);
 * $storage->user_id = 456; // Магический доступ к свойствам
 * $value = $storage->get('user_id'); // Возвращает 456
 * $value = $storage->user_id; // То же самое
 * ```
 * 
 * @see StorageInterface
 * @see AbstractSingleton
 */
abstract class AbstractStorage extends AbstractSingleton implements StorageInterface
{
    /**
     * Конфигурация времени жизни хранилищ
     * 
     * Этот статический массив хранит время жизни (в секундах) для различных классов
     * хранилищ. Ключом является имя класса, а значением - количество секунд,
     * которое хранилище должно существовать до истечения срока действия.
     *
     * @var array<string, int> Карта имен классов к времени жизни в секундах
     *
     * @example
     * ```php
     * [
     *     'CloudCastle\HttpRequest\Common\Session' => 3600, // 1 час
     *     'CloudCastle\HttpRequest\Common\Cookie' => 86400, // 24 часа
     * ]
     * ```
     */
    protected static array $expire = [];
    
    /**
     * Установить время жизни для текущего класса хранилища
     * 
     * Этот метод позволяет настроить, как долго данные должны храниться в хранилище
     * до истечения срока действия. Время жизни устанавливается для типа класса
     * хранилища, а не для отдельных ключей. После установки все данные в этом
     * типе хранилища истекут через указанное количество секунд.
     * 
     * @param int $seconds Количество секунд до истечения срока действия хранилища (0 = без истечения)
     * 
     * @return static Возвращает экземпляр singleton для цепочки методов
     * 
     * @example
     * ```php
     * // Установить время жизни сессии на 30 минут
     * Session::setExpire(1800)->set('user_data', $data);
     * 
     * // Установить время жизни куки на 1 день
     * Cookie::setExpire(86400)->set('preferences', $prefs);
     * ```
     * 
     * @note Время жизни хранится статически для каждого класса, поэтому все экземпляры
     *       одного типа хранилища будут использовать одинаковую настройку времени жизни.
     *
     * @see AbstractSingleton::getInstance()
     */
    public static function setExpire (int $seconds): static
    {
        static::$expire[static::class] = $seconds;
        
        return static::getInstance();
    }
    
    /**
     * Очистить все данные из хранилища
     *
     * Этот метод удаляет все сохраненные пары ключ-значение из текущего экземпляра
     * хранилища. Он перебирает все свойства объекта и вызывает метод remove()
     * для каждого ключа, который существует в хранилище.
     *
     * @return StorageInterface Возвращает текущий экземпляр для цепочки методов
     *
     * @throws StorageException
     * @see StorageInterface::remove()
     * @example
     * ```php
     * $storage = Session::getInstance();
     * $storage->set('user_id', 123);
     * $storage->set('preferences', ['theme' => 'dark']);
     *
     * // Очистить все сохраненные данные
     * $storage->clear(); // Удаляет и 'user_id', и 'preferences'
     * ```
     *
     * @note Этот метод помечен как final для предотвращения переопределения,
     *       обеспечивая согласованное поведение во всех реализациях хранилищ.
     *
     */
    final public function clear (): StorageInterface
    {
        foreach((array)$this as $key => $value){
            $this->remove($key);
        }
        
        return $this;
    }
    
    /**
     * Магический геттер для доступа к значениям хранилища как к свойствам объекта
     * 
     * Этот метод позволяет получать доступ к сохраненным значениям, используя
     * синтаксис свойств объекта вместо метода get(). Он предоставляет более
     * удобный способ получения значений из хранилища.
     *
     * @param string $key Ключ для получения из хранилища
     *
     * @return mixed Сохраненное значение или null, если ключ не существует
     * 
     * @example
     * ```php
     * $storage = Session::getInstance();
     * $storage->set('user_id', 123);
     * 
     * // Использование магического доступа к свойствам
     * $userId = $storage->user_id; // Возвращает 123
     * 
     * // Эквивалентно использованию метода get()
     * $userId = $storage->get('user_id'); // Тот же результат
     * ```
     * 
     * @note Этот метод помечен как final для предотвращения переопределения,
     *       обеспечивая согласованное поведение во всех реализациях хранилищ.
     * 
     * @see AbstractStorage::get()
     */
    final public function __get (string $key): mixed
    {
        return $this->get($key);
    }
    
    /**
     * Магический сеттер для сохранения значений как свойств объекта
     * 
     * Этот метод позволяет сохранять значения, используя синтаксис свойств объекта
     * вместо метода set(). Он предоставляет более удобный способ сохранения
     * значений в хранилище.
     *
     * @param string $key Ключ для сохранения значения
     * @param mixed $value Значение для сохранения (будет автоматически сериализовано)
     *
     * @return void
     * 
     * @example
     * ```php
     * $storage = Session::getInstance();
     * 
     * // Использование магического доступа к свойствам
     * $storage->user_id = 123;
     * $storage->preferences = ['theme' => 'dark', 'language' => 'en'];
     * 
     * // Эквивалентно использованию метода set()
     * $storage->set('user_id', 123);
     * $storage->set('preferences', ['theme' => 'dark', 'language' => 'en']);
     * ```
     * 
     * @note Этот метод помечен как final для предотвращения переопределения,
     *       обеспечивая согласованное поведение во всех реализациях хранилищ.
     *       Значения автоматически сериализуются при сохранении.
     * 
     * @see AbstractStorage::set()
     */
    final public function __set (string $key, mixed $value): void
    {
        $this->set($key, $value);
    }
    
    /**
     * Получить значение из хранилища с опциональным значением по умолчанию
     * 
     * Этот метод извлекает значение из хранилища по его ключу. Если ключ существует,
     * сохраненное значение десериализуется и возвращается. Если ключ не существует,
     * возвращается предоставленное значение по умолчанию.
     * 
     * Метод автоматически десериализует сохраненные значения.
     * Для получения сырых сериализованных значений используйте getRaw().
     *
     * @param string $key Ключ для получения из хранилища
     * @param mixed $default Значение по умолчанию для возврата, если ключ не существует
     *
     * @return mixed Сохраненное значение (десериализованное) или значение по умолчанию
     * 
     * @example
     * ```php
     * $storage = Session::getInstance();
     * 
     * // Сохранить некоторые данные
     * $storage->set('user_id', 123);
     * $storage->set('preferences', ['theme' => 'dark']);
     * 
     * // Получить десериализованные значения
     * $userId = $storage->get('user_id', 0); // Возвращает 123
     * $prefs = $storage->get('preferences', []); // Возвращает ['theme' => 'dark']
     * $nonExistent = $storage->get('missing_key', 'default'); // Возвращает 'default'
     * 
     * // Для получения сырых сериализованных значений используйте getRaw()
     * $userIdRaw = $storage->getRaw('user_id'); // Возвращает 's:3:"123";'
     * ```
     * 
     * @see AbstractStorage::getRaw()
     * @see AbstractStorage::has()
     * @see AbstractStorage::set()
     */
    public function get (string $key, mixed $default = null): mixed
    {
        if ($this->has($key)) {
            $value = $this->{$key};
            if ($value !== null && is_string($value)) {
                return unserialize($value);
            }
            return $value;
        }
        
        return $default;
    }
    
    /**
     * Получить сырое сериализованное значение из хранилища
     * 
     * Этот метод извлекает значение из хранилища по его ключу без десериализации.
     * Если ключ существует, возвращается сериализованное значение как есть.
     * Если ключ не существует, возвращается предоставленное значение по умолчанию.
     *
     * @param string $key Ключ для получения из хранилища
     * @param mixed $default Значение по умолчанию для возврата, если ключ не существует
     *
     * @return mixed Сохраненное значение (сериализованное) или значение по умолчанию
     * 
     * @example
     * ```php
     * $storage = Session::getInstance();
     * 
     * // Сохранить некоторые данные
     * $storage->set('user_id', 123);
     * $storage->set('preferences', ['theme' => 'dark']);
     * 
     * // Получить сырые сериализованные значения
     * $userIdRaw = $storage->getRaw('user_id'); // Возвращает 's:3:"123";'
     * $prefsRaw = $storage->getRaw('preferences'); // Возвращает 'a:1:{s:5:"theme";s:4:"dark";}'
     * $nonExistent = $storage->getRaw('missing_key', 'default'); // Возвращает 'default'
     * ```
     * 
     * @see AbstractStorage::get()
     * @see AbstractStorage::has()
     * @see AbstractStorage::set()
     */
    public function getRaw (string $key, mixed $default = null): mixed
    {
        if ($this->has($key)) {
            return $this->{$key};
        }
        
        return $default;
    }

    /**
     * Получить все данные из хранилища в виде массива
     * 
     * Этот метод возвращает все сохраненные данные из хранилища в виде
     * ассоциативного массива. Все значения автоматически десериализуются
     * перед возвратом, аналогично методу get().
     *
     * @return array<string, mixed> Ассоциативный массив всех данных (десериализованных)
     * 
     * @example
     * ```php
     * $storage = Session::getInstance();
     * $storage->set('user_id', 123);
     * $storage->set('preferences', ['theme' => 'dark']);
     * $storage->set('name', 'John');
     * 
     * // Получение всех данных
     * $allData = $storage->all();
     * 
     * // Результат:
     * // [
     * //     'user_id' => 123,
     * //     'preferences' => ['theme' => 'dark'],
     * //     'name' => 'John'
     * // ]
     * 
     * // Использование в циклах
     * foreach ($storage->all() as $key => $value) {
     *     echo "{$key}: " . (is_array($value) ? json_encode($value) : $value) . "\n";
     * }
     * ```
     * 
     * @see AbstractStorage::get()
     * @see AbstractStorage::getRaw()
     */
    public function all(): array
    {
        $result = [];
        $reflection = new \ReflectionObject($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
        
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $key = $property->getName();
            // Пропускаем системные свойства
            if (in_array($key, ['expire', 'lazy'])) {
                continue;
            }
            $value = $property->isInitialized($this) ? $property->getValue($this) : null;
            if ($value !== null && is_string($value)) {
                $result[$key] = unserialize($value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}