<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use stdClass;

/**
 * Class UploadFile
 *
 * Класс-обёртка для работы с загруженным файлом из $_FILES.
 * Позволяет сохранять файл, получать его имя, размер, MIME-тип, расширение и статус загрузки.
 *
 * Пример использования:
 * <code>
 * use CloudCastle\HttpRequest\Http\Files;
 *
 * $files = Files::getInstance();
 * $file = $files->get('avatar');
 * if ($file instanceof UploadFile && $file->isUploaded()) {
 *     $path = $file->save('/tmp/uploads');
 *     echo 'Файл сохранён: ' . $path;
 * }
 * </code>
 *
 * @package CloudCastle\HttpRequest\Http
 * @property string $name Оригинальное имя файла
 * @property string $type MIME-тип файла
 * @property string $tmp_name Временный путь к файлу
 * @property int $size Размер файла в байтах
 * @property int $error Код ошибки загрузки
 */
final class UploadFile extends stdClass
{
    /**
     * Конструктор UploadFile. Заполняет объект данными файла.
     *
     * @param array $file Массив с данными файла (обычно из $_FILES)
     *
     * Пример внутреннего использования:
     * <code>
     * $upload = new UploadFile($_FILES['avatar']);
     * </code>
     */
    public function __construct(array $file)
    {
        foreach ($file as $key => $value) {
            $this->{$key} = $value;
        }
    }
    
    /**
     * Сохраняет загруженный файл в указанную директорию.
     * Если имя не указано, будет использовано оригинальное имя или сгенерировано уникальное.
     *
     * @param string $directory Путь к директории для сохранения
     * @param string|null $filename Новое имя файла (опционально)
     * @return string|false Путь к сохранённому файлу или false при ошибке
     *
     * Пример:
     * <code>
     * $file->save('/tmp/uploads', 'avatar.jpg');
     * </code>
     */
    public function save(string $directory, ?string $filename = null): string|false
    {
        if (!isset($this->tmp_name) || !is_uploaded_file($this->tmp_name)) {
            return false;
        }
        
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                return false;
            }
        }
        
        $name = $filename ?? ($this->name ?? uniqid('upload_', true));
        
        if (!pathinfo($name, PATHINFO_EXTENSION) && isset($this->type)) {
            $extension = $this->getExtensionFromMimeType($this->type);
            
            if ($extension) {
                $name .= '.' . $extension;
            }
        }
        
        $target = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;
        
        if (move_uploaded_file($this->tmp_name, $target)) {
            return $target;
        }
        
        return false;
    }
    
    /**
     * Получить расширение по MIME-типу (внутренний метод).
     *
     * @param string $mimeType MIME-тип файла
     * @return string|null Расширение файла или null, если не найдено
     *
     * Пример внутреннего использования:
     * <code>
     * $ext = $this->getExtensionFromMimeType('image/jpeg');
     * </code>
     */
    private function getExtensionFromMimeType(string $mimeType): ?string
    {
        $mimeToExtension = require __DIR__ . '/../inc/mime_types.php';
        
        return $mimeToExtension[$mimeType] ?? null;
    }
    
    /**
     * Проверяет, был ли файл успешно загружен через HTTP POST.
     *
     * @return bool true, если файл загружен корректно
     *
     * Пример:
     * <code>
     * if ($file->isUploaded()) { ... }
     * </code>
     */
    public function isUploaded(): bool
    {
        return is_uploaded_file($this->tmp_name);
    }
    
    /**
     * Получить оригинальное имя файла.
     *
     * @return string Оригинальное имя файла
     *
     * Пример:
     * <code>
     * $name = $file->getOriginalName();
     * </code>
     */
    public function getOriginalName(): string
    {
        return $this->name;
    }
    
    /**
     * Получить размер файла в байтах.
     *
     * @return int Размер файла
     *
     * Пример:
     * <code>
     * $size = $file->getSize();
     * </code>
     */
    public function getSize(): int
    {
        return $this->size;
    }
    
    /**
     * Получить код ошибки загрузки файла.
     *
     * @return int Код ошибки
     *
     * Пример:
     * <code>
     * $error = $file->getError();
     * </code>
     */
    public function getError(): int
    {
        return $this->error;
    }
    
    /**
     * Получить MIME-тип файла.
     *
     * @return string MIME-тип
     *
     * Пример:
     * <code>
     * $mime = $file->getMimeType();
     * </code>
     */
    public function getMimeType(): string
    {
        return $this->type;
    }
    
    /**
     * Получить расширение файла.
     *
     * @return string Расширение файла
     *
     * Пример:
     * <code>
     * $ext = $file->getExtension();
     * </code>
     */
    public function getExtension(): string
    {
        return $this->getExtensionFromMimeType($this->type) ?? '';
    }
}