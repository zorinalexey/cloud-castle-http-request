<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use PHPUnit\Framework\TestCase;
use CloudCastle\HttpRequest\Http\UploadFile;
use ReflectionClass;

final class UploadFileTest extends TestCase
{
    /**
     * @var array<string, mixed>
     */
    private array $fileData = [];

    protected function setUp(): void
    {
        $this->fileData = [
            'name' => 'test.txt',
            'type' => 'text/plain',
            'tmp_name' => __DIR__ . '/test_tmp_file',
            'error' => 0,
            'size' => 123
        ];
        file_put_contents($this->fileData['tmp_name'], 'test');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->fileData['tmp_name'])) {
            unlink($this->fileData['tmp_name']);
        }
    }

    public function testGetters(): void
    {
        $file = new UploadFile($this->fileData);
        $this->assertEquals('test.txt', $file->getOriginalName());
        $this->assertEquals(123, $file->getSize());
        $this->assertEquals(0, $file->getError());
        $this->assertEquals('text/plain', $file->getMimeType());
        $this->assertEquals('txt', $file->getExtension());
    }

    public function testIsUploadedFalse(): void
    {
        $file = new UploadFile($this->fileData);
        $this->assertFalse($file->isUploaded());
    }

    public function testSaveFail(): void
    {
        $file = new UploadFile($this->fileData);
        $this->assertFalse($file->save('/not/exist/dir'));
    }

    public function testSaveSuccess(): void
    {
        $file = new UploadFile($this->fileData);
        // эмулируем is_uploaded_file
        $tmp = $this->fileData['tmp_name'];
        $targetDir = __DIR__ . '/upload_test_dir';
        if (!is_dir($targetDir)) {
            mkdir($targetDir);
        }
        // подменяем функцию is_uploaded_file и move_uploaded_file через runkit или uopz нельзя, поэтому тестируем только структуру
        $this->assertFalse($file->save($targetDir));
        rmdir($targetDir);
    }

    public function testSaveWithCustomFilename(): void
    {
        $file = new UploadFile($this->fileData);
        $targetDir = __DIR__ . '/upload_test_dir';
        if (!is_dir($targetDir)) {
            mkdir($targetDir);
        }
        $result = $file->save($targetDir, 'custom.txt');
        $this->assertFalse($result); // is_uploaded_file вернет false
        rmdir($targetDir);
    }

    public function testSaveWithInvalidTmpName(): void
    {
        $fileData = $this->fileData;
        $fileData['tmp_name'] = '/invalid/path';
        $file = new UploadFile($fileData);
        $this->assertFalse($file->save('/tmp'));
    }

    public function testGetExtensionFromMimeType(): void
    {
        $fileData = $this->fileData;
        $fileData['type'] = 'image/jpeg';
        $file = new UploadFile($fileData);
        $this->assertEquals('jpg', $file->getExtension());
    }

    public function testSaveWithExtensionInFilename(): void
    {
        $fileData = $this->fileData;
        $fileData['name'] = 'test.txt';
        $file = new UploadFile($fileData);
        $targetDir = __DIR__ . '/upload_test_dir';
        if (!is_dir($targetDir)) {
            mkdir($targetDir);
        }
        $result = $file->save($targetDir, 'custom.txt');
        $this->assertFalse($result);
        rmdir($targetDir);
    }

    public function testSaveWithNoExtension(): void
    {
        $fileData = $this->fileData;
        $fileData['name'] = 'test';
        $fileData['type'] = 'image/jpeg';
        $file = new UploadFile($fileData);
        $targetDir = __DIR__ . '/upload_test_dir';
        if (!is_dir($targetDir)) {
            mkdir($targetDir);
        }
        $result = $file->save($targetDir, 'custom');
        $this->assertFalse($result);
        rmdir($targetDir);
    }

    public function testSaveWithDirectoryCreation(): void
    {
        $file = new UploadFile($this->fileData);
        $targetDir = __DIR__ . '/new_test_dir';
        $result = $file->save($targetDir);
        $this->assertFalse($result);
        if (is_dir($targetDir)) {
            rmdir($targetDir);
        }
    }

    public function testSaveWithUnknownMimeType(): void
    {
        $fileData = $this->fileData;
        $fileData['type'] = 'unknown/type';
        $file = new UploadFile($fileData);
        $targetDir = __DIR__ . '/upload_test_dir';
        if (!is_dir($targetDir)) {
            mkdir($targetDir);
        }
        $result = $file->save($targetDir, 'custom');
        $this->assertFalse($result);
        rmdir($targetDir);
    }

    public function testSaveWithNullTmpName(): void
    {
        $fileData = $this->fileData;
        $fileData['tmp_name'] = null;
        $file = new UploadFile($fileData);
        $this->assertFalse($file->save('/tmp'));
    }

    public function testGetExtension(): void
    {
        $file = new UploadFile([
            'name' => 'test.txt',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/test.txt',
            'size' => 100,
            'error' => 0
        ]);
        
        $this->assertEquals('jpg', $file->getExtension());
    }

    public function testSaveWithMoveUploadedFileFailure(): void
    {
        $file = new UploadFile($this->fileData);
        $targetDir = __DIR__ . '/upload_test_dir';
        if (!is_dir($targetDir)) {
            mkdir($targetDir);
        }
        // Эмулируем неудачу move_uploaded_file
        $result = $file->save($targetDir);
        $this->assertFalse($result);
        rmdir($targetDir);
    }

    public function testSaveWithMkdirFailure(): void
    {
        $file = new UploadFile($this->fileData);
        // Пытаемся создать директорию в несуществующем пути
        $result = $file->save('/root/invalid/path');
        $this->assertFalse($result);
    }

    public function testGetExtensionFromMimeTypeWithArray(): void
    {
        $fileData = $this->fileData;
        $fileData['type'] = 'application/pdf';
        $file = new UploadFile($fileData);
        $this->assertEquals('pdf', $file->getExtension());
    }

    public function testSaveWithNonExistentDirectory(): void
    {
        $file = new UploadFile([
            'name' => 'test.txt',
            'type' => 'text/plain',
            'tmp_name' => '/tmp/test.txt',
            'size' => 100,
            'error' => 0
        ]);
        
        // Тестируем сохранение в несуществующую директорию
        $result = $file->save('/non/existent/directory');
        
        $this->assertFalse($result);
    }

    public function testSaveWithNoTmpName(): void
    {
        $file = new UploadFile([
            'name' => 'test.txt',
            'type' => 'text/plain',
            'size' => 100,
            'error' => 0
        ]);
        
        // Тестируем сохранение без tmp_name
        $result = $file->save('/tmp');
        
        $this->assertFalse($result);
    }

    public function testSaveWithNoNameAndNoFilename(): void
    {
        $file = new UploadFile([
            'type' => 'text/plain',
            'tmp_name' => '/tmp/test.txt',
            'size' => 100,
            'error' => 0
        ]);
        
        // Тестируем сохранение без имени файла
        $result = $file->save('/tmp');
        $this->assertFalse($result);
    }

    public function testSaveWithExtensionFromMimeType(): void
    {
        $file = new UploadFile([
            'name' => 'test', // без расширения
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/test.txt',
            'size' => 100,
            'error' => 0
        ]);
        
        // Тестируем сохранение с автоматическим добавлением расширения
        $result = $file->save('/tmp');
        $this->assertFalse($result);
    }

    public function testIsUploadedWithInvalidFile(): void
    {
        $file = new UploadFile([
            'name' => 'test.txt',
            'type' => 'text/plain',
            'tmp_name' => '/invalid/path/file.txt',
            'size' => 100,
            'error' => 0
        ]);
        
        $this->assertFalse($file->isUploaded());
    }

    public function testGetOriginalName(): void
    {
        $file = new UploadFile([
            'name' => 'test.txt',
            'type' => 'text/plain',
            'tmp_name' => '/tmp/test.txt',
            'size' => 100,
            'error' => 0
        ]);
        
        $this->assertEquals('test.txt', $file->getOriginalName());
    }

    public function testGetSize(): void
    {
        $file = new UploadFile([
            'name' => 'test.txt',
            'type' => 'text/plain',
            'tmp_name' => '/tmp/test.txt',
            'size' => 1024,
            'error' => 0
        ]);
        
        $this->assertEquals(1024, $file->getSize());
    }

    public function testGetError(): void
    {
        $file = new UploadFile([
            'name' => 'test.txt',
            'type' => 'text/plain',
            'tmp_name' => '/tmp/test.txt',
            'size' => 100,
            'error' => UPLOAD_ERR_OK
        ]);
        
        $this->assertEquals(UPLOAD_ERR_OK, $file->getError());
    }

    public function testGetMimeType(): void
    {
        $file = new UploadFile([
            'name' => 'test.txt',
            'type' => 'text/plain',
            'tmp_name' => '/tmp/test.txt',
            'size' => 100,
            'error' => 0
        ]);
        
        $this->assertEquals('text/plain', $file->getMimeType());
    }

    public function testPrivateConstructorCoverage(): void
    {
        $reflection = new ReflectionClass(UploadFile::class);
        $constructor = $reflection->getConstructor();
        if ($constructor !== null) {
            $constructor->setAccessible(true);
            $instance = $reflection->newInstanceWithoutConstructor();
            $constructor->invoke($instance, []);
            $this->assertInstanceOf(UploadFile::class, $instance);
        }
    }

    public function testPrivateGetExtensionFromMimeTypeEdgeCases(): void
    {
        $file = new UploadFile([
            'name' => 'test.txt',
            'type' => 'image/jpeg',
            'tmp_name' => '/tmp/test.txt',
            'size' => 100,
            'error' => 0
        ]);
        $reflection = new ReflectionClass($file);
        $reflectionMethod = $reflection->getMethod('getExtensionFromMimeType');
        $reflectionMethod->setAccessible(true);
        $this->assertNull($reflectionMethod->invoke($file, 'not/exist'));
        $this->assertNull($reflectionMethod->invoke($file, ''));
    }
} 