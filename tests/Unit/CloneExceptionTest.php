<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use PHPUnit\Framework\TestCase;
use CloudCastle\HttpRequest\Exceptions\CloneException;

final class CloneExceptionTest extends TestCase
{
    public function testCloneException(): void
    {
        $exception = new CloneException('Test message');
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertInstanceOf(\Exception::class, $exception);
    }
} 