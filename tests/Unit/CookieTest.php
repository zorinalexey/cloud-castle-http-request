<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use CloudCastle\HttpRequest\Common\Cookie;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для класса Cookie
 * 
 * @package CloudCastle\HttpRequest\Tests\Unit
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 */
class CookieTest extends TestCase
{
    /**
     * Очистка статических свойств после каждого теста
     */
    protected function tearDown(): void
    {
        // Очищаем статические свойства через рефлексию
        $reflection = new \ReflectionClass(Cookie::class);
        $property = $reflection->getProperty('instance');
        $property->setAccessible(true);
        $property->setValue(null, []);
        
        parent::tearDown();
    }
    
    /**
     * Тест получения единственного экземпляра
     */
    public function testGetInstanceReturnsSameInstance(): void
    {
        $cookie1 = Cookie::getInstance();
        $cookie2 = Cookie::getInstance();
        
        $this->assertSame($cookie1, $cookie2);
        $this->assertInstanceOf(Cookie::class, $cookie1);
    }
    
    /**
     * Тест настройки времени жизни куки
     */
    public function testSetExpireConfiguresCookieLifetime(): void
    {
        $expireTime = 86400; // 24 часа
        
        $cookie = Cookie::setExpire($expireTime)::getInstance();
        
        $this->assertInstanceOf(Cookie::class, $cookie);
        
        // Проверяем, что время жизни действительно установлено в статическом свойстве
        $reflection = new \ReflectionClass(Cookie::class);
        $expireProperty = $reflection->getProperty('expire');
        $expireProperty->setAccessible(true);
        $expireArray = $expireProperty->getValue();
        
        $this->assertArrayHasKey(Cookie::class, $expireArray);
        $this->assertEquals($expireTime, $expireArray[Cookie::class]);
    }
    
    /**
     * Тест установки значения в cookie
     * 
     * @covers \CloudCastle\HttpRequest\Common\Cookie::set
     * @covers \CloudCastle\HttpRequest\Common\Cookie::get
     * @covers \CloudCastle\HttpRequest\Common\Cookie::has
     */
    public function testSetValueInCookie(): void
    {
        $cookie = Cookie::getInstance();
        
        $cookie->set('test_key', 'test_value');
        
        // Проверяем, что значение десериализовано
        $this->assertEquals('test_value', $cookie->get('test_key'));
        
        // Проверяем, что значение установлено в $_COOKIE
        $this->assertArrayHasKey('test_key', $_COOKIE);
        
        // Проверяем, что значение существует в объекте
        $this->assertTrue($cookie->has('test_key'));
        
        // Проверяем, что значение можно получить через магический метод
        $this->assertEquals('test_value', $cookie->test_key);
        
        // Проверяем, что несуществующий ключ возвращает null
        $this->assertNull($cookie->get('non_existing_key'));
        
        // Проверяем, что несуществующий ключ не есть в $_COOKIE
        $this->assertArrayNotHasKey('non_existing_key', $_COOKIE);
        
        // Проверяем, что несуществующий ключ не существует в объекте
        $this->assertFalse($cookie->has('non_existing_key'));
    }
    
    /**
     * Тест установки cookie с временем жизни
     * 
     * @covers \CloudCastle\HttpRequest\Common\Cookie::set
     * @covers \CloudCastle\HttpRequest\Common\Cookie::get
     * @covers \CloudCastle\HttpRequest\Common\Cookie::has
     */
    public function testSetCookieWithExpireTime(): void
    {
        $cookie = Cookie::setExpire(3600)->getInstance();
        
        $cookie->set('test_key', 'test_value');
        
        // Проверяем, что значение десериализовано
        $this->assertEquals('test_value', $cookie->get('test_key'));
        
        // Проверяем, что значение установлено в $_COOKIE как сериализованная строка
        $this->assertArrayHasKey('test_key', $_COOKIE);
        $this->assertEquals('s:10:"test_value";', $_COOKIE['test_key']);
        
        // Проверяем, что значение существует в объекте
        $this->assertTrue($cookie->has('test_key'));
        
        // Проверяем, что время жизни установлено правильно
        $reflection = new \ReflectionClass(Cookie::class);
        $expireProperty = $reflection->getProperty('expire');
        $expireProperty->setAccessible(true);
        $expireArray = $expireProperty->getValue();
        
        $this->assertArrayHasKey(Cookie::class, $expireArray);
        $this->assertEquals(3600, $expireArray[Cookie::class]);
        
        // Проверяем, что значение можно получить через магический метод
        $this->assertEquals('test_value', $cookie->test_key);
        
        // Проверяем, что несуществующий ключ возвращает null
        $this->assertNull($cookie->get('non_existing_key'));
        
        // Проверяем, что несуществующий ключ не есть в $_COOKIE
        $this->assertArrayNotHasKey('non_existing_key', $_COOKIE);
        
        // Проверяем, что значение можно получить с дефолтным значением
        $this->assertEquals('default_value', $cookie->get('non_existing_key', 'default_value'));
        
        // Проверяем, что значение является строкой
        $this->assertIsString($cookie->get('test_key'));
        
        // Проверяем длину строки
        $this->assertEquals(strlen('test_value'), strlen($cookie->get('test_key')));
        
        // Проверяем, что значение не пустое
        $this->assertNotEmpty($cookie->get('test_key'));
    }
    
    /**
     * Тест получения значения из cookie
     * 
     * @covers \CloudCastle\HttpRequest\Common\Cookie::set
     * @covers \CloudCastle\HttpRequest\Common\Cookie::get
     * @covers \CloudCastle\HttpRequest\Common\Cookie::has
     */
    public function testGetValueFromCookie(): void
    {
        $cookie = Cookie::getInstance();
        
        $cookie->set('test_key', 'test_value');
        
        // Проверяем, что возвращается десериализованное значение
        $this->assertEquals('test_value', $cookie->get('test_key'));
        
        // Проверяем, что значение существует в $_COOKIE
        $this->assertArrayHasKey('test_key', $_COOKIE);
        
        // Проверяем, что значение существует в объекте
        $this->assertTrue($cookie->has('test_key'));
        
        // Проверяем, что значение можно получить через магический метод
        $this->assertEquals('test_value', $cookie->test_key);
        
        // Проверяем, что несуществующий ключ возвращает null
        $this->assertNull($cookie->get('non_existing_key'));
        
        // Проверяем, что несуществующий ключ не есть в $_COOKIE
        $this->assertArrayNotHasKey('non_existing_key', $_COOKIE);
        
        // Проверяем, что несуществующий ключ не существует в объекте
        $this->assertFalse($cookie->has('non_existing_key'));
        
        // Проверяем, что значение можно получить с дефолтным значением
        $this->assertEquals('default_value', $cookie->get('non_existing_key', 'default_value'));
    }
    
    /**
     * Тест получения значения с значением по умолчанию
     */
    public function testGetValueWithDefault(): void
    {
        $cookie = Cookie::getInstance();
        
        $this->assertEquals('default_value', $cookie->get('non_existing_key', 'default_value'));
        $this->assertNull($cookie->get('another_non_existing_key'));
    }
    
    /**
     * Тест проверки существования ключа
     * 
     * @covers \CloudCastle\HttpRequest\Common\Cookie::set
     * @covers \CloudCastle\HttpRequest\Common\Cookie::has
     * @covers \CloudCastle\HttpRequest\Common\Cookie::get
     */
    public function testHasKey(): void
    {
        $cookie = Cookie::getInstance();
        
        $cookie->set('existing_key', 'value');
        
        $this->assertTrue($cookie->has('existing_key'));
        $this->assertFalse($cookie->has('non_existing_key'));
        
        // Проверяем, что значение установлено в $_COOKIE
        $this->assertArrayHasKey('existing_key', $_COOKIE);
        
        // Проверяем, что значение можно получить
        $this->assertEquals('value', $cookie->get('existing_key'));
        
        // Проверяем, что несуществующий ключ не есть в $_COOKIE
        $this->assertArrayNotHasKey('non_existing_key', $_COOKIE);
    }
    
    /**
     * Тест удаления ключа из cookie
     * 
     * @covers \CloudCastle\HttpRequest\Common\Cookie::set
     * @covers \CloudCastle\HttpRequest\Common\Cookie::remove
     * @covers \CloudCastle\HttpRequest\Common\Cookie::has
     */
    public function testDeleteKey(): void
    {
        $cookie = Cookie::getInstance();
        
        $cookie->set('key_to_delete', 'value');
        $cookie->set('key_to_keep', 'value');
        
        // Проверяем, что ключи установлены
        $this->assertTrue($cookie->has('key_to_delete'));
        $this->assertTrue($cookie->has('key_to_keep'));
        $this->assertArrayHasKey('key_to_delete', $_COOKIE);
        $this->assertArrayHasKey('key_to_keep', $_COOKIE);
        
        $cookie->remove('key_to_delete');
        
        $this->assertFalse($cookie->has('key_to_delete'));
        $this->assertTrue($cookie->has('key_to_keep'));
        
        // Проверяем, что ключ удален из $_COOKIE
        $this->assertArrayNotHasKey('key_to_delete', $_COOKIE);
        $this->assertArrayHasKey('key_to_keep', $_COOKIE);
    }
    
    /**
     * Тест получения всех данных cookie
     * 
     * @covers \CloudCastle\HttpRequest\Common\Cookie::set
     * @covers \CloudCastle\HttpRequest\Common\Cookie::all
     */
    public function testGetAllCookieData(): void
    {
        $cookie = Cookie::getInstance();
        
        $cookie->set('key1', 'value1');
        $cookie->set('key2', 'value2');
        $cookie->set('key3', ['nested' => 'value']);
        
        $allData = $cookie->all();
        
        $this->assertArrayHasKey('key1', $allData);
        $this->assertArrayHasKey('key2', $allData);
        $this->assertArrayHasKey('key3', $allData);
        // Проверяем десериализованные значения
        $this->assertEquals('value1', $allData['key1']);
        $this->assertEquals('value2', $allData['key2']);
        $this->assertEquals(['nested' => 'value'], $allData['key3']);
        
        // Проверяем, что все значения установлены в $_COOKIE
        $this->assertArrayHasKey('key1', $_COOKIE);
        $this->assertArrayHasKey('key2', $_COOKIE);
        $this->assertArrayHasKey('key3', $_COOKIE);
        
        // Проверяем, что все значения существуют в объекте
        $this->assertTrue($cookie->has('key1'));
        $this->assertTrue($cookie->has('key2'));
        $this->assertTrue($cookie->has('key3'));
        
        // Проверяем, что количество элементов правильное
        $this->assertGreaterThanOrEqual(3, count($allData));
    }
    
    /**
     * Тест работы с различными типами данных
     * 
     * @covers \CloudCastle\HttpRequest\Common\Cookie::set
     * @covers \CloudCastle\HttpRequest\Common\Cookie::get
     */
    public function testVariousDataTypes(): void
    {
        $cookie = Cookie::getInstance();
        
        // Строка
        $cookie->set('string_key', 'string_value');
        $this->assertEquals('string_value', $cookie->get('string_key'));
        $this->assertTrue($cookie->has('string_key'));
        $this->assertArrayHasKey('string_key', $_COOKIE);
        
        // Число
        $cookie->set('number_key', 42);
        $this->assertEquals(42, $cookie->get('number_key'));
        $this->assertTrue($cookie->has('number_key'));
        $this->assertArrayHasKey('number_key', $_COOKIE);
        
        // Булево значение
        $cookie->set('bool_key', true);
        $this->assertTrue($cookie->get('bool_key'));
        $this->assertTrue($cookie->has('bool_key'));
        $this->assertArrayHasKey('bool_key', $_COOKIE);
        
        // Массив
        $cookie->set('array_key', [1, 2, 3]);
        $this->assertEquals([1, 2, 3], $cookie->get('array_key'));
        $this->assertTrue($cookie->has('array_key'));
        $this->assertArrayHasKey('array_key', $_COOKIE);
        
        // Объект
        $object = new \stdClass();
        $object->property = 'value';
        $cookie->set('object_key', $object);
        $this->assertEquals($object, $cookie->get('object_key'));
        $this->assertTrue($cookie->has('object_key'));
        $this->assertArrayHasKey('object_key', $_COOKIE);
        
        // Проверяем, что все значения можно получить через магические методы
        $this->assertEquals('string_value', $cookie->string_key);
        $this->assertEquals(42, $cookie->number_key);
        $this->assertTrue($cookie->bool_key);
        $this->assertEquals([1, 2, 3], $cookie->array_key);
        $this->assertEquals($object, $cookie->object_key);
    }
    
    /**
     * Тест работы с JSON данными
     * 
     * @covers \CloudCastle\HttpRequest\Common\Cookie::set
     * @covers \CloudCastle\HttpRequest\Common\Cookie::get
     * @covers \CloudCastle\HttpRequest\Common\Cookie::has
     */
    public function testJsonData(): void
    {
        $cookie = Cookie::getInstance();
        
        $jsonData = ['key' => 'value', 'number' => 123];
        $cookie->set('json_key', $jsonData);
        
        // Проверяем десериализованный JSON массив
        $this->assertEquals($jsonData, $cookie->get('json_key'));
        
        // Проверяем, что значение установлено в $_COOKIE
        $this->assertArrayHasKey('json_key', $_COOKIE);
        
        // Проверяем, что значение существует в объекте
        $this->assertTrue($cookie->has('json_key'));
        
        // Проверяем, что значение можно получить через магический метод
        $this->assertEquals($jsonData, $cookie->json_key);
        
        // Проверяем структуру JSON данных
        $retrievedData = $cookie->get('json_key');
        $this->assertIsArray($retrievedData);
        $this->assertArrayHasKey('key', $retrievedData);
        $this->assertArrayHasKey('number', $retrievedData);
        $this->assertEquals('value', $retrievedData['key']);
        $this->assertEquals(123, $retrievedData['number']);
    }
    
    /**
     * Тест регистронезависимого поиска
     * 
     * @covers \CloudCastle\HttpRequest\Common\Cookie::set
     * @covers \CloudCastle\HttpRequest\Common\Cookie::has
     * @covers \CloudCastle\HttpRequest\Common\Cookie::get
     */
    public function testCaseInsensitiveSearch(): void
    {
        $cookie = Cookie::getInstance();
        
        $cookie->set('TestKey', 'test_value');
        
        // Поиск должен быть регистрозависимым
        $this->assertTrue($cookie->has('TestKey'));
        $this->assertFalse($cookie->has('testkey'));
        $this->assertFalse($cookie->has('TESTKEY'));
        
        $this->assertEquals('test_value', $cookie->get('TestKey'));
        $this->assertNull($cookie->get('testkey'));
        $this->assertNull($cookie->get('TESTKEY'));
        
        // Проверяем, что значение установлено в $_COOKIE
        $this->assertArrayHasKey('TestKey', $_COOKIE);
        $this->assertArrayNotHasKey('testkey', $_COOKIE);
        $this->assertArrayNotHasKey('TESTKEY', $_COOKIE);
        
        // Проверяем, что значение можно получить через магический метод
        $this->assertEquals('test_value', $cookie->TestKey);
        
        // Проверяем, что несуществующие ключи не доступны через магический метод
        $this->assertNull($cookie->testkey);
        $this->assertNull($cookie->TESTKEY);
    }
    
    /**
     * Тест работы с пустыми значениями
     * 
     * @covers \CloudCastle\HttpRequest\Common\Cookie::set
     * @covers \CloudCastle\HttpRequest\Common\Cookie::get
     */
    public function testEmptyValues(): void
    {
        $cookie = Cookie::getInstance();
        
        $cookie->set('empty_string', '');
        $cookie->set('null_value', null);
        $cookie->set('zero_value', 0);
        
        // Проверяем десериализованные пустые значения
        $this->assertEquals('', $cookie->get('empty_string'));
        $this->assertNull($cookie->get('null_value'));
        $this->assertEquals(0, $cookie->get('zero_value'));
        
        // Проверяем, что значения установлены в $_COOKIE
        $this->assertArrayHasKey('empty_string', $_COOKIE);
        $this->assertArrayHasKey('null_value', $_COOKIE);
        $this->assertArrayHasKey('zero_value', $_COOKIE);
        
        // Проверяем, что значения существуют в объекте
        $this->assertTrue($cookie->has('empty_string'));
        $this->assertTrue($cookie->has('null_value'));
        $this->assertTrue($cookie->has('zero_value'));
        
        // Проверяем, что значения можно получить через магические методы
        $this->assertEquals('', $cookie->empty_string);
        $this->assertNull($cookie->null_value);
        $this->assertEquals(0, $cookie->zero_value);
        
        // Проверяем типы значений
        $this->assertIsString($cookie->get('empty_string'));
        $this->assertIsInt($cookie->get('zero_value'));
    }
    
    /**
     * Тест работы со специальными символами
     * 
     * @covers \CloudCastle\HttpRequest\Common\Cookie::set
     * @covers \CloudCastle\HttpRequest\Common\Cookie::get
     */
    public function testSpecialCharacters(): void
    {
        $cookie = Cookie::getInstance();
        
        $specialString = 'test@example.com';
        $cookie->set('email', $specialString);
        
        // Проверяем десериализованную строку со специальными символами
        $this->assertEquals($specialString, $cookie->get('email'));
        
        // Проверяем, что значение установлено в $_COOKIE
        $this->assertArrayHasKey('email', $_COOKIE);
        
        // Проверяем, что значение существует в объекте
        $this->assertTrue($cookie->has('email'));
        
        // Проверяем, что значение можно получить через магический метод
        $this->assertEquals($specialString, $cookie->email);
        
        // Проверяем, что значение является строкой
        $this->assertIsString($cookie->get('email'));
        
        // Проверяем длину строки
        $this->assertEquals(strlen($specialString), strlen($cookie->get('email')));
        
        // Проверяем, что строка содержит специальные символы
        $this->assertStringContainsString('@', $cookie->get('email'));
        $this->assertStringContainsString('.', $cookie->get('email'));
    }
    
    /**
     * Тест работы с длинными значениями
     * 
     * @covers \CloudCastle\HttpRequest\Common\Cookie::set
     * @covers \CloudCastle\HttpRequest\Common\Cookie::get
     */
    public function testLongValues(): void
    {
        $cookie = Cookie::getInstance();
        
        $longString = str_repeat('a', 1000);
        $cookie->set('long_key', $longString);
        
        // Проверяем десериализованную длинную строку
        $this->assertEquals($longString, $cookie->get('long_key'));
        
        // Проверяем, что значение установлено в $_COOKIE
        $this->assertArrayHasKey('long_key', $_COOKIE);
        
        // Проверяем, что значение существует в объекте
        $this->assertTrue($cookie->has('long_key'));
        
        // Проверяем, что значение можно получить через магический метод
        $this->assertEquals($longString, $cookie->long_key);
        
        // Проверяем, что значение является строкой
        $this->assertIsString($cookie->get('long_key'));
        
        // Проверяем длину строки
        $this->assertEquals(1000, strlen($cookie->get('long_key')));
        
        // Проверяем, что строка содержит только символ 'a'
        $this->assertEquals(str_repeat('a', 1000), $cookie->get('long_key'));
    }
    
    /**
     * Тест перезаписи значений
     * 
     * @covers \CloudCastle\HttpRequest\Common\Cookie::set
     * @covers \CloudCastle\HttpRequest\Common\Cookie::get
     */
    public function testOverwriteValues(): void
    {
        $cookie = Cookie::getInstance();
        
        $cookie->set('test_key', 'initial_value');
        $this->assertEquals('initial_value', $cookie->get('test_key'));
        $this->assertTrue($cookie->has('test_key'));
        $this->assertArrayHasKey('test_key', $_COOKIE);
        
        $cookie->set('test_key', 'new_value');
        $this->assertEquals('new_value', $cookie->get('test_key'));
        
        // Проверяем, что значение все еще существует в объекте
        $this->assertTrue($cookie->has('test_key'));
        
        // Проверяем, что значение все еще установлено в $_COOKIE
        $this->assertArrayHasKey('test_key', $_COOKIE);
        
        // Проверяем, что значение можно получить через магический метод
        $this->assertEquals('new_value', $cookie->test_key);
        
        // Проверяем, что старое значение больше не доступно
        $this->assertNotEquals('initial_value', $cookie->get('test_key'));
        
        // Проверяем, что значение является строкой
        $this->assertIsString($cookie->get('test_key'));
    }
    
    /**
     * Тест работы с массивами cookie
     * 
     * @covers \CloudCastle\HttpRequest\Common\Cookie::set
     * @covers \CloudCastle\HttpRequest\Common\Cookie::get
     */
    public function testCookieArray(): void
    {
        $cookie = Cookie::getInstance();
        
        $cookie->set('array_key', [123, 'test']);
        
        // Проверяем десериализованный массив
        $this->assertEquals([123, 'test'], $cookie->get('array_key'));
        
        // Проверяем, что значение установлено в $_COOKIE
        $this->assertArrayHasKey('array_key', $_COOKIE);
        
        // Проверяем, что значение существует в объекте
        $this->assertTrue($cookie->has('array_key'));
        
        // Проверяем, что значение можно получить через магический метод
        $this->assertEquals([123, 'test'], $cookie->array_key);
        
        // Проверяем, что значение является массивом
        $this->assertIsArray($cookie->get('array_key'));
        
        // Проверяем элементы массива
        $array = $cookie->get('array_key');
        $this->assertEquals(123, $array[0]);
        $this->assertEquals('test', $array[1]);
        
        // Проверяем количество элементов
        $this->assertEquals(2, count($array));
        
        // Проверяем типы элементов
        $this->assertIsInt($array[0]);
        $this->assertIsString($array[1]);
    }
    
    /**
     * Тест множественных операций
     * 
     * @covers \CloudCastle\HttpRequest\Common\Cookie::set
     * @covers \CloudCastle\HttpRequest\Common\Cookie::get
     * @covers \CloudCastle\HttpRequest\Common\Cookie::remove
     * @covers \CloudCastle\HttpRequest\Common\Cookie::has
     */
    public function testMultipleOperations(): void
    {
        $cookie = Cookie::getInstance();
        
        // Устанавливаем несколько значений
        $cookie->set('key1', 'value1')
               ->set('key2', 'value2')
               ->set('key3', 'value3');
        
        // Проверяем десериализованные значения
        $this->assertEquals('value1', $cookie->get('key1'));
        $this->assertEquals('value2', $cookie->get('key2'));
        $this->assertEquals('value3', $cookie->get('key3'));
        
        // Проверяем, что все значения установлены в $_COOKIE
        $this->assertArrayHasKey('key1', $_COOKIE);
        $this->assertArrayHasKey('key2', $_COOKIE);
        $this->assertArrayHasKey('key3', $_COOKIE);
        
        // Проверяем, что все значения существуют в объекте
        $this->assertTrue($cookie->has('key1'));
        $this->assertTrue($cookie->has('key2'));
        $this->assertTrue($cookie->has('key3'));
        
        // Проверяем, что значения можно получить через магические методы
        $this->assertEquals('value1', $cookie->key1);
        $this->assertEquals('value2', $cookie->key2);
        $this->assertEquals('value3', $cookie->key3);
        
        // Удаляем одно значение
        $cookie->remove('key2');
        $this->assertFalse($cookie->has('key2'));
        $this->assertTrue($cookie->has('key1'));
        $this->assertTrue($cookie->has('key3'));
        
        // Проверяем, что удаленное значение не есть в $_COOKIE
        $this->assertArrayNotHasKey('key2', $_COOKIE);
        $this->assertArrayHasKey('key1', $_COOKIE);
        $this->assertArrayHasKey('key3', $_COOKIE);
        
        // Проверяем, что удаленное значение возвращает null
        $this->assertNull($cookie->get('key2'));
        $this->assertNull($cookie->key2);
    }
} 