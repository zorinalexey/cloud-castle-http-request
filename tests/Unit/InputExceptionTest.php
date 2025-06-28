<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use PHPUnit\Framework\TestCase;
use CloudCastle\HttpRequest\Exceptions\InputException;

final class InputExceptionTest extends TestCase
{
    public function testInputException(): void
    {
        $exception = new InputException('Test message');
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertInstanceOf(\Exception::class, $exception);
    }
} 