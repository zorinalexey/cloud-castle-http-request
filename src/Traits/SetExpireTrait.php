<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Traits;

trait SetExpireTrait
{
    use GetInstanceTrait;
    
    protected static int $expire = 3600;
    
    public static function setExpire(int $expire): static
    {
        static::$expire = $expire;
        
        return static::getInstance();
    }
}