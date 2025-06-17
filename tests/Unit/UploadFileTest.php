<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use CloudCastle\HttpRequest\Common\UploadFile;
use PHPUnit\Framework\TestCase;

/**
 * Тесты для класса UploadFile
 * 
 * @package CloudCastle\HttpRequest\Tests\Unit
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 */
class UploadFileTest extends TestCase
{
    private string $tempDir;
    private string $testFilePath;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Создаем временную директорию для тестов
        $this->tempDir = sys_get_temp_dir() . '/upload_file_test_' . uniqid();
        mkdir($this->tempDir, 0777, true);
        
        // Создаем тестовый файл
        $this->testFilePath = $this->tempDir . '/test_file.txt';
        file_put_contents($this->testFilePath, 'Test file content');
    }
    
    protected function tearDown(): void
    {
        // Очищаем временные файлы
        if (file_exists($this->testFilePath)) {
            unlink($this->testFilePath);
        }
        
        if (is_dir($this->tempDir)) {
            $this->removeDirectory($this->tempDir);
        }
        
        parent::tearDown();
    }
    
    /**
     * Рекурсивно удаляет директорию
     */
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        
        rmdir($dir);
    }
    
    /**
     * @return array<string, mixed>
     */
    private function createTestFileData(): array
    {
        return [
            'name' => 'test_file.txt',
            'type' => 'text/plain',
            'size' => 18,
            'tmp_name' => $this->testFilePath,
            'error' => UPLOAD_ERR_OK
        ];
    }
    
    /**
     * Тест конструктора
     */
    public function testConstructor(): void
    {
        $fileData = $this->createTestFileData();
        $uploadFile = new UploadFile($fileData);
        
        $this->assertEquals('test_file.txt', $uploadFile->name);
        $this->assertEquals('text/plain', $uploadFile->type);
        $this->assertEquals(18, $uploadFile->size);
        $this->assertEquals($this->testFilePath, $uploadFile->tmp_name);
        $this->assertEquals(UPLOAD_ERR_OK, $uploadFile->error);
    }
    
    /**
     * Тест метода getOriginalName()
     */
    public function testGetOriginalName(): void
    {
        $fileData = $this->createTestFileData();
        $uploadFile = new UploadFile($fileData);
        
        $this->assertEquals('test_file.txt', $uploadFile->getOriginalName());
    }
    
    /**
     * Тест метода getSize()
     */
    public function testGetSize(): void
    {
        $fileData = $this->createTestFileData();
        $uploadFile = new UploadFile($fileData);
        
        $this->assertEquals(18, $uploadFile->getSize());
    }
    
    /**
     * Тест метода getError()
     */
    public function testGetError(): void
    {
        $fileData = $this->createTestFileData();
        $uploadFile = new UploadFile($fileData);
        
        $this->assertEquals(UPLOAD_ERR_OK, $uploadFile->getError());
    }
    
    /**
     * Тест метода getMimeType()
     */
    public function testGetMimeType(): void
    {
        $fileData = $this->createTestFileData();
        $uploadFile = new UploadFile($fileData);
        
        $this->assertEquals('text/plain', $uploadFile->getMimeType());
    }
    
    /**
     * Тест метода getExtension()
     */
    public function testGetExtension(): void
    {
        $fileData = $this->createTestFileData();
        $uploadFile = new UploadFile($fileData);
        
        $this->assertEquals('txt', $uploadFile->getExtension());
    }
    
    /**
     * Тест метода getExtension() для различных MIME-типов
     */
    public function testGetExtensionForVariousMimeTypes(): void
    {
        $testCases = [
            ['type' => 'image/jpeg', 'expected' => 'jpg'],
            ['type' => 'image/png', 'expected' => 'png'],
            ['type' => 'application/pdf', 'expected' => 'pdf'],
            ['type' => 'text/html', 'expected' => 'html'],
            ['type' => 'application/json', 'expected' => 'json'],
            ['type' => 'unknown/type', 'expected' => '']
        ];
        
        foreach ($testCases as $testCase) {
            $fileData = $this->createTestFileData();
            $fileData['type'] = $testCase['type'];
            $uploadFile = new UploadFile($fileData);
            
            $this->assertEquals($testCase['expected'], $uploadFile->getExtension());
        }
    }
    
    /**
     * Тест метода isUploaded() для незагруженного файла
     */
    public function testIsUploadedForNonUploadedFile(): void
    {
        $fileData = $this->createTestFileData();
        $uploadFile = new UploadFile($fileData);
        
        $this->assertFalse($uploadFile->isUploaded());
    }
    
    /**
     * Тест метода save() с ошибкой (файл не загружен)
     */
    public function testSaveFailsForNonUploadedFile(): void
    {
        $fileData = $this->createTestFileData();
        $uploadFile = new UploadFile($fileData);
        
        $result = $uploadFile->save($this->tempDir);
        
        $this->assertFalse($result);
    }
    
    /**
     * Тест метода save() с отсутствующим tmp_name
     */
    public function testSaveFailsWithMissingTmpName(): void
    {
        $fileData = $this->createTestFileData();
        unset($fileData['tmp_name']);
        $uploadFile = new UploadFile($fileData);
        
        $result = $uploadFile->save($this->tempDir);
        
        $this->assertFalse($result);
    }
    
    /**
     * Тест с различными кодами ошибок
     */
    public function testVariousErrorCodes(): void
    {
        $errorCodes = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'Upload stopped by extension'
        ];
        
        foreach ($errorCodes as $errorCode => $description) {
            $fileData = $this->createTestFileData();
            $fileData['error'] = $errorCode;
            $uploadFile = new UploadFile($fileData);
            
            $this->assertEquals($errorCode, $uploadFile->getError());
        }
    }
} 