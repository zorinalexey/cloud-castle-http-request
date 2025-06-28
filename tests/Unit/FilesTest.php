<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use PHPUnit\Framework\TestCase;
use CloudCastle\HttpRequest\Http\Files;
use CloudCastle\HttpRequest\Http\UploadFile;
use ReflectionClass;

final class FilesTest extends TestCase
{
    protected function setUp(): void
    {
        $_FILES = [
            'file1' => [
                'name' => 'test.txt',
                'type' => 'text/plain',
                'tmp_name' => __DIR__ . '/test_tmp_file',
                'error' => 0,
                'size' => 123
            ]
        ];
        file_put_contents($_FILES['file1']['tmp_name'], 'test');
    }

    protected function tearDown(): void
    {
        if (isset($_FILES['file1']['tmp_name']) && file_exists($_FILES['file1']['tmp_name'])) {
            unlink($_FILES['file1']['tmp_name']);
        }
    }

    public function testGetFile(): void
    {
        $files = Files::getInstance();
        $all = $files->all();
        $this->assertArrayHasKey('test.txt', $all);
        $file = $all['test.txt'];
        $this->assertInstanceOf(UploadFile::class, $file);
        $this->assertEquals('test.txt', $file->getOriginalName());
    }

    public function testConstructor(): void
    {
        $_FILES = [
            'test_file' => [
                'name' => 'test.txt',
                'type' => 'text/plain',
                'tmp_name' => __DIR__ . '/test_tmp_file',
                'error' => 0,
                'size' => 123
            ]
        ];
        file_put_contents($_FILES['test_file']['tmp_name'], 'test');
        
        $ref = new ReflectionClass(Files::class);
        $prop = $ref->getProperty('instance');
        $prop->setAccessible(true);
        $prop->setValue([]);
        
        $constructor = $ref->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $ref->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Files::class, $instance);
        
        if (file_exists($_FILES['test_file']['tmp_name'])) {
            unlink($_FILES['test_file']['tmp_name']);
        }
    }

    public function testConstructorWithMultipleFiles(): void
    {
        $_FILES = [
            'test_file' => [
                'name' => ['file1.txt', 'file2.txt'],
                'type' => ['text/plain', 'text/plain'],
                'tmp_name' => [__DIR__ . '/test_tmp_file1', __DIR__ . '/test_tmp_file2'],
                'error' => [0, 0],
                'size' => [123, 456]
            ]
        ];
        file_put_contents($_FILES['test_file']['tmp_name'][0], 'test1');
        file_put_contents($_FILES['test_file']['tmp_name'][1], 'test2');
        
        $ref = new ReflectionClass(Files::class);
        $prop = $ref->getProperty('instance');
        $prop->setAccessible(true);
        $prop->setValue([]);
        
        $constructor = $ref->getConstructor();
        
        if ($constructor === null) {
            $this->fail('Constructor not found');
        }
        
        $constructor->setAccessible(true);
        $instance = $ref->newInstanceWithoutConstructor();
        $constructor->invoke($instance);
        
        $this->assertInstanceOf(Files::class, $instance);
        
        // Проверяем, что файлы были обработаны
        $all = $instance->all();
        $this->assertNotEmpty($all);
        
        // Очищаем временные файлы
        if (file_exists($_FILES['test_file']['tmp_name'][0])) {
            unlink($_FILES['test_file']['tmp_name'][0]);
        }
        if (file_exists($_FILES['test_file']['tmp_name'][1])) {
            unlink($_FILES['test_file']['tmp_name'][1]);
        }
    }
} 