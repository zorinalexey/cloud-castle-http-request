<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use CloudCastle\HttpRequest\Traits\SetExpireTrait;

final class Cookie
{
    use SetExpireTrait;

    private function __construct()
    {
    
    }
    
    public function get(string $key, mixed $default = null): mixed
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
    }
}