<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use PHPUnit\Framework\TestCase;
use CloudCastle\HttpRequest\Http\Cookie;

final class CookieTest extends TestCase
{
    protected function setUp(): void
    {
        $_COOKIE = ['foo' => 'bar'];
    }

    public function testGet(): void
    {
        $cookie = Cookie::getInstance();
        $this->assertEquals('bar', $cookie->get('foo'));
        $this->assertNull($cookie->get('not_exist'));
    }
} 