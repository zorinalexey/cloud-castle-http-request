<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;

/**
 * Class Files
 *
 * Класс-обёртка для доступа к загруженным файлам ($_FILES) через удобные методы и магические свойства.
 * Реализует паттерн Singleton и предоставляет методы для получения файлов по имени, всех файлов и магический геттер.
 * Каждый файл представлен объектом UploadFile.
 *
 * Пример использования:
 * <code>
 * use CloudCastle\HttpRequest\Http\Files;
 *
 * $files = Files::getInstance();
 * $file = $files->get('avatar');
 * if ($file instanceof UploadFile) {
 *     $file->save('/tmp/uploads');
 * }
 * $allFiles = $files->all();
 * </code>
 *
 * @package CloudCastle\HttpRequest\Http
 */
final class Files
{
    /**
     * @var array<string, UploadFile|array<int, UploadFile>>
     */
    protected array $data = [];
    use GetDataTrait, GetInstanceTrait;
    
    /**
     * Конструктор Files. Заполняет коллекцию объектами UploadFile из $_FILES.
     * Вызывается только внутри класса (Singleton).
     *
     * Пример внутреннего использования:
     * <code>
     * $files = new self();
     * </code>
     */
    private function __construct()
    {
        /** @var array<string, UploadFile|array<int, UploadFile>> $files */
        $files = [];
        
        foreach ($_FILES as $fieldName => $file) {
            if(is_array($file['name'])) {
                // Множественные файлы
                $fileCount = count($file['name']);
                for ($i = 0; $i < $fileCount; $i++) {
                    $data = [
                        'name' => $file['name'][$i],
                        'type' => $file['type'][$i],
                        'tmp_name' => $file['tmp_name'][$i],
                        'error' => $file['error'][$i],
                        'size' => $file['size'][$i] ?? 0
                    ];
                    $files[$data['name']] = new UploadFile($data);
                }
            } else {
                // Одиночный файл
                $files[$file['name']] = new UploadFile($file);
            }
        }
        
        $this->data = $files;
    }
}