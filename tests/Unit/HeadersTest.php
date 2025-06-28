<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use CloudCastle\HttpRequest\Http\Headers;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;

/**
 * Class HeadersTest
 */
class HeadersTest extends TestCase
{
    protected function setUp(): void
    {
        // Сброс singleton перед каждым тестом
        $reflection = new ReflectionClass(Headers::class);
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, []);
    }

    public function testGetAndMagicGet(): void
    {
        $_SERVER['HTTP_X_TEST_HEADER'] = 'test_value';
        
        $headers = Headers::getInstance();
        
        $this->assertEquals('test_value', $headers->get('X_TEST_HEADER'));
        $this->assertEquals('test_value', $headers->X_TEST_HEADER);
        
        unset($_SERVER['HTTP_X_TEST_HEADER']);
    }

    public function testAll(): void
    {
        $_SERVER['HTTP_X_ALL_TEST'] = 'all_test_value';
        
        $headers = Headers::getInstance();
        $all = $headers->all();
        
        $this->assertNotEmpty($all);
        
        unset($_SERVER['HTTP_X_ALL_TEST']);
    }

    public function testSet(): void
    {
        $headers = Headers::getInstance();
        
        $headers->X_SET_TEST = 'set_value';
        
        $this->assertEquals('set_value', $headers->get('X_SET_TEST'));
    }

    public function testConstructor(): void
    {
        $_SERVER['HTTP_X_CONSTRUCTOR_TEST'] = 'constructor_value';
        
        $reflection = new ReflectionClass(Headers::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Headers::class, $instance);
        
        unset($_SERVER['HTTP_X_CONSTRUCTOR_TEST']);
    }

    public function testConstructorWithServerHeaders(): void
    {
        // Симулируем HTTP заголовки в $_SERVER
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';
        $_SERVER['HTTP_USER_AGENT'] = 'TestAgent';
        
        $headers = Headers::getInstance();
        
        $this->assertInstanceOf(Headers::class, $headers);
        
        // Очищаем $_SERVER
        unset($_SERVER['HTTP_CONTENT_TYPE'], $_SERVER['HTTP_USER_AGENT']);
    }

    public function testConstructorWithJsonHeader(): void
    {
        // Симулируем JSON заголовок
        $_SERVER['HTTP_X_JSON_DATA'] = '{"key": "value"}';
        
        $headers = Headers::getInstance();
        
        $this->assertInstanceOf(Headers::class, $headers);
        
        // Очищаем $_SERVER
        unset($_SERVER['HTTP_X_JSON_DATA']);
    }

    public function testMagicSet(): void
    {
        $headers = Headers::getInstance();
        
        // Устанавливаем заголовок через магический сеттер
        $headers->X_CUSTOM_HEADER = 'custom-value';
        
        $this->assertEquals('custom-value', $headers->get('X_CUSTOM_HEADER'));
    }

    public function testGetInstanceReturnsSameInstance(): void
    {
        $instance1 = Headers::getInstance();
        $instance2 = Headers::getInstance();
        
        $this->assertSame($instance1, $instance2);
    }

    public function testPrivateConstructorCoverage(): void
    {
        // Тестируем покрытие приватного конструктора
        $reflection = new ReflectionClass(Headers::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Headers::class, $instance);
    }

    public function testMagicGet(): void
    {
        $_SERVER['HTTP_X_TEST_HEADER'] = 'test_value';
        
        $headers = Headers::getInstance();
        
        // Тестируем магический геттер
        $this->assertEquals('test_value', $headers->X_TEST_HEADER);
        
        unset($_SERVER['HTTP_X_TEST_HEADER']);
    }

    public function testSetWithArray(): void
    {
        $headers = Headers::getInstance();
        
        // Устанавливаем массив как заголовок
        $headers->X_ARRAY_HEADER = ['key' => 'value', 'number' => 123];
        
        $this->assertEquals(['key' => 'value', 'number' => 123], $headers->get('X_ARRAY_HEADER'));
    }

    public function testSetWithBoolean(): void
    {
        $headers = Headers::getInstance();
        
        // Устанавливаем boolean как заголовок
        $headers->X_BOOL_HEADER = true;
        
        $this->assertTrue($headers->get('X_BOOL_HEADER'));
    }

    public function testSetWithBooleanFalse(): void
    {
        $headers = Headers::getInstance();
        
        // Устанавливаем false как заголовок
        $headers->X_BOOL_FALSE_HEADER = false;
        
        $this->assertFalse($headers->get('X_BOOL_FALSE_HEADER'));
    }

    public function testSetWithObject(): void
    {
        $headers = Headers::getInstance();
        
        // Устанавливаем объект как заголовок
        $obj = new stdClass();
        $obj->key = 'value';
        $obj->number = 123;
        
        $headers->X_OBJECT_HEADER = $obj;
        
        $this->assertEquals($obj, $headers->get('X_OBJECT_HEADER'));
    }

    public function testConstructorWithApacheHeaders(): void
    {
        if (!function_exists('apache_request_headers')) {
            $this->markTestSkipped('apache_request_headers not available');
        }
        $headers = Headers::getInstance();
        $this->assertInstanceOf(Headers::class, $headers);
    }

    public function testConstructorWithGetAllHeaders(): void
    {
        if (!function_exists('getallheaders')) {
            $this->markTestSkipped('getallheaders not available');
        }
        $headers = Headers::getInstance();
        $this->assertInstanceOf(Headers::class, $headers);
    }

    public function testConstructorWithServerHeadersOnly(): void
    {
        // Очищаем все заголовки и устанавливаем только в $_SERVER
        $_SERVER['HTTP_X_SERVER_ONLY'] = 'server_value';
        
        $headers = Headers::getInstance();
        
        $this->assertInstanceOf(Headers::class, $headers);
        
        unset($_SERVER['HTTP_X_SERVER_ONLY']);
    }

    public function testConstructorWithNonJsonHeader(): void
    {
        // Симулируем не-JSON заголовок
        $_SERVER['HTTP_X_PLAIN_TEXT'] = 'plain text value';
        
        $headers = Headers::getInstance();
        
        $this->assertInstanceOf(Headers::class, $headers);
        
        unset($_SERVER['HTTP_X_PLAIN_TEXT']);
    }

    public function testGetMethod(): void
    {
        $_SERVER['HTTP_X_GET_TEST'] = 'get_test_value';
        
        $headers = Headers::getInstance();
        
        $this->assertEquals('get_test_value', $headers->get('X_GET_TEST'));
        $this->assertNull($headers->get('NON_EXISTENT_HEADER'));
        
        unset($_SERVER['HTTP_X_GET_TEST']);
    }

    public function testAllMethod(): void
    {
        $_SERVER['HTTP_X_ALL_TEST'] = 'all_test_value';
        
        $headers = Headers::getInstance();
        $all = $headers->all();
        
        $this->assertNotEmpty($all);
        $this->assertArrayHasKey('x_all_test', $all);
        
        unset($_SERVER['HTTP_X_ALL_TEST']);
    }

    public function testPrivateConstructorCoverageEdge(): void
    {
        $instance = \CloudCastle\HttpRequest\Http\Headers::createForTesting();
        $this->assertInstanceOf(\CloudCastle\HttpRequest\Http\Headers::class, $instance);
    }
} 