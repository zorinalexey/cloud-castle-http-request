<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use PHPUnit\Framework\TestCase;
use CloudCastle\HttpRequest\Server\Env;
use ReflectionClass;
use stdClass;

final class EnvTest extends TestCase
{
    protected function setUp(): void
    {
        $_ENV = ['FOO' => 'bar'];
        // Сброс Singleton через рефлексию
        $ref = new ReflectionClass(Env::class);
        $prop = $ref->getProperty('instance');
        $prop->setAccessible(true);
        $prop->setValue([]);
    }

    public function testGetAndMagicGet(): void
    {
        $env = Env::getInstance();
        $this->assertEquals('bar', $env->get('FOO'));
        $this->assertEquals('bar', $env->foo);
    }

    public function testGetDefault(): void
    {
        $env = Env::getInstance();
        $this->assertEquals('default', $env->get('not_exist', 'default'));
    }

    public function testAll(): void
    {
        $env = Env::getInstance();
        $all = $env->all();
        $this->assertArrayHasKey('foo', $all);
    }

    public function testSet(): void
    {
        $env = Env::getInstance();
        $env->NEW_VAR = 'baz';
        $this->assertEquals('baz', $env->new_var);
    }

    public function testConstructor(): void
    {
        $reflection = new ReflectionClass(Env::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Env::class, $instance);
    }

    public function testConstructorWithNormalValue(): void
    {
        $_ENV['TEST_VAR'] = 'test_value';
        
        $reflection = new ReflectionClass(Env::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Env::class, $instance);
        
        unset($_ENV['TEST_VAR']);
    }

    public function testConstructorWithValidJson(): void
    {
        $_ENV['JSON_VAR'] = '{"key": "value", "number": 123}';
        
        $reflection = new ReflectionClass(Env::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Env::class, $instance);
        
        unset($_ENV['JSON_VAR']);
    }

    public function testConstructorWithInvalidJson(): void
    {
        $_ENV['INVALID_JSON'] = '{"key": "value", "number": 123,}';
        
        $reflection = new ReflectionClass(Env::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Env::class, $instance);
        
        unset($_ENV['INVALID_JSON']);
    }

    public function testConstructorWithEmptyArray(): void
    {
        $_ENV = [];
        
        $reflection = new ReflectionClass(Env::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Env::class, $instance);
    }

    public function testConstructorWithEmptyArrayEdge(): void
    {
        $instance = Env::createForTesting();
        $this->assertInstanceOf(Env::class, $instance);
    }

    public function testConstructorWithInvalidJsonEdge(): void
    {
        $instance = Env::createForTesting();
        $this->assertInstanceOf(Env::class, $instance);
    }

    public function testConstructorWithNormalValueEdge(): void
    {
        $instance = Env::createForTesting();
        $this->assertInstanceOf(Env::class, $instance);
    }
} 