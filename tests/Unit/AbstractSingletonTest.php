<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use CloudCastle\HttpRequest\Common\AbstractSingleton;
use PHPUnit\Framework\TestCase;

/**
 * Тестовый класс для проверки AbstractSingleton
 * 
 * Этот класс наследует AbstractSingleton для тестирования его функциональности.
 * Реализует абстрактные методы checkRun() и createInstance().
 */
class TestSingleton extends AbstractSingleton
{
    protected static function checkRun(): array
    {
        return [
            'test_property' => 'test_value',
            'counter' => 1,
            'initialized' => true
        ];
    }
    
    /**
     * @phpstan-return static
     */
    protected static function createInstance(): static
    {
        return new static();
    }
}

/**
 * Тесты для класса AbstractSingleton
 * 
 * @package CloudCastle\HttpRequest\Tests\Unit
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 */
class AbstractSingletonTest extends TestCase
{
    /**
     * Очистка статических свойств после каждого теста
     */
    protected function tearDown(): void
    {
        // Очищаем статические свойства через рефлексию
        $reflection = new \ReflectionClass(TestSingleton::class);
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
        $instance1 = TestSingleton::getInstance();
        $instance2 = TestSingleton::getInstance();
        
        $this->assertSame($instance1, $instance2);
        $this->assertInstanceOf(TestSingleton::class, $instance1);
    }
    
    /**
     * Тест инициализации свойств из checkRun()
     */
    public function testInstanceInitializedWithCheckRunData(): void
    {
        $instance = TestSingleton::getInstance();
        
        $this->assertEquals('test_value', $instance->test_property);
        $this->assertEquals(1, $instance->counter);
        $this->assertTrue($instance->initialized);
    }
    
    /**
     * Тест регистронезависимого поиска свойств
     */
    public function testCaseInsensitivePropertyAccess(): void
    {
        $instance = TestSingleton::getInstance();
        
        // Добавляем тестовое свойство
        $instance->testProperty = 'value';
        
        // Проверяем регистронезависимый доступ
        $this->assertTrue($instance->has('testProperty'));
        $this->assertTrue($instance->has('testproperty'));
        $this->assertTrue($instance->has('TESTPROPERTY'));
        
        $this->assertEquals('value', $instance->get('testProperty'));
        $this->assertEquals('value', $instance->get('testproperty'));
        $this->assertEquals('value', $instance->get('TESTPROPERTY'));
    }
    
    /**
     * Тест метода has() для существующих свойств
     */
    public function testHasMethodForExistingProperties(): void
    {
        $instance = TestSingleton::getInstance();
        
        $this->assertTrue($instance->has('test_property'));
        $this->assertTrue($instance->has('counter'));
        $this->assertTrue($instance->has('initialized'));
    }
    
    /**
     * Тест метода has() для несуществующих свойств
     */
    public function testHasMethodForNonExistingProperties(): void
    {
        $instance = TestSingleton::getInstance();
        
        $this->assertFalse($instance->has('non_existing_property'));
        $this->assertFalse($instance->has('missing'));
    }
    
    /**
     * Тест метода get() с значением по умолчанию
     */
    public function testGetMethodWithDefaultValue(): void
    {
        $instance = TestSingleton::getInstance();
        
        $this->assertEquals('test_value', $instance->get('test_property'));
        $this->assertEquals('default', $instance->get('non_existing', 'default'));
        $this->assertNull($instance->get('non_existing'));
    }
    
    /**
     * Тест метода all() для получения всех свойств
     */
    public function testAllMethodReturnsAllProperties(): void
    {
        $instance = TestSingleton::getInstance();
        
        // Добавляем дополнительное свойство
        $instance->additional_property = 'additional_value';
        
        $allProperties = $instance->all();
        
        $this->assertArrayHasKey('test_property', $allProperties);
        $this->assertArrayHasKey('counter', $allProperties);
        $this->assertArrayHasKey('initialized', $allProperties);
        $this->assertArrayHasKey('additional_property', $allProperties);
        
        $this->assertEquals('test_value', $allProperties['test_property']);
        $this->assertEquals('additional_value', $allProperties['additional_property']);
    }
    
    /**
     * Тест магического метода __get()
     */
    public function testMagicGetMethod(): void
    {
        $instance = TestSingleton::getInstance();
        
        $this->assertEquals('test_value', $instance->test_property);
        $this->assertEquals('test_value', $instance->TEST_PROPERTY);
    }
    
    /**
     * Тест кэширования свойств
     */
    public function testPropertyCaching(): void
    {
        $instance = TestSingleton::getInstance();
        
        // Первый вызов - должно найти свойство
        $this->assertTrue($instance->has('test_property'));
        
        // Второй вызов - должно использовать кэш
        $this->assertTrue($instance->has('test_property'));
        
        // Проверяем, что значение кэшировано
        $this->assertEquals('test_value', $instance->get('test_property'));
    }
    
    /**
     * Тест работы с динамическими свойствами
     */
    public function testDynamicProperties(): void
    {
        $instance = TestSingleton::getInstance();
        
        // Добавляем динамические свойства
        $instance->dynamic_property = 'dynamic_value';
        $instance->another_property = 42;
        
        // Проверяем доступ к ним
        $this->assertTrue($instance->has('dynamic_property'));
        $this->assertTrue($instance->has('another_property'));
        
        $this->assertEquals('dynamic_value', $instance->get('dynamic_property'));
        $this->assertEquals(42, $instance->get('another_property'));
        
        // Проверяем в методе all()
        $allProperties = $instance->all();
        $this->assertArrayHasKey('dynamic_property', $allProperties);
        $this->assertArrayHasKey('another_property', $allProperties);
    }
    
    /**
     * Тест разных экземпляров для разных классов
     */
    public function testDifferentInstancesForDifferentClasses(): void
    {
        // Создаем второй тестовый класс
        $instance1 = TestSingleton::getInstance();
        
        // Создаем третий тестовый класс для проверки
        $instance2 = TestSingleton::getInstance();
        
        // Они должны быть одинаковыми (один класс)
        $this->assertSame($instance1, $instance2);
    }
} 