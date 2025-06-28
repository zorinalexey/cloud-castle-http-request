<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Traits;

/**
 * Trait GetDataTrait
 *
 * Универсальный трейт для работы с коллекцией данных (например, GET, POST, SERVER, ENV, FILES, HEADERS).
 * Предоставляет методы для получения значения по ключу, получения всех данных и магический геттер.
 *
 * Пример использования:
 * <code>
 * use CloudCastle\HttpRequest\Traits\GetDataTrait;
 *
 * class Example {
 *     use GetDataTrait;
 *     public function __construct(array $data) {
 *         $this->data = $data;
 *     }
 * }
 *
 * $obj = new Example(['foo' => 'bar']);
 * echo $obj->get('foo'); // bar
 * echo $obj->foo; // bar
 * print_r($obj->all()); // ['foo' => 'bar']
 * </code>
 */
/**
 * @template TValue
 */
trait GetDataTrait
{
    /**
     * @var array<string, TValue>
     */
    protected array $data = [];
    
    /**
     * Магический геттер для доступа к данным по имени свойства.
     *
     * @param string $name Имя свойства
     * @return mixed Значение из коллекции данных или null, если не найдено
     *
     * Пример:
     * <code>
     * $value = $obj->foo;
     * </code>
     */
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }
    
    /**
     * Получить значение по ключу из коллекции данных.
     *
     * @param string $name Имя ключа (регистр не важен)
     * @param mixed $default Значение по умолчанию, если ключ не найден
     * @return mixed Значение из коллекции данных или $default
     *
     * Пример:
     * <code>
     * $value = $obj->get('foo', 'default');
     * </code>
     */
    public function get (string $name, mixed $default = null): mixed
    {
        $key = mb_strtolower($name);
        
        if(isset($this->data[$key])) {
            return $this->data[$key];
        }
        
        return $default;
    }
    
    /**
     * Получить все данные в виде ассоциативного массива.
     *
     * @return array<string, TValue> Все данные
     *
     * Пример:
     * <code>
     * $all = $obj->all();
     * </code>
     */
    /**
     * @return array<string, TValue>
     */
    public function all(): array
    {
        $data = [];
        
        foreach (array_keys($this->data) as $key) {
            $data[$key] = $this->get($key);
        }
        
        return $data;  
    }
}