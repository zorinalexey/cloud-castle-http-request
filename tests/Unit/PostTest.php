<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use PHPUnit\Framework\TestCase;
use CloudCastle\HttpRequest\Http\Post;
use ReflectionClass;
use stdClass;

final class PostTest extends TestCase
{
    protected function setUp(): void
    {
        $_POST = ['foo' => 'bar'];
        $ref = new ReflectionClass(Post::class);
        $prop = $ref->getProperty('instance');
        $prop->setAccessible(true);
        $prop->setValue([]);
    }

    public function testGetAndMagicGet(): void
    {
        $post = Post::getInstance();
        $this->assertEquals('bar', $post->get('foo'));
        $this->assertEquals('bar', $post->foo);
    }

    public function testGetDefault(): void
    {
        $post = Post::getInstance();
        $this->assertEquals('default', $post->get('not_exist', 'default'));
    }

    public function testAll(): void
    {
        $post = Post::getInstance();
        $all = $post->all();
        $this->assertArrayHasKey('foo', $all);
    }

    public function testConstructor(): void
    {
        $_POST = ['test_param' => 'test_value'];
        
        $reflection = new ReflectionClass(Post::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Post::class, $instance);
    }

    public function testConstructorWithNormalValue(): void
    {
        $_POST = ['param' => 'value'];
        
        $reflection = new ReflectionClass(Post::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Post::class, $instance);
    }

    public function testConstructorWithValidJson(): void
    {
        $_POST['json_data'] = '{"key": "value", "number": 123}';
        
        $reflection = new ReflectionClass(Post::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Post::class, $instance);
        
        unset($_POST['json_data']);
    }

    public function testConstructorWithInvalidJson(): void
    {
        $_POST = ['broken' => '{not json}'];
        
        $reflection = new ReflectionClass(Post::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Post::class, $instance);
    }

    public function testConstructorWithEmptyArray(): void
    {
        $_POST = [];
        
        $reflection = new ReflectionClass(Post::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Post::class, $instance);
    }
} 