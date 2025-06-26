<?php

namespace CloudCastle\HttpRequest\Tests\Unit;

use CloudCastle\HttpRequest\Http\Headers;
use CloudCastle\HttpRequest\Request;
use PHPUnit\Framework\TestCase;
use stdClass;

final class RequestTest extends TestCase
{
    /**
     * @return void
     * @covers Request::getInstance()
     * @covers Headers::getInstance()
     */
    public function testRequest(): void
    {
        $headers = Headers::getInstance();
        $headers->{'Content-Type'} = 'application/json';
        $request = Request::getInstance();
        
        $this->assertInstanceOf(Request::class, $request);
        $this->assertInstanceOf(Headers::class, $headers);
    }
}
