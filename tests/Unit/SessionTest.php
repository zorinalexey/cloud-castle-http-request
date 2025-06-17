<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use CloudCastle\HttpRequest\Common\Session;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для класса Session
 * 
 * @package CloudCastle\HttpRequest\Tests\Unit
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 */
class SessionTest extends TestCase
{
    /**
     * Очистка после каждого теста
     */
    protected function tearDown(): void
    {
        // Очищаем статические свойства через рефлексию
        $reflection = new \ReflectionClass(Session::class);
        
        // Очищаем статическое свойство instance
        $instanceProperty = $reflection->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(null, []);
        
        // Очищаем статические свойства expire
        $expireProperty = $reflection->getProperty('expire');
        $expireProperty->setAccessible(true);
        $expireProperty->setValue(null, []);
        
        // Очищаем сессию
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        
        // Очищаем глобальные переменные
        $_SESSION = [];
        
        parent::tearDown();
    }
    
    /**
     * Тест получения единственного экземпляра
     */
    public function testGetInstanceReturnsSameInstance(): void
    {
        $session1 = Session::getInstance();
        $session2 = Session::getInstance();
        
        $this->assertSame($session1, $session2);
        $this->assertInstanceOf(Session::class, $session1);
    }
    
    /**
     * Тест настройки времени жизни сессии
     */
    public function testSetExpireConfiguresSessionLifetime(): void
    {
        $expireTime = 7200; // 2 часа
        
        $session = Session::setExpire($expireTime)::getInstance();
        
        $this->assertInstanceOf(Session::class, $session);
        $this->assertTrue(session_status() === PHP_SESSION_ACTIVE);
    }
    
    /**
     * Тест установки значения в сессии
     */
    public function testSetValueInSession(): void
    {
        $session = Session::getInstance();
        
        $session->set('test_key', 'test_value');
        
        // Проверяем, что значение десериализовано
        $this->assertEquals('test_value', $session->get('test_key'));
    }
    
    /**
     * Тест установки массива в сессии
     */
    public function testSetArrayInSession(): void
    {
        $session = Session::getInstance();
        
        $arrayData = ['key1' => 'value1', 'key2' => 'value2'];
        $session->set('array_key', $arrayData);
        
        // Проверяем, что массив десериализован
        $this->assertEquals($arrayData, $session->get('array_key'));
    }
    
    /**
     * Тест получения значения из сессии
     */
    public function testGetValueFromSession(): void
    {
        $session = Session::getInstance();
        
        $session->set('test_key', 'test_value');
        
        // Проверяем, что возвращается десериализованное значение
        $this->assertEquals('test_value', $session->get('test_key'));
    }
    
    /**
     * Тест получения значения с значением по умолчанию
     */
    public function testGetValueWithDefault(): void
    {
        $session = Session::getInstance();
        
        $this->assertEquals('default_value', $session->get('non_existing_key', 'default_value'));
        $this->assertNull($session->get('another_non_existing_key'));
    }
    
    /**
     * Тест проверки существования ключа
     */
    public function testHasKey(): void
    {
        $session = Session::getInstance();
        
        $session->set('existing_key', 'value');
        
        $this->assertTrue($session->has('existing_key'));
        $this->assertFalse($session->has('non_existing_key'));
    }
    
    /**
     * Тест удаления ключа из сессии
     */
    public function testDeleteKey(): void
    {
        $session = Session::getInstance();
        
        $session->set('key_to_delete', 'value');
        $session->set('key_to_keep', 'value');
        
        $session->remove('key_to_delete');
        
        $this->assertFalse($session->has('key_to_delete'));
        $this->assertTrue($session->has('key_to_keep'));
    }
    
    /**
     * Тест очистки всей сессии
     * 
     * @covers \CloudCastle\HttpRequest\Common\Session::set
     * @covers \CloudCastle\HttpRequest\Common\Session::has
     * @covers \CloudCastle\HttpRequest\Common\Session::clear
     * @covers \CloudCastle\HttpRequest\Common\Session::all
     */
    public function testClearSession(): void
    {
        $session = Session::getInstance();
        
        $session->set('key1', 'value1');
        $session->set('key2', 'value2');
        $session->set('key3', 'value3');
        
        // Проверяем, что данные установлены
        $this->assertTrue($session->has('key1'));
        $this->assertTrue($session->has('key2'));
        $this->assertTrue($session->has('key3'));
        
        // Проверяем, что данные установлены в $_SESSION
        $this->assertArrayHasKey('key1', $_SESSION);
        $this->assertArrayHasKey('key2', $_SESSION);
        $this->assertArrayHasKey('key3', $_SESSION);
        
        // Проверяем, что данные можно получить
        $this->assertEquals('value1', $session->get('key1'));
        $this->assertEquals('value2', $session->get('key2'));
        $this->assertEquals('value3', $session->get('key3'));
        
        $session->clear();
        
        // Проверяем, что данные удалены из объекта
        $this->assertFalse($session->has('key1'));
        $this->assertFalse($session->has('key2'));
        $this->assertFalse($session->has('key3'));
        
        // Проверяем, что данные удалены из $_SESSION
        $this->assertArrayNotHasKey('key1', $_SESSION);
        $this->assertArrayNotHasKey('key2', $_SESSION);
        $this->assertArrayNotHasKey('key3', $_SESSION);
        
        // Проверяем, что все данные сессии пусты (только те, что мы установили)
        $allData = $session->all();
        $this->assertArrayNotHasKey('key1', $allData);
        $this->assertArrayNotHasKey('key2', $allData);
        $this->assertArrayNotHasKey('key3', $allData);
        
        // Проверяем, что удаленные данные возвращают null
        $this->assertNull($session->get('key1'));
        $this->assertNull($session->get('key2'));
        $this->assertNull($session->get('key3'));
        
        // Проверяем, что удаленные данные не доступны через магические методы
        $this->assertNull($session->key1);
        $this->assertNull($session->key2);
        $this->assertNull($session->key3);
    }
    
    /**
     * Тест получения всех данных сессии
     */
    public function testGetAllSessionData(): void
    {
        $session = Session::getInstance();
        
        $session->set('key1', 'value1');
        $session->set('key2', 'value2');
        $session->set('key3', ['nested' => 'value']);
        
        $allData = $session->all();
        
        $this->assertArrayHasKey('key1', $allData);
        $this->assertArrayHasKey('key2', $allData);
        $this->assertArrayHasKey('key3', $allData);
        // Проверяем десериализованные значения
        $this->assertEquals('value1', $allData['key1']);
        $this->assertEquals('value2', $allData['key2']);
        $this->assertEquals(['nested' => 'value'], $allData['key3']);
    }
    
    /**
     * Тест работы с различными типами данных
     */
    public function testVariousDataTypes(): void
    {
        $session = Session::getInstance();
        
        // Строка
        $session->set('string_key', 'string_value');
        $this->assertEquals('string_value', $session->get('string_key'));
        
        // Число
        $session->set('number_key', 42);
        $this->assertEquals(42, $session->get('number_key'));
        
        // Булево значение
        $session->set('bool_key', true);
        $this->assertTrue($session->get('bool_key'));
        
        // Массив
        $session->set('array_key', [1, 2, 3]);
        $this->assertEquals([1, 2, 3], $session->get('array_key'));
        
        // Объект
        $object = new \stdClass();
        $object->property = 'value';
        $session->set('object_key', $object);
        $this->assertEquals($object, $session->get('object_key'));
    }
    
    /**
     * Тест регистронезависимого поиска
     */
    public function testCaseInsensitiveSearch(): void
    {
        $session = Session::getInstance();
        
        $session->set('TestKey', 'test_value');
        
        // Поиск должен быть регистрозависимым
        $this->assertTrue($session->has('TestKey'));
        $this->assertFalse($session->has('testkey'));
        $this->assertFalse($session->has('TESTKEY'));
        
        $this->assertEquals('test_value', $session->get('TestKey'));
        $this->assertNull($session->get('testkey'));
        $this->assertNull($session->get('TESTKEY'));
    }
    
    /**
     * Тест работы с вложенными массивами
     */
    public function testNestedArrays(): void
    {
        $session = Session::getInstance();
        
        $nestedData = [
            'level1' => [
                'level2' => [
                    'level3' => 'deep_value'
                ]
            ]
        ];
        
        $session->set('nested_key', $nestedData);
        
        // Проверяем десериализованный вложенный массив
        $this->assertEquals($nestedData, $session->get('nested_key'));
    }
    
    /**
     * Тест множественных операций
     */
    public function testMultipleOperations(): void
    {
        $session = Session::getInstance();
        
        // Устанавливаем несколько значений
        $session->set('key1', 'value1')
                ->set('key2', 'value2')
                ->set('key3', 'value3');
        
        // Проверяем десериализованные значения
        $this->assertEquals('value1', $session->get('key1'));
        $this->assertEquals('value2', $session->get('key2'));
        $this->assertEquals('value3', $session->get('key3'));
        
        // Удаляем одно значение
        $session->remove('key2');
        $this->assertFalse($session->has('key2'));
        $this->assertTrue($session->has('key1'));
        $this->assertTrue($session->has('key3'));
    }
    
    /**
     * Тест пустых значений
     */
    public function testEmptyValues(): void
    {
        $session = Session::getInstance();
        
        $session->set('empty_string', '');
        $session->set('null_value', null);
        $session->set('zero_value', 0);
        
        // Проверяем десериализованные пустые значения
        $this->assertEquals('', $session->get('empty_string'));
        $this->assertNull($session->get('null_value'));
        $this->assertEquals(0, $session->get('zero_value'));
    }
} 