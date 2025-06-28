<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use CloudCastle\HttpRequest\Http\Cookie;
use CloudCastle\HttpRequest\Http\Files;
use CloudCastle\HttpRequest\Http\Get;
use CloudCastle\HttpRequest\Http\Headers;
use CloudCastle\HttpRequest\Http\Post;
use CloudCastle\HttpRequest\Http\Session;
use CloudCastle\HttpRequest\Server\Env;
use PHPUnit\Framework\TestCase;
use CloudCastle\HttpRequest\Interfaces\HttpRequestInterface;
use CloudCastle\HttpRequest\Request;

final class HttpRequestInterfaceTest extends TestCase
{
    protected function setUp(): void
    {
        // Устанавливаем поддерживаемый Content-Type во все возможные места
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_SERVER['HTTP_CONTENT-TYPE'] = 'application/x-www-form-urlencoded';
        $_SERVER['CONTENT-TYPE'] = 'application/x-www-form-urlencoded';
        $_POST = ['test' => 'value'];
        // Сброс singleton Request
        Request::resetInstance();
        // Сброс singleton Headers и других зависимых классов
        $classes = [
            Headers::class,
            Post::class,
            Get::class,
            Cookie::class,
            Session::class,
            Env::class,
            Files::class,
        ];
        foreach ($classes as $class) {
            $reflection = new \ReflectionClass($class);
            if ($reflection->hasProperty('instance')) {
                $instance = $reflection->getProperty('instance');
                $instance->setAccessible(true);
                $instance->setValue(null, []);
            }
        }
    }

    protected function tearDown(): void
    {
        // Очищаем все возможные ключи Content-Type
        unset(
            $_SERVER['CONTENT_TYPE'],
            $_SERVER['HTTP_CONTENT_TYPE'],
            $_SERVER['HTTP_CONTENT-TYPE'],
            $_SERVER['CONTENT-TYPE'],
            $_POST
        );
    }

    public function testRequestImplementsInterface(): void
    {
        $request = Request::getInstance();
        $this->assertInstanceOf(HttpRequestInterface::class, $request);
    }

    public function testInterfaceMethods(): void
    {
        $request = Request::getInstance();
        
        // Проверяем, что все методы интерфейса доступны
        $this->assertInstanceOf(\CloudCastle\HttpRequest\Request::class, $request);
        $this->assertInstanceOf(\CloudCastle\HttpRequest\Request::class, Request::init());
        $this->assertIsObject($request->__get('session'));
    }
} 