<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Traits;

/**
 * Trait SetExpireTrait
 *
 * Трейт для управления временем жизни (expire) сущностей, например, сессий или cookie.
 * Позволяет задать время жизни через статический метод setExpire().
 * Использует паттерн Singleton через GetInstanceTrait.
 *
 * Пример использования:
 * <code>
 * use CloudCastle\HttpRequest\Traits\SetExpireTrait;
 *
 * class Example {
 *     use SetExpireTrait;
 *     private function __construct() {}
 * }
 *
 * $obj = Example::setExpire(7200); // 2 часа
 * </code>
 */
trait SetExpireTrait
{
    use GetInstanceTrait;
    
    /**
     * Время жизни сущности (в секундах).
     *
     * @var int
     *
     * Пример:
     * <code>
     * static::$expire = 1800; // 30 минут
     * </code>
     */
    protected static int $expire = 3600;
    
    /**
     * Установить время жизни сущности (expire).
     * Возвращает Singleton-экземпляр класса.
     *
     * @param int $expire Время жизни в секундах
     * @return static Экземпляр класса
     *
     * Пример:
     * <code>
     * $obj = Example::setExpire(600); // 10 минут
     * </code>
     */
    public static function setExpire(int $expire): static
    {
        static::$expire = $expire;
        
        return static::getInstance();
    }
}