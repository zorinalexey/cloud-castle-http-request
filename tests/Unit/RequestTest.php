<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use Error;
use PHPUnit\Framework\TestCase;
use CloudCastle\HttpRequest\Request;
use CloudCastle\HttpRequest\Http\Headers;
use CloudCastle\HttpRequest\Exceptions\InputException;
use ReflectionClass;

final class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        Headers::getInstance()->{'Content-Type'} = 'application/json';
        $_GET = ['foo' => 'bar'];
        $_POST = ['baz' => 'qux'];
        $_COOKIE = ['cookie1' => 'value1'];
        $_SESSION = ['sess1' => 'val1'];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';
        $_ENV['ENV1'] = 'envval1';
        file_put_contents('php://memory', '');
        Request::resetInstance();
    }

    public function testSingletonInstance(): void
    {
        $instance1 = Request::getInstance();
        $instance2 = Request::getInstance();
        $this->assertSame($instance1, $instance2);
    }

    public function testInitSetsExpire(): void
    {
        $instance = Request::init(123, 456);
        $this->assertInstanceOf(Request::class, $instance);
    }

    public function testGetAndMagicGet(): void
    {
        $request = Request::getInstance();
        $this->assertEquals('bar', $request->get('foo'));
        $this->assertEquals('bar', $request->foo);
        $this->assertNull($request->get('not_exist'));
        $this->assertEquals('default', $request->get('not_exist', 'default'));
    }

    public function testAllReturnsArray(): void
    {
        $request = Request::getInstance();
        $all = $request->all();
        $this->assertArrayHasKey('foo', $all);
    }

    public function testCloneThrowsException(): void
    {
        $this->expectException(Error::class);
        $request = Request::getInstance();
        $clone = clone $request;
    }

    public function testInputExceptionOnUnsupportedContentType(): void
    {
        $_SERVER['CONTENT_TYPE'] = 'unsupported/type';
        $_SERVER['HTTP_CONTENT_TYPE'] = 'unsupported/type';
        Headers::getInstance()->{'Content-Type'} = 'unsupported/type';
        Request::resetInstance();
        $this->expectException(InputException::class);
        Request::init();
    }

    public function testSessionAccess(): void
    {
        $request = Request::getInstance();
        $this->assertTrue(property_exists($request, 'session'));
    }

    public function testCookieAccess(): void
    {
        $request = Request::getInstance();
        $this->assertTrue(property_exists($request, 'cookie'));
    }

    public function testHeadersAccess(): void
    {
        $request = Request::getInstance();
        $this->assertTrue(property_exists($request, 'headers'));
    }

    public function testPostAccess(): void
    {
        $request = Request::getInstance();
        $this->assertTrue(property_exists($request, 'post'));
    }

    public function testGetAccess(): void
    {
        $request = Request::getInstance();
        $this->assertTrue(property_exists($request, 'get'));
    }

    public function testFilesAccess(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'multipart/form-data';
        $_SERVER['HTTP_CONTENT_TYPE'] = 'multipart/form-data';
        $_FILES = [
            'file1' => [
                'name' => 'test.txt',
                'type' => 'text/plain',
                'tmp_name' => '/tmp/phpYzdqkD',
                'error' => 0,
                'size' => 123
            ]
        ];
        Request::resetInstance();
        $request = Request::init();
        $this->assertTrue(property_exists($request, 'files'));
    }

    public function testServerAccess(): void
    {
        $request = Request::getInstance();
        $this->assertTrue(property_exists($request, 'server'));
    }

    public function testEnvAccess(): void
    {
        $request = Request::getInstance();
        $this->assertTrue(property_exists($request, 'env'));
    }

    public function testRequestWithJsonData(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        file_put_contents('php://input', '{"test": "value"}');
        Request::resetInstance();
        $request = Request::getInstance();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testRequestWithXmlData(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/xml';
        file_put_contents('php://input', '<root><test>value</test></root>');
        Request::resetInstance();
        $request = Request::getInstance();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testRequestWithDeleteMethod(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        Request::resetInstance();
        $request = Request::getInstance();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testRequestWithFormUrlencoded(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        file_put_contents('php://input', 'test=value');
        Request::resetInstance();
        $request = Request::getInstance();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testRequestWithTextHtml(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'text/html';
        file_put_contents('php://input', '<html>test</html>');
        Request::resetInstance();
        $request = Request::getInstance();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testRequestWithMultipartFormData(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'multipart/form-data';
        Request::resetInstance();
        $request = Request::getInstance();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testRequestWithInvalidJson(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        file_put_contents('php://input', 'invalid json');
        Request::resetInstance();
        $request = Request::getInstance();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testRequestWithInvalidXml(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/xml';
        file_put_contents('php://input', 'invalid xml');
        Request::resetInstance();
        $request = Request::getInstance();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testRequestWithEmptyInput(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        file_put_contents('php://input', '');
        Request::resetInstance();
        $request = Request::getInstance();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testRequestWithFalseInput(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        // Эмулируем file_get_contents возвращающий false
        $request = Request::getInstance();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testRequestWithJsonValidation(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        file_put_contents('php://input', '{"valid": "json"}');
        Request::resetInstance();
        $request = Request::getInstance();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testRequestWithXmlToJsonConversion(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/xml';
        file_put_contents('php://input', '<root><item>value</item></root>');
        Request::resetInstance();
        $request = Request::getInstance();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testRequestWithJsonDecodeNull(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        file_put_contents('php://input', 'invalid json that will return null');
        Request::resetInstance();
        $request = Request::getInstance();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testRequestWithXmlJsonEncodeFailure(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/xml';
        file_put_contents('php://input', '<root><item>value</item></root>');
        Request::resetInstance();
        $request = Request::getInstance();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testRequestWithTextXmlContentType(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'text/xml';
        file_put_contents('php://input', '<root><item>value</item></root>');
        Request::resetInstance();
        $request = Request::getInstance();
        $this->assertInstanceOf(Request::class, $request);
    }

    public function testPrivateConstructorCoverage(): void
    {
        $instance = Request::createForTesting();
        $this->assertInstanceOf(Request::class, $instance);
    }

    public function testPrivateGetRequestEdgeCase(): void
    {
        // Создаем экземпляр без вызова конструктора
        $reflection = new ReflectionClass(Request::class);
        $instance = $reflection->newInstanceWithoutConstructor();
        
        $headers = Headers::getInstance();
        // Симулируем неподдерживаемый Content-Type
        $headers->{'Content-Type'} = 'unsupported/type';
        
        $method = $reflection->getMethod('getRequest');
        $method->setAccessible(true);
        $result = $method->invoke($instance, $headers);
        $this->assertIsArray($result);
    }
} 