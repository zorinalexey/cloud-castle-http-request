<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Interfaces;

interface HttpRequestInterface
{
    public static function getInstance(): static;
    
    public static function init(int $secondsSession = 3600, int $secondCookie = 3600): static;
    
    public function __get(string $name): mixed;
}