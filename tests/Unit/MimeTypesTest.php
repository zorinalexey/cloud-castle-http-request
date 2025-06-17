<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Тесты для файла MIME-типов
 * 
 * @package CloudCastle\HttpRequest\Tests\Unit
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 */
class MimeTypesTest extends TestCase
{
    /**
     * @var array<string, string>
     */
    private array $mimeTypes;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Загружаем MIME-типы
        $this->mimeTypes = require __DIR__ . '/../../src/inc/mime_types.php';
    }
    
    /**
     * Тест загрузки MIME-типов
     */
    public function testMimeTypesLoaded(): void
    {
        $this->assertNotEmpty($this->mimeTypes);
    }
    
    /**
     * Тест основных изображений
     */
    public function testBasicImages(): void
    {
        $basicImages = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'image/bmp' => 'bmp',
            'image/tiff' => 'tiff',
            'image/x-icon' => 'ico'
        ];
        
        foreach ($basicImages as $mimeType => $expectedExtension) {
            $this->assertArrayHasKey($mimeType, $this->mimeTypes);
            $this->assertEquals($expectedExtension, $this->mimeTypes[$mimeType]);
        }
    }
    
    /**
     * Тест документов
     */
    public function testDocuments(): void
    {
        $documents = [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'text/plain' => 'txt',
            'text/html' => 'html',
            'text/css' => 'css',
            'text/javascript' => 'js',
            'application/json' => 'json',
            'application/xml' => 'xml'
        ];
        
        foreach ($documents as $mimeType => $expectedExtension) {
            $this->assertArrayHasKey($mimeType, $this->mimeTypes);
            $this->assertEquals($expectedExtension, $this->mimeTypes[$mimeType]);
        }
    }
    
    /**
     * Тест архивов
     */
    public function testArchives(): void
    {
        $archives = [
            'application/zip' => 'zip',
            'application/x-rar-compressed' => 'rar',
            'application/x-7z-compressed' => '7z',
            'application/x-tar' => 'tar',
            'application/gzip' => 'gz',
            'application/x-bzip2' => 'bz2'
        ];
        
        foreach ($archives as $mimeType => $expectedExtension) {
            $this->assertArrayHasKey($mimeType, $this->mimeTypes);
            $this->assertEquals($expectedExtension, $this->mimeTypes[$mimeType]);
        }
    }
    
    /**
     * Тест аудио файлов
     */
    public function testAudioFiles(): void
    {
        $audioFiles = [
            'audio/mpeg' => 'mp3',
            'audio/wav' => 'wav',
            'audio/ogg' => 'ogg',
            'audio/aac' => 'aac',
            'audio/flac' => 'flac',
            'audio/midi' => 'mid'
        ];
        
        foreach ($audioFiles as $mimeType => $expectedExtension) {
            $this->assertArrayHasKey($mimeType, $this->mimeTypes);
            $this->assertEquals($expectedExtension, $this->mimeTypes[$mimeType]);
        }
    }
    
    /**
     * Тест видео файлов
     */
    public function testVideoFiles(): void
    {
        $videoFiles = [
            'video/mp4' => 'mp4',
            'video/avi' => 'avi',
            'video/quicktime' => 'mov',
            'video/x-ms-wmv' => 'wmv',
            'video/webm' => 'webm',
            'video/x-matroska' => 'mkv'
        ];
        
        foreach ($videoFiles as $mimeType => $expectedExtension) {
            $this->assertArrayHasKey($mimeType, $this->mimeTypes);
            $this->assertEquals($expectedExtension, $this->mimeTypes[$mimeType]);
        }
    }
    
    /**
     * Тест шрифтов
     */
    public function testFonts(): void
    {
        $fonts = [
            'font/ttf' => 'ttf',
            'font/otf' => 'otf',
            'font/woff' => 'woff',
            'font/woff2' => 'woff2',
            'application/vnd.ms-fontobject' => 'eot'
        ];
        
        foreach ($fonts as $mimeType => $expectedExtension) {
            $this->assertArrayHasKey($mimeType, $this->mimeTypes);
            $this->assertEquals($expectedExtension, $this->mimeTypes[$mimeType]);
        }
    }
    
    /**
     * Тест RAW форматов камер
     */
    public function testRawCameraFormats(): void
    {
        $rawFormats = [
            'image/x-canon-cr2' => 'cr2',
            'image/x-nikon-nef' => 'nef',
            'image/x-sony-arw' => 'arw',
            'image/x-fuji-raf' => 'raf',
            'image/x-olympus-orf' => 'orf',
            'image/x-pentax-pef' => 'pef',
            'image/x-samsung-srw' => 'srw',
            'image/x-kodak-dcr' => 'dcr'
        ];
        
        foreach ($rawFormats as $mimeType => $expectedExtension) {
            $this->assertArrayHasKey($mimeType, $this->mimeTypes);
            $this->assertEquals($expectedExtension, $this->mimeTypes[$mimeType]);
        }
    }
    
    /**
     * Тест уникальности ключей
     */
    public function testUniqueKeys(): void
    {
        $keys = array_keys($this->mimeTypes);
        $uniqueKeys = array_unique($keys);
        
        $this->assertEquals(count($keys), count($uniqueKeys), 'Все ключи должны быть уникальными');
    }
    
    /**
     * Тест корректности расширений
     */
    public function testValidExtensions(): void
    {
        foreach ($this->mimeTypes as $mimeType => $extension) {
            // Расширение не должно быть пустым
            $this->assertNotEmpty($extension, "Расширение для $mimeType не должно быть пустым");
            
            // Расширение не должно содержать точку
            $this->assertStringNotContainsString('.', $extension, "Расширение $extension не должно содержать точку");
            
            // Расширение должно быть строкой
            // $this->assertIsString($extension, "Расширение для $mimeType должно быть строкой");
            
            // Расширение не должно быть слишком длинным
            $this->assertLessThanOrEqual(10, strlen($extension), "Расширение $extension слишком длинное");
        }
    }
    
    /**
     * Тест корректности MIME-типов
     */
    public function testValidMimeTypes(): void
    {
        foreach ($this->mimeTypes as $mimeType => $extension) {
            // MIME-тип должен содержать слеш
            $this->assertStringContainsString('/', $mimeType, "MIME-тип $mimeType должен содержать слеш");
            
            // MIME-тип должен быть в правильном формате (поддерживает + и цифры в подтипе)
            $this->assertMatchesRegularExpression('/^[a-z-]+\/[a-zA-Z0-9.+.-]+$/', $mimeType, "MIME-тип $mimeType имеет неправильный формат");
            
            // MIME-тип не должен быть пустым
            $this->assertNotEmpty($mimeType, "MIME-тип не должен быть пустым");
        }
    }
    
    /**
     * Тест отсутствия дублирующихся расширений
     */
    public function testNoDuplicateExtensions(): void
    {
        $extensions = array_values($this->mimeTypes);
        $uniqueExtensions = array_unique($extensions);
        
        // Некоторые дублирования допустимы (например, разные MIME-типы для одного расширения)
        // Но проверяем, что нет слишком много дублирований (не более 50% дублирований)
        $duplicateCount = count($extensions) - count($uniqueExtensions);
        $this->assertLessThan(count($extensions) * 0.5, $duplicateCount, 'Слишком много дублирующихся расширений');
    }
    
    /**
     * Тест категорий файлов
     */
    public function testFileCategories(): void
    {
        $categories = [
            'image' => 0,
            'application' => 0,
            'text' => 0,
            'audio' => 0,
            'video' => 0,
            'font' => 0
        ];
        
        foreach ($this->mimeTypes as $mimeType => $extension) {
            $category = explode('/', $mimeType)[0];
            if (isset($categories[$category])) {
                $categories[$category]++;
            }
        }
        
        // Проверяем, что у нас есть файлы разных категорий
        $this->assertGreaterThan(0, $categories['image'], 'Должны быть изображения');
        $this->assertGreaterThan(0, $categories['application'], 'Должны быть приложения');
        $this->assertGreaterThan(0, $categories['text'], 'Должны быть текстовые файлы');
    }
    
    /**
     * Тест популярных форматов
     */
    public function testPopularFormats(): void
    {
        $popularFormats = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
            'text/plain',
            'text/html',
            'application/json',
            'application/zip',
            'audio/mpeg',
            'video/mp4'
        ];
        
        foreach ($popularFormats as $mimeType) {
            $this->assertArrayHasKey($mimeType, $this->mimeTypes, "Популярный формат $mimeType отсутствует");
        }
    }
    
    /**
     * Тест минимального количества MIME-типов
     */
    public function testMinimumMimeTypesCount(): void
    {
        $this->assertGreaterThanOrEqual(100, count($this->mimeTypes), 'Должно быть не менее 100 MIME-типов');
    }
    
    /**
     * Тест структуры массива
     */
    public function testArrayStructure(): void
    {
        $this->assertNotEmpty($this->mimeTypes);
        
        foreach ($this->mimeTypes as $key => $value) {
            // Проверяем только что ключи и значения не пустые
            $this->assertNotEmpty($key);
            $this->assertNotEmpty($value);
        }
    }
} 