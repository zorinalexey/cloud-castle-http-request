<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use stdClass;

/**
 * Class UploadFile
 * 
 * Управляет загруженным файлом из HTTP запроса. Предоставляет удобный интерфейс для работы
 * с файлами из массива $_FILES. Позволяет сохранять файлы, получать информацию о них
 * и проверять статус загрузки.
 * 
 * Класс автоматически обрабатывает данные файла, предоставляет методы для безопасного
 * сохранения, получения метаданных и валидации загруженных файлов. Поддерживает
 * автоматическое определение расширения файла по MIME-типу.
 * 
 * @package CloudCastle\HttpRequest\Http
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * @property string $name Оригинальное имя файла
 * @property string $type MIME-тип файла
 * @property string $tmp_name Временный путь к файлу
 * @property int $size Размер файла в байтах
 * @property int $error Код ошибки загрузки
 * 
 * @example
 * ```php
 * use CloudCastle\HttpRequest\Http\Files;
 * 
 * // Получение файла через класс Files
 * $files = Files::getInstance();
 * $file = $files->get('avatar');
 * 
 * if ($file instanceof UploadFile && $file->isUploaded()) {
 *     // Получение информации о файле
 *     echo 'Имя файла: ' . $file->getOriginalName();
 *     echo 'Размер: ' . $file->getSize() . ' байт';
 *     echo 'Тип: ' . $file->getMimeType();
 *     echo 'Расширение: ' . $file->getExtension();
 *     
 *     // Сохранение файла
 *     $path = $file->save('/uploads/avatars/', 'user_avatar.jpg');
 *     if ($path) {
 *         echo 'Файл сохранён: ' . $path;
 *     }
 * }
 * 
 * // Прямое создание объекта
 * $uploadFile = new UploadFile($_FILES['document']);
 * if ($uploadFile->isUploaded()) {
 *     $uploadFile->save('/uploads/documents/');
 * }
 * 
 * // Проверка ошибок загрузки
 * if ($file->getError() !== UPLOAD_ERR_OK) {
 *     echo 'Ошибка загрузки: ' . $file->getError();
 * }
 * ```
 */
final class UploadFile extends stdClass
{
    /**
     * Конструктор UploadFile
     * 
     * Инициализирует объект UploadFile, заполняя его данными из массива файла.
     * Обычно используется с данными из $_FILES, но может работать с любым
     * массивом, содержащим информацию о файле.
     * 
     * @param array<string, mixed> $file Массив с данными файла (обычно из $_FILES)
     * @since 1.0.0
     * 
     * @example
     * ```php
     * // Создание из $_FILES
     * $uploadFile = new UploadFile($_FILES['avatar']);
     * 
     * // Создание из произвольного массива
     * $fileData = [
     *     'name' => 'document.pdf',
     *     'type' => 'application/pdf',
     *     'tmp_name' => '/tmp/phpABC123',
     *     'error' => 0,
     *     'size' => 1024000
     * ];
     * $uploadFile = new UploadFile($fileData);
     * 
     * // Структура данных файла:
     * // $file = [
     * //   'name' => 'original_filename.jpg', // Оригинальное имя файла
     * //   'type' => 'image/jpeg', // MIME-тип
     * //   'tmp_name' => '/tmp/phpABC123', // Временный путь
     * //   'error' => 0, // Код ошибки (0 = успех)
     * //   'size' => 1024000 // Размер в байтах
     * // ];
     * 
     * // После создания объект содержит свойства:
     * // $uploadFile->name = 'original_filename.jpg'
     * // $uploadFile->type = 'image/jpeg'
     * // $uploadFile->tmp_name = '/tmp/phpABC123'
     * // $uploadFile->error = 0
     * // $uploadFile->size = 1024000
     * ```
     */
    public function __construct(array $file)
    {
        foreach ($file as $key => $value) {
            $this->{$key} = $value;
        }
    }
    
    /**
     * Сохраняет загруженный файл в указанную директорию
     * 
     * Безопасно перемещает загруженный файл из временной директории в указанную
     * папку. Автоматически создает директорию, если она не существует.
     * Поддерживает переименование файла и автоматическое определение расширения.
     * 
     * @param string $directory Путь к директории для сохранения
     * @param string|null $filename Новое имя файла (опционально)
     * @return string|false Путь к сохранённому файлу или false при ошибке
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $file = new UploadFile($_FILES['avatar']);
     * 
     * // Сохранение с оригинальным именем
     * $path = $file->save('/uploads/avatars/');
     * // Результат: '/uploads/avatars/original_filename.jpg'
     * 
     * // Сохранение с новым именем
     * $path = $file->save('/uploads/avatars/', 'user_123_avatar.jpg');
     * // Результат: '/uploads/avatars/user_123_avatar.jpg'
     * 
     * // Сохранение с автоматическим расширением
     * $path = $file->save('/uploads/documents/', 'report');
     * // Если MIME-тип 'application/pdf', результат: '/uploads/documents/report.pdf'
     * 
     * // Сохранение с уникальным именем
     * $path = $file->save('/uploads/temp/');
     * // Результат: '/uploads/temp/upload_64f8a1b2c3d4e.jpg'
     * 
     * // Обработка ошибок
     * if ($path === false) {
     *     echo 'Ошибка сохранения файла';
     * } else {
     *     echo 'Файл сохранён: ' . $path;
     * }
     * 
     * // Создание директории автоматически
     * $file->save('/uploads/new_folder/subfolder/', 'file.jpg');
     * // Создаст все необходимые директории
     * 
     * // Практические примеры:
     * 
     * // Сохранение аватара пользователя
     * $avatar = new UploadFile($_FILES['avatar']);
     * $userId = 123;
     * $path = $avatar->save('/uploads/avatars/', "user_{$userId}_avatar.jpg");
     * 
     * // Сохранение документов с датой
     * $document = new UploadFile($_FILES['document']);
     * $date = date('Y-m-d');
     * $path = $document->save('/uploads/documents/', "doc_{$date}.pdf");
     * 
     * // Сохранение изображений с префиксом
     * $image = new UploadFile($_FILES['image']);
     * $prefix = 'gallery_';
     * $path = $image->save('/uploads/gallery/', $prefix . uniqid());
     * 
     * // Сохранение во временную папку
     * $tempFile = new UploadFile($_FILES['temp_file']);
     * $path = $tempFile->save('/tmp/uploads/');
     * // Используется оригинальное имя или генерируется уникальное
     * ```
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
     * Получает расширение файла по MIME-типу
     * 
     * Внутренний метод для определения расширения файла на основе его MIME-типа.
     * Использует внешний файл с маппингом MIME-типов к расширениям.
     * 
     * @param string $mimeType MIME-тип файла
     * @return string|null Расширение файла или null, если не найдено
     * @since 1.0.0
     * @internal Метод предназначен для внутреннего использования
     * 
     * @example
     * ```php
     * // Внутреннее использование
     * $extension = $this->getExtensionFromMimeType('image/jpeg'); // 'jpg'
     * $extension = $this->getExtensionFromMimeType('application/pdf'); // 'pdf'
     * $extension = $this->getExtensionFromMimeType('text/plain'); // 'txt'
     * $extension = $this->getExtensionFromMimeType('unknown/type'); // null
     * 
     * // Примеры MIME-типов и их расширений:
     * // 'image/jpeg' => 'jpg'
     * // 'image/png' => 'png'
     * // 'image/gif' => 'gif'
     * // 'application/pdf' => 'pdf'
     * // 'application/msword' => 'doc'
     * // 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
     * // 'text/plain' => 'txt'
     * // 'text/html' => 'html'
     * // 'application/json' => 'json'
     * // 'application/xml' => 'xml'
     * ```
     */
    protected function getExtensionFromMimeType(string $mimeType): ?string
    {
        $mimeToExtension = require __DIR__ . '/../inc/mime_types.php';
        
        return $mimeToExtension[$mimeType] ?? null;
    }
    
    /**
     * Проверяет, был ли файл успешно загружен через HTTP POST
     * 
     * Использует PHP функцию is_uploaded_file() для проверки безопасности.
     * Гарантирует, что файл действительно был загружен через HTTP POST,
     * а не создан локально или подделан.
     * 
     * @return bool true, если файл загружен корректно
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $file = new UploadFile($_FILES['avatar']);
     * 
     * // Проверка перед сохранением
     * if ($file->isUploaded()) {
     *     $path = $file->save('/uploads/');
     *     echo 'Файл успешно сохранён';
     * } else {
     *     echo 'Файл не был загружен через HTTP POST';
     * }
     * 
     * // Проверка в цикле обработки файлов
     * $files = Files::getInstance();
     * foreach ($files->all() as $file) {
     *     if ($file instanceof UploadFile && $file->isUploaded()) {
     *         // Безопасная обработка файла
     *         $file->save('/uploads/');
     *     }
     * }
     * 
     * // Проверка перед валидацией
     * if (!$file->isUploaded()) {
     *     throw new Exception('Файл не был загружен корректно');
     * }
     * 
     * // Проверка в API endpoint
     * public function uploadAvatar() {
     *     $file = new UploadFile($_FILES['avatar']);
     *     
     *     if (!$file->isUploaded()) {
     *         return ['error' => 'Invalid file upload'];
     *     }
     *     
     *     $path = $file->save('/uploads/avatars/');
     *     return ['success' => true, 'path' => $path];
     * }
     * ```
     */
    public function isUploaded(): bool
    {
        return is_uploaded_file($this->tmp_name);
    }
    
    /**
     * Получает оригинальное имя загруженного файла
     * 
     * Возвращает имя файла, которое было у файла на компьютере пользователя
     * до загрузки. Это имя может содержать путь и специальные символы.
     * 
     * @return string Оригинальное имя файла
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $file = new UploadFile($_FILES['document']);
     * 
     * // Получение имени файла
     * $originalName = $file->getOriginalName();
     * echo $originalName; // 'My Document.pdf'
     * 
     * // Использование для логирования
     * $logMessage = "Загружен файл: " . $file->getOriginalName();
     * 
     * // Использование для валидации
     * $allowedExtensions = ['jpg', 'png', 'gif'];
     * $extension = pathinfo($file->getOriginalName(), PATHINFO_EXTENSION);
     * if (!in_array(strtolower($extension), $allowedExtensions)) {
     *     echo 'Неподдерживаемый тип файла';
     * }
     * 
     * // Использование для создания безопасного имени
     * $originalName = $file->getOriginalName();
     * $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
     * $path = $file->save('/uploads/', $safeName);
     * 
     * // Примеры оригинальных имен:
     * // 'photo.jpg'
     * // 'My Document.pdf'
     * // 'image (1).png'
     * // 'C:\Users\John\Desktop\file.txt'
     * // '/home/user/documents/report.docx'
     * ```
     */
    public function getOriginalName(): string
    {
        return $this->name;
    }
    
    /**
     * Получает размер загруженного файла в байтах
     * 
     * Возвращает размер файла в байтах. Полезно для валидации размера файла
     * и отображения информации пользователю.
     * 
     * @return int Размер файла в байтах
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $file = new UploadFile($_FILES['document']);
     * 
     * // Получение размера
     * $size = $file->getSize();
     * echo "Размер файла: {$size} байт";
     * 
     * // Валидация размера файла
     * $maxSize = 10 * 1024 * 1024; // 10MB
     * if ($file->getSize() > $maxSize) {
     *     echo 'Файл слишком большой. Максимальный размер: 10MB';
     * }
     * 
     * // Форматирование размера для отображения
     * function formatFileSize(int $bytes): string {
     *     $units = ['B', 'KB', 'MB', 'GB'];
     *     $bytes = max($bytes, 0);
     *     $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
     *     $pow = min($pow, count($units) - 1);
     *     $bytes /= pow(1024, $pow);
     *     return round($bytes, 2) . ' ' . $units[$pow];
     * }
     * 
     * $formattedSize = formatFileSize($file->getSize());
     * echo "Размер файла: {$formattedSize}";
     * 
     * // Проверка минимального размера
     * $minSize = 1024; // 1KB
     * if ($file->getSize() < $minSize) {
     *     echo 'Файл слишком маленький';
     * }
     * 
     * // Примеры размеров:
     * // 1024 байт = 1KB
     * // 1048576 байт = 1MB
     * // 52428800 байт = 50MB
     * ```
     */
    public function getSize(): int
    {
        return $this->size;
    }
    
    /**
     * Получает код ошибки загрузки файла
     * 
     * Возвращает код ошибки PHP, связанный с загрузкой файла.
     * UPLOAD_ERR_OK (0) означает успешную загрузку.
     * 
     * @return int Код ошибки загрузки
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $file = new UploadFile($_FILES['document']);
     * 
     * // Проверка ошибок загрузки
     * $error = $file->getError();
     * 
     * switch ($error) {
     *     case UPLOAD_ERR_OK:
     *         echo 'Файл загружен успешно';
     *         break;
     *     case UPLOAD_ERR_INI_SIZE:
     *         echo 'Файл превышает upload_max_filesize в php.ini';
     *         break;
     *     case UPLOAD_ERR_FORM_SIZE:
     *         echo 'Файл превышает MAX_FILE_SIZE в HTML форме';
     *         break;
     *     case UPLOAD_ERR_PARTIAL:
     *         echo 'Файл был загружен частично';
     *         break;
     *     case UPLOAD_ERR_NO_FILE:
     *         echo 'Файл не был загружен';
     *         break;
     *     case UPLOAD_ERR_NO_TMP_DIR:
     *         echo 'Отсутствует временная папка';
     *         break;
     *     case UPLOAD_ERR_CANT_WRITE:
     *         echo 'Ошибка записи на диск';
     *         break;
     *     case UPLOAD_ERR_EXTENSION:
     *         echo 'Загрузка остановлена расширением PHP';
     *         break;
     *     default:
     *         echo 'Неизвестная ошибка загрузки';
     * }
     * 
     * // Простая проверка
     * if ($file->getError() !== UPLOAD_ERR_OK) {
     *     echo 'Ошибка загрузки файла';
     * }
     * 
     * // Проверка в API
     * public function handleUpload() {
     *     $file = new UploadFile($_FILES['file']);
     *     
     *     if ($file->getError() !== UPLOAD_ERR_OK) {
     *         return ['error' => 'Upload failed', 'code' => $file->getError()];
     *     }
     *     
     *     // Обработка успешной загрузки
     *     return ['success' => true];
     * }
     * 
     * // Коды ошибок PHP:
     * // UPLOAD_ERR_OK (0) - успешная загрузка
     * // UPLOAD_ERR_INI_SIZE (1) - превышен upload_max_filesize
     * // UPLOAD_ERR_FORM_SIZE (2) - превышен MAX_FILE_SIZE
     * // UPLOAD_ERR_PARTIAL (3) - частичная загрузка
     * // UPLOAD_ERR_NO_FILE (4) - файл не загружен
     * // UPLOAD_ERR_NO_TMP_DIR (6) - нет временной папки
     * // UPLOAD_ERR_CANT_WRITE (7) - ошибка записи
     * // UPLOAD_ERR_EXTENSION (8) - остановлено расширением
     * ```
     */
    public function getError(): int
    {
        return $this->error;
    }
    
    /**
     * Получает MIME-тип загруженного файла
     * 
     * Возвращает MIME-тип файла, определенный браузером или сервером.
     * Полезно для валидации типа файла и определения способа обработки.
     * 
     * @return string MIME-тип файла
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $file = new UploadFile($_FILES['document']);
     * 
     * // Получение MIME-типа
     * $mimeType = $file->getMimeType();
     * echo "Тип файла: {$mimeType}";
     * 
     * // Валидация типа файла
     * $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
     * if (!in_array($file->getMimeType(), $allowedTypes)) {
     *     echo 'Неподдерживаемый тип файла';
     * }
     * 
     * // Проверка типа изображения
     * if (strpos($file->getMimeType(), 'image/') === 0) {
     *     echo 'Это изображение';
     *     // Обработка изображения
     * }
     * 
     * // Проверка типа документа
     * $documentTypes = [
     *     'application/pdf',
     *     'application/msword',
     *     'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
     * ];
     * if (in_array($file->getMimeType(), $documentTypes)) {
     *     echo 'Это документ';
     *     // Обработка документа
     * }
     * 
     * // Создание обработчиков по типу
     * switch ($file->getMimeType()) {
     *     case 'image/jpeg':
     *     case 'image/png':
     *     case 'image/gif':
     *         // Обработка изображений
     *         $this->processImage($file);
     *         break;
     *     case 'application/pdf':
     *         // Обработка PDF
     *         $this->processPdf($file);
     *         break;
     *     case 'text/plain':
     *         // Обработка текстовых файлов
     *         $this->processText($file);
     *         break;
     *     default:
     *         echo 'Неподдерживаемый тип файла';
     * }
     * 
     * // Примеры MIME-типов:
     * // 'image/jpeg' - JPEG изображения
     * // 'image/png' - PNG изображения
     * // 'image/gif' - GIF изображения
     * // 'application/pdf' - PDF документы
     * // 'application/msword' - Word документы
     * // 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' - Word 2007+
     * // 'text/plain' - Текстовые файлы
     * // 'text/html' - HTML файлы
     * // 'application/json' - JSON файлы
     * // 'video/mp4' - MP4 видео
     * // 'audio/mpeg' - MP3 аудио
     * ```
     */
    public function getMimeType(): string
    {
        return $this->type;
    }
    
    /**
     * Получает расширение файла на основе MIME-типа
     * 
     * Возвращает расширение файла, определенное по его MIME-типу.
     * Использует внутренний маппинг MIME-типов к расширениям.
     * 
     * @return string Расширение файла или пустая строка, если не найдено
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $file = new UploadFile($_FILES['document']);
     * 
     * // Получение расширения
     * $extension = $file->getExtension();
     * echo "Расширение файла: {$extension}";
     * 
     * // Использование для валидации
     * $allowedExtensions = ['jpg', 'png', 'gif'];
     * if (!in_array($file->getExtension(), $allowedExtensions)) {
     *     echo 'Неподдерживаемое расширение файла';
     * }
     * 
     * // Создание имени файла с расширением
     * $baseName = 'user_avatar';
     * $extension = $file->getExtension();
     * $fileName = $extension ? "{$baseName}.{$extension}" : $baseName;
     * $path = $file->save('/uploads/', $fileName);
     * 
     * // Проверка типа файла по расширению
     * switch ($file->getExtension()) {
     *     case 'jpg':
     *     case 'jpeg':
     *     case 'png':
     *     case 'gif':
     *         echo 'Это изображение';
     *         break;
     *     case 'pdf':
     *         echo 'Это PDF документ';
     *         break;
     *     case 'doc':
     *     case 'docx':
     *         echo 'Это Word документ';
     *         break;
     *     case 'txt':
     *         echo 'Это текстовый файл';
     *         break;
     *     default:
     *         echo 'Неизвестный тип файла';
     * }
     * 
     * // Примеры расширений для различных MIME-типов:
     * // 'image/jpeg' => 'jpg'
     * // 'image/png' => 'png'
     * // 'image/gif' => 'gif'
     * // 'application/pdf' => 'pdf'
     * // 'application/msword' => 'doc'
     * // 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
     * // 'text/plain' => 'txt'
     * // 'text/html' => 'html'
     * // 'application/json' => 'json'
     * // 'application/xml' => 'xml'
     * // 'video/mp4' => 'mp4'
     * // 'audio/mpeg' => 'mp3'
     * ```
     */
    public function getExtension(): string
    {
        return $this->getExtensionFromMimeType($this->type) ?? '';
    }
}