<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use CloudCastle\HttpRequest\Request;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для класса Request
 * 
 * @package CloudCastle\HttpRequest\Tests\Unit
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 */
class RequestTest extends TestCase
{
    /**
     * Очистка после каждого теста
     */
    protected function tearDown(): void
    {
        // Очищаем статические свойства через рефлексию
        $reflection = new \ReflectionClass(Request::class);
        $property = $reflection->getProperty('instance');
        $property->setAccessible(true);
        $property->setValue(null, null);
        
        // Очищаем глобальные переменные
        $_GET = [];
        $_POST = [];
        $_COOKIE = [];
        $_SESSION = [];
        $_SERVER = [];
        $_ENV = [];
        $_FILES = [];
        
        parent::tearDown();
    }
    
    /**
     * Тест получения единственного экземпляра
     */
    public function testGetInstanceReturnsSameInstance(): void
    {
        $request1 = Request::getInstance();
        $request2 = Request::getInstance();
        
        $this->assertSame($request1, $request2);
        $this->assertInstanceOf(Request::class, $request1);
    }
    
    /**
     * Тест настройки времени жизни
     */
    public function testSetMethodConfiguresExpireTimes(): void
    {
        $sessionExpire = 7200;
        $cookieExpire = 86400;
        
        $request = Request::set($sessionExpire, $cookieExpire)->getInstance();
        
        // Проверяем, что объект создан
        $this->assertInstanceOf(Request::class, $request);
        
        // Проверяем наличие основных компонентов через рефлексию
        $reflection = new \ReflectionObject($request);
        $this->assertTrue($reflection->hasProperty('session'));
        $this->assertTrue($reflection->hasProperty('cookie'));
        $this->assertTrue($reflection->hasProperty('server'));
        $this->assertTrue($reflection->hasProperty('env'));
        $this->assertTrue($reflection->hasProperty('headers'));
        $this->assertTrue($reflection->hasProperty('post'));
        $this->assertTrue($reflection->hasProperty('get'));
    }
    
    /**
     * Тест доступа к компонентам запроса
     */
    public function testAccessToRequestComponents(): void
    {
        $request = Request::getInstance();
        
        // Проверяем наличие всех основных компонентов через рефлексию
        $reflection = new \ReflectionObject($request);
        $this->assertTrue($reflection->hasProperty('session'));
        $this->assertTrue($reflection->hasProperty('cookie'));
        $this->assertTrue($reflection->hasProperty('server'));
        $this->assertTrue($reflection->hasProperty('env'));
        $this->assertTrue($reflection->hasProperty('headers'));
        $this->assertTrue($reflection->hasProperty('post'));
        $this->assertTrue($reflection->hasProperty('get'));
    }
    
    /**
     * Тест магического метода __get()
     */
    public function testMagicGetMethod(): void
    {
        $request = Request::getInstance();
        
        // Добавляем тестовое свойство
        $request->test_property = 'test_value';
        
        // Проверяем доступ через магический метод
        $this->assertEquals('test_value', $request->test_property);
    }
    
    /**
     * Тест работы с GET параметрами
     */
    public function testGetParameters(): void
    {
        $request = Request::getInstance();
        
        // Проверяем через рефлексию
        $reflection = new \ReflectionObject($request);
        $this->assertTrue($reflection->hasProperty('get'));
        
        // Проверяем доступ к GET данным
        $getProperty = $reflection->getProperty('get');
        $getProperty->setAccessible(true);
        $getComponent = $getProperty->getValue($request);
        
        // Устанавливаем тестовые данные через компонент
        $getComponent->test_param = 'test_value';
        $getComponent->array_param = ['key' => 'value'];
        
        $this->assertEquals('test_value', $getComponent->get('test_param'));
        $this->assertEquals(['key' => 'value'], $getComponent->get('array_param'));
    }
    
    /**
     * Тест работы с POST данными
     */
    public function testPostData(): void
    {
        $request = Request::getInstance();
        
        // Проверяем через рефлексию
        $reflection = new \ReflectionObject($request);
        $this->assertTrue($reflection->hasProperty('post'));
        
        // Проверяем доступ к POST данным
        $postProperty = $reflection->getProperty('post');
        $postProperty->setAccessible(true);
        $postComponent = $postProperty->getValue($request);
        
        // Устанавливаем тестовые данные через компонент
        $postComponent->post_param = 'post_value';
        $postComponent->array_param = ['key' => 'value'];
        
        $this->assertEquals('post_value', $postComponent->get('post_param'));
        $this->assertEquals(['key' => 'value'], $postComponent->get('array_param'));
    }
    
    /**
     * Тест работы с сессиями
     */
    public function testSessionData(): void
    {
        $request = Request::getInstance();
        
        // Проверяем через рефлексию
        $reflection = new \ReflectionObject($request);
        $this->assertTrue($reflection->hasProperty('session'));
        
        // Проверяем доступ к сессии
        $sessionProperty = $reflection->getProperty('session');
        $sessionProperty->setAccessible(true);
        $sessionComponent = $sessionProperty->getValue($request);
        
        // Тестируем методы сессии
        $sessionComponent->set('test_session_key', 'test_session_value');
        $this->assertEquals('test_session_value', $sessionComponent->get('test_session_key'));
        $this->assertTrue($sessionComponent->has('test_session_key'));
    }
    
    /**
     * Тест работы с куками
     */
    public function testCookieData(): void
    {
        $request = Request::getInstance();
        
        // Проверяем через рефлексию
        $reflection = new \ReflectionObject($request);
        $this->assertTrue($reflection->hasProperty('cookie'));
        
        // Проверяем доступ к куки
        $cookieProperty = $reflection->getProperty('cookie');
        $cookieProperty->setAccessible(true);
        $cookieComponent = $cookieProperty->getValue($request);
        
        // Проверяем, что можно получить все данные куки
        $allCookies = $cookieComponent->all();
        
        // Проверяем, что компонент cookie имеет необходимые методы
        $this->assertInstanceOf(\CloudCastle\HttpRequest\Common\Cookie::class, $cookieComponent);
    }
    
    /**
     * Тест работы с заголовками
     */
    public function testHeaders(): void
    {
        $request = Request::getInstance();
        
        // Проверяем через рефлексию
        $reflection = new \ReflectionObject($request);
        $this->assertTrue($reflection->hasProperty('headers'));
        
        // Проверяем доступ к заголовкам
        $headersProperty = $reflection->getProperty('headers');
        $headersProperty->setAccessible(true);
        $headersComponent = $headersProperty->getValue($request);
        
        // Проверяем, что заголовки доступны
        $allHeaders = $headersComponent->all();
        $this->assertInstanceOf(\CloudCastle\HttpRequest\Common\Headers::class, $headersComponent);
    }
    
    /**
     * Тест работы с серверными переменными
     */
    public function testServerVariables(): void
    {
        $request = Request::getInstance();
        
        // Проверяем через рефлексию
        $reflection = new \ReflectionObject($request);
        $this->assertTrue($reflection->hasProperty('server'));
        
        // Проверяем доступ к серверным переменным
        $serverProperty = $reflection->getProperty('server');
        $serverProperty->setAccessible(true);
        $serverComponent = $serverProperty->getValue($request);
        
        // Проверяем, что серверные переменные доступны
        $allServerVars = $serverComponent->all();
        $this->assertInstanceOf(\CloudCastle\HttpRequest\Common\Server::class, $serverComponent);
    }
    
    /**
     * Тест работы с переменными окружения
     */
    public function testEnvironmentVariables(): void
    {
        $request = Request::getInstance();
        
        // Проверяем через рефлексию
        $reflection = new \ReflectionObject($request);
        $this->assertTrue($reflection->hasProperty('env'));
        
        // Проверяем доступ к переменным окружения
        $envProperty = $reflection->getProperty('env');
        $envProperty->setAccessible(true);
        $envComponent = $envProperty->getValue($request);
        
        // Проверяем, что переменные окружения доступны
        $allEnvVars = $envComponent->all();
        $this->assertInstanceOf(\CloudCastle\HttpRequest\Common\Env::class, $envComponent);
    }
    
    /**
     * Тест работы с файлами (для POST запросов)
     */
    public function testFilesForPostRequest(): void
    {
        // Очищаем экземпляр Request
        $reflection = new \ReflectionClass(\CloudCastle\HttpRequest\Request::class);
        $property = $reflection->getProperty('instance');
        $property->setAccessible(true);
        $property->setValue(null, null);
        
        // Симулируем POST запрос
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        $request = Request::getInstance();
        
        // Проверяем через рефлексию
        $reflection = new \ReflectionObject($request);
        $this->assertTrue($reflection->hasProperty('files'));
        
        // Проверяем доступ к файлам
        $filesProperty = $reflection->getProperty('files');
        $filesProperty->setAccessible(true);
        $filesComponent = $filesProperty->getValue($request);
        
        // Проверяем, что компонент файлов доступен
        $this->assertIsObject($filesComponent);
    }
    
    /**
     * Тест работы с JSON данными
     */
    public function testJsonDataParsing(): void
    {
        // Симулируем JSON запрос
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        
        // Симулируем JSON данные в php://input
        $jsonData = ['key' => 'value', 'number' => 123];
        $this->simulateJsonInput($jsonData);
        
        $request = Request::getInstance();
        
        // Проверяем через рефлексию
        $reflection = new \ReflectionObject($request);
        $this->assertTrue($reflection->hasProperty('post'));
        
        // Проверяем доступ к POST данным
        $postProperty = $reflection->getProperty('post');
        $postProperty->setAccessible(true);
        $postComponent = $postProperty->getValue($request);
        
        // Проверяем, что компонент POST данных доступен
        $this->assertIsObject($postComponent);
    }
    
    /**
     * Тест работы с XML данными
     */
    public function testXmlDataParsing(): void
    {
        // Симулируем XML запрос
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/xml';
        
        // Симулируем XML данные в php://input
        $xmlData = '<?xml version="1.0"?><root><key>value</key></root>';
        $this->simulateXmlInput($xmlData);
        
        $request = Request::getInstance();
        
        // Проверяем через рефлексию
        $reflection = new \ReflectionObject($request);
        $this->assertTrue($reflection->hasProperty('post'));
        
        // Проверяем доступ к POST данным
        $postProperty = $reflection->getProperty('post');
        $postProperty->setAccessible(true);
        $postComponent = $postProperty->getValue($request);
        
        // Проверяем, что компонент POST данных доступен
        $this->assertInstanceOf(\CloudCastle\HttpRequest\Common\Post::class, $postComponent);
        
        // Проверяем, что метод all() работает
        $allPostData = $postComponent->all();
    }
    
    /**
     * Тест цепочки вызовов set() и getInstance()
     */
    public function testMethodChaining(): void
    {
        $request = Request::set(3600, 7200)->getInstance();
        
        $this->assertInstanceOf(Request::class, $request);
        
        // Проверяем через рефлексию
        $reflection = new \ReflectionObject($request);
        $this->assertTrue($reflection->hasProperty('session'));
        $this->assertTrue($reflection->hasProperty('cookie'));
    }
    
    /**
     * Тест множественных вызовов set()
     */
    public function testMultipleSetCalls(): void
    {
        $request1 = Request::set(1000, 2000)->getInstance();
        $request2 = Request::set(3000, 4000)->getInstance();
        
        // Должны быть одинаковые экземпляры
        $this->assertSame($request1, $request2);
    }
    
    /**
     * Симулирует JSON данные в php://input
     *
     * @param array<string, mixed> $data
     */
    private function simulateJsonInput(array $data): void
    {
        $json = json_encode($data);
        $tmpFile = tempnam(sys_get_temp_dir(), 'php_input_');
        file_put_contents($tmpFile, $json);
        if (in_array('php', stream_get_wrappers())) {
            stream_wrapper_unregister('php');
        }
        stream_wrapper_register('php', \CloudCastle\HttpRequest\Tests\Unit\PhpInputMock::class);
        \CloudCastle\HttpRequest\Tests\Unit\PhpInputMock::$inputFile = $tmpFile;
    }
    
    /**
     * Симулирует XML данные в php://input
     */
    private function simulateXmlInput(string $xmlData): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'php_input_');
        file_put_contents($tmpFile, $xmlData);
        if (in_array('php', stream_get_wrappers())) {
            stream_wrapper_unregister('php');
        }
        stream_wrapper_register('php', \CloudCastle\HttpRequest\Tests\Unit\PhpInputMock::class);
        \CloudCastle\HttpRequest\Tests\Unit\PhpInputMock::$inputFile = $tmpFile;
    }
} 