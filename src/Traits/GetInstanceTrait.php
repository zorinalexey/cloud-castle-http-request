<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Traits;

trait GetInstanceTrait
{
    private static array $instance = [];
    
    public static function getInstance(): self
    {
        $class = static::class;
        
        if(!self::$instance[$class]) {
            self::$instance[$class] = new self();
        }
        
        return self::$instance[$class];
    }
}