<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use PHPUnit\Framework\TestCase;
use CloudCastle\HttpRequest\Server\Server;
use ReflectionClass;

final class ServerTest extends TestCase
{
    protected function setUp(): void
    {
        $_SERVER['FOO'] = 'bar';
        $ref = new ReflectionClass(Server::class);
        $prop = $ref->getProperty('instance');
        $prop->setAccessible(true);
        $prop->setValue([]);
    }

    public function testGetAndMagicGet(): void
    {
        $server = Server::getInstance();
        $this->assertEquals('bar', $server->get('FOO'));
        $this->assertEquals('bar', $server->foo);
    }

    public function testGetDefault(): void
    {
        $server = Server::getInstance();
        $this->assertEquals('default', $server->get('not_exist', 'default'));
    }

    public function testAll(): void
    {
        $server = Server::getInstance();
        $all = $server->all();
        $this->assertArrayHasKey('foo', $all);
    }

    public function testConstructor(): void
    {
        $_SERVER['TEST_SERVER_VAR'] = 'test_value';
        
        $reflection = new ReflectionClass(Server::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Server::class, $instance);
        
        unset($_SERVER['TEST_SERVER_VAR']);
    }
} 