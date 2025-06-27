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
        $files = [];
        
        foreach ($_FILES as $file) {
            if(is_array($file['name'])) {
                foreach ($file as $key => $value) {
                    $data = [
                        'name' => $file['name'][$key],
                        'type' => $file['type'][$key],
                        'tmp_name' => $file['tmp_name'][$key],
                        'error' => $file['error'][$key],
                    ];
                    $files[$data['name']][$key] = new UploadFile($data);
                }
            }else{
                $files[$file['name']] = new UploadFile($file);
            }
        }
        
        $this->data = $files;
    }
}