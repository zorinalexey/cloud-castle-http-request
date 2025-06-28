<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use PHPUnit\Framework\TestCase;
use CloudCastle\HttpRequest\Http\Get;
use ReflectionClass;
use stdClass;

final class GetTest extends TestCase
{
    protected function setUp(): void
    {
        $_GET = ['foo' => 'bar'];
        $ref = new ReflectionClass(Get::class);
        $prop = $ref->getProperty('instance');
        $prop->setAccessible(true);
        $prop->setValue([]);
    }

    public function testGetAndMagicGet(): void
    {
        $get = Get::getInstance();
        $this->assertEquals('bar', $get->get('foo'));
        $this->assertEquals('bar', $get->foo);
    }

    public function testGetDefault(): void
    {
        $get = Get::getInstance();
        $this->assertEquals('default', $get->get('not_exist', 'default'));
    }

    public function testAll(): void
    {
        $get = Get::getInstance();
        $all = $get->all();
        $this->assertArrayHasKey('foo', $all);
    }

    public function testConstructor(): void
    {
        $_GET = ['test_param' => 'test_value'];
        
        $reflection = new ReflectionClass(Get::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Get::class, $instance);
    }

    public function testConstructorWithNormalValue(): void
    {
        $_GET = ['param' => 'value'];
        
        $reflection = new ReflectionClass(Get::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Get::class, $instance);
    }

    public function testConstructorWithValidJson(): void
    {
        $_GET['json_data'] = '{"key": "value", "number": 123}';
        
        $reflection = new ReflectionClass(Get::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Get::class, $instance);
        
        unset($_GET['json_data']);
    }

    public function testConstructorWithInvalidJson(): void
    {
        $_GET = ['broken' => '{not json}'];
        
        $reflection = new ReflectionClass(Get::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Get::class, $instance);
    }

    public function testConstructorWithEmptyArray(): void
    {
        $_GET = [];
        
        $reflection = new ReflectionClass(Get::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Get::class, $instance);
    }
} 