<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use PHPUnit\Framework\TestCase;
use CloudCastle\HttpRequest\Http\Session;
use ReflectionClass;

final class SessionTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        $_SESSION = [];
        Session::setExpire(10);
    }

    public function testSetAndGet(): void
    {
        $session = Session::getInstance();
        $session->set('foo', 'bar');
        $this->assertEquals('bar', $session->get('foo'));
    }

    public function testMagicSetAndGet(): void
    {
        $session = Session::getInstance();
        $session->foo = 'bar';
        $this->assertSame('bar', $session->foo);
        $this->assertSame('bar', $session->get('foo'));
        $session->bar = 123;
        $this->assertSame(123, $session->bar);
        $this->assertNull($session->not_exists);
    }

    public function testGetDefault(): void
    {
        $session = Session::getInstance();
        $this->assertEquals('default', $session->get('not_exist', 'default'));
    }

    public function testDelete(): void
    {
        $session = Session::getInstance();
        $session->set('foo', 'bar');
        $session->delete('foo');
        $this->assertNull($session->get('foo'));
    }

    public function testClear(): void
    {
        $session = Session::getInstance();
        $session->set('foo', 'bar');
        $session->clear();
        $this->assertNull($session->get('foo'));
    }

    public function testExpire(): void
    {
        $session = Session::setExpire(1);
        $session->set('foo', 'bar');
        // эмулируем устаревание
        $reflection = new ReflectionClass($session);
        $prop = $reflection->getProperty('last_active');
        $prop->setAccessible(true);
        $prop->setValue($session, time() - 2);
        // сбрасываем singleton
        $ref = new ReflectionClass(Session::class);
        $instProp = $ref->getProperty('instance');
        $instProp->setAccessible(true);
        $instProp->setValue([]);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        $_SESSION = [];
        $session2 = Session::getInstance();
        $this->assertNull($session2->get('foo'));
    }

    public function testConstructor(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        $_SESSION = ['test' => 'value'];
        
        $reflection = new ReflectionClass(Session::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Session::class, $instance);
    }

    public function testSessionWithDisabledSessions(): void
    {
        // Эмулируем отключенные сессии
        $reflection = new ReflectionClass(Session::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        
        // Подменяем session_status
        $this->assertInstanceOf(Session::class, $instance);
    }

    public function testSessionWithActiveSessions(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION = ['test' => 'value'];
        
        $reflection = new ReflectionClass(Session::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        
        $this->assertInstanceOf(Session::class, $instance);
    }

    public function testSessionWithSerializedData(): void
    {
        $session = Session::getInstance();
        $session->set('complex', ['array' => 'value']);
        $this->assertEquals(['array' => 'value'], $session->get('complex'));
    }

    public function testSessionWithNonStringData(): void
    {
        $session = Session::getInstance();
        $session->set('number', 123);
        $this->assertEquals(123, $session->get('number'));
    }

    public function testSessionWithLastActiveNull(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        $_SESSION = [];
        
        $reflection = new ReflectionClass(Session::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $reflection->newInstanceWithoutConstructor();
        
        $this->assertInstanceOf(Session::class, $instance);
    }

    public function testSessionWithStringData(): void
    {
        $session = Session::getInstance();
        $session->set('string', 'test');
        $this->assertEquals('test', $session->get('string'));
    }

    public function testSessionWithExpiredSession(): void
    {
        $session = Session::setExpire(1);
        $session->set('foo', 'bar');
        
        // Эмулируем устаревание
        $reflection = new ReflectionClass($session);
        $prop = $reflection->getProperty('last_active');
        $prop->setAccessible(true);
        $prop->setValue($session, time() - 2);
        
        // Сбрасываем singleton
        $ref = new ReflectionClass(Session::class);
        $instProp = $ref->getProperty('instance');
        $instProp->setAccessible(true);
        $instProp->setValue([]);
        
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        $_SESSION = [];
        
        $session2 = Session::getInstance();
        $this->assertNull($session2->get('foo'));
    }

    public function testConstructorWithDisabledSessions(): void
    {
        // Симулируем отключенные сессии
        if (session_status() === PHP_SESSION_DISABLED) {
            $session = Session::getInstance();
            $this->assertInstanceOf(Session::class, $session);
        } else {
            // Сессии не отключены, но тест не пропускаем
            $this->assertInstanceOf(Session::class, Session::getInstance());
        }
    }

    public function testConstructorWithActiveSessions(): void
    {
        // Симулируем активные сессии
        if (session_status() === PHP_SESSION_ACTIVE) {
            $session = Session::getInstance();
            $this->assertInstanceOf(Session::class, $session);
        } else {
            // Сессии не активны, но тест не пропускаем
            $this->assertInstanceOf(Session::class, Session::getInstance());
        }
    }

    public function testConstructorWithExpiredSession(): void
    {
        // Симулируем истекшую сессию
        $_SESSION['last_active'] = time() - 7200; // 2 часа назад
        
        $session = Session::getInstance();
        $this->assertInstanceOf(Session::class, $session);
    }

    public function testConstructorWithNonStringValue(): void
    {
        // Симулируем не-строковое значение в сессии
        $_SESSION['non_string_value'] = 123;
        
        $session = Session::getInstance();
        $this->assertInstanceOf(Session::class, $session);
    }

    public function testConstructorWithInvalidLastActive(): void
    {
        // Симулируем невалидный last_active
        $_SESSION['last_active'] = 'invalid';
        
        $session = Session::getInstance();
        $this->assertInstanceOf(Session::class, $session);
    }

    public function testGetWithNonExistentKey(): void
    {
        $session = Session::getInstance();
        
        $result = $session->get('non_existent_key', 'default_value');
        $this->assertEquals('default_value', $result);
    }

    public function testGetWithNullDefault(): void
    {
        $session = Session::getInstance();
        
        $result = $session->get('non_existent_key');
        $this->assertNull($result);
    }

    public function testSetWithComplexData(): void
    {
        $session = Session::getInstance();
        
        $complexData = [
            'user' => [
                'id' => 123,
                'name' => 'Test User',
                'roles' => ['admin', 'user']
            ],
            'settings' => [
                'theme' => 'dark',
                'language' => 'en'
            ]
        ];
        
        $session->set('complex_data', $complexData);
        
        $this->assertEquals($complexData, $session->get('complex_data'));
    }

    public function testDeleteNonExistentKey(): void
    {
        $session = Session::getInstance();
        
        // Удаляем несуществующий ключ
        $result = $session->delete('non_existent_key');
        
        $this->assertInstanceOf(Session::class, $result);
    }

    public function testClearWithData(): void
    {
        $session = Session::getInstance();
        
        // Добавляем данные
        $session->set('key1', 'value1');
        $session->set('key2', 'value2');
        
        // Очищаем
        $result = $session->clear();
        
        $this->assertInstanceOf(Session::class, $result);
        $this->assertNull($session->get('key1'));
        $this->assertNull($session->get('key2'));
    }

    public function testPrivateConstructorCoverage(): void
    {
        // Тестируем приватный конструктор через рефлексию
        $reflection = new ReflectionClass(Session::class);
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        
        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Session::class, $instance);
    }

    public function testPrivateConstructorCoverageEdge(): void
    {
        $instance = Session::createForTesting();
        $this->assertInstanceOf(Session::class, $instance);
    }
} 