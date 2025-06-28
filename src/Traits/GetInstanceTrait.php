<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Traits;

/**
 * Trait GetInstanceTrait
 *
 * Трейт для реализации паттерна Singleton для классов, которым требуется только один экземпляр.
 * Позволяет получать единственный экземпляр класса через статический метод getInstance().
 *
 * Пример использования:
 * <code>
 * use CloudCastle\HttpRequest\Traits\GetInstanceTrait;
 *
 * class Example {
 *     use GetInstanceTrait;
 *     private function __construct() {}
 * }
 *
 * $obj1 = Example::getInstance();
 * $obj2 = Example::getInstance();
 * var_dump($obj1 === $obj2); // true
 * </code>
 */
trait GetInstanceTrait
{
    /**
     * Массив экземпляров классов (по имени класса).
     *
     * @var array<string, self|null>
     *
     * Пример:
     * <code>
     * $instance = self::$instance[static::class];
     * </code>
     */
    private static array $instance = [];
    
    /**
     * Получить единственный экземпляр класса (Singleton).
     * Если экземпляр не создан — создаёт его.
     *
     * @return self Экземпляр класса
     *
     * Пример:
     * <code>
     * $obj = Example::getInstance();
     * </code>
     */
    public static function getInstance(): self
    {
        $class = static::class;
        
        if (!isset(self::$instance[$class])) {
            self::$instance[$class] = new self();
        }
        
        return self::$instance[$class];
    }
    
    /**
     * Сбросить экземпляр (внутренний метод для тестирования)
     */
    public static function resetInstance(): void
    {
        $class = static::class;
        self::$instance[$class] = null;
    }
}