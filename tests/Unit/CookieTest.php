<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use CloudCastle\HttpRequest\Http\Cookie;

/**
 * @property mixed $foo
 */

class CookieTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_COOKIE = [];
        Cookie::resetInstance();
    }

    public function testSingletonInstance(): void
    {
        $a = Cookie::getInstance();
        $b = Cookie::getInstance();
        $this->assertSame($a, $b);
    }

    public function testConstructorInitializesFromCookie(): void
    {
        $_COOKIE['foo'] = serialize('bar');
        Cookie::resetInstance();
        $cookie = Cookie::getInstance();
        $this->assertSame('bar', $cookie->get('foo'));
    }

    public function testGetReturnsValue(): void
    {
        $cookie = Cookie::getInstance();
        $cookie->set('foo', 'bar');
        $this->assertSame('bar', $cookie->get('foo'));
    }

    public function testGetReturnsDefaultIfNotExists(): void
    {
        $cookie = Cookie::getInstance();
        $this->assertSame('default', $cookie->get('not_exists', 'default'));
    }

    public function testSetStoresValue(): void
    {
        $cookie = Cookie::getInstance();
        $cookie->set('foo', 123);
        $this->assertSame(123, $cookie->get('foo'));
    }

    public function testSetOverwritesValue(): void
    {
        $cookie = Cookie::getInstance();
        $cookie->set('foo', 1);
        $cookie->set('foo', 2);
        $this->assertSame(2, $cookie->get('foo'));
    }

    public function testSetSerializesComplexTypes(): void
    {
        $cookie = Cookie::getInstance();
        $arr = ['a' => 1, 'b' => [2, 3]];
        $cookie->set('arr', $arr);
        $this->assertEquals($arr, $cookie->get('arr'));
    }

    public function testSetHandlesNull(): void
    {
        $cookie = Cookie::getInstance();
        $cookie->set('foo', null);
        $this->assertNull($cookie->get('foo'));
    }

    public function testMagicSetAndGet(): void
    {
        $cookie = Cookie::getInstance();
        $cookie->set('foo', 'bar');
        $this->assertSame('bar', $cookie->get('foo'));
    }

    public function testDeleteRemovesCookie(): void
    {
        $cookie = Cookie::getInstance();
        $cookie->set('foo', 'bar');
        $cookie->delete('foo');
        $this->assertNull($cookie->get('foo'));
    }

    public function testDeleteNonExistentKey(): void
    {
        $cookie = Cookie::getInstance();
        $this->assertNull($cookie->get('not_exists'));
        $cookie->delete('not_exists');
        $this->assertNull($cookie->get('not_exists'));
    }

    public function testClearRemovesAllCookies(): void
    {
        $cookie = Cookie::getInstance();
        $cookie->set('a', 1)->set('b', 2);
        $cookie->clear();
        $this->assertNull($cookie->get('a'));
        $this->assertNull($cookie->get('b'));
    }

    public function testChainingSetDeleteClear(): void
    {
        $cookie = Cookie::getInstance();
        $cookie->set('a', 1)->set('b', 2)->delete('a')->clear();
        $this->assertNull($cookie->get('a'));
        $this->assertNull($cookie->get('b'));
    }

    public function testSetWithEmptyValue(): void
    {
        $cookie = Cookie::getInstance();
        $cookie->set('empty', '');
        $this->assertSame('', $cookie->get('empty'));
    }

    public function testSetWithLongValue(): void
    {
        $cookie = Cookie::getInstance();
        $long = str_repeat('x', 4096);
        $cookie->set('long', $long);
        $this->assertSame($long, $cookie->get('long'));
    }

    public function testSetWithSpecialCharacters(): void
    {
        $cookie = Cookie::getInstance();
        $special = ' !@#$%^&*()_+-=~`[]{}|;:",.<>?/\\';
        $cookie->set('special', $special);
        $this->assertSame($special, $cookie->get('special'));
    }

    public function testInternalDataSync(): void
    {
        $cookie = Cookie::getInstance();
        $cookie->set('foo', 'bar');
        $reflection = new ReflectionClass($cookie);
        $prop = $reflection->getProperty('data');
        $prop->setAccessible(true);
        $data = $prop->getValue($cookie);
        $this->assertArrayHasKey('foo', $data);
        $this->assertSame('bar', $data['foo']);
    }
} 