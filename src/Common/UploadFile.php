<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Common;

use Exception;
use stdClass;

/**
 * Класс для работы с загруженными файлами
 * 
 * Этот класс представляет загруженный файл из HTTP-запроса и предоставляет
 * удобные методы для работы с ним. Наследует stdClass для поддержки
 * динамических свойств, что позволяет обращаться к данным файла как к
 * свойствам объекта.
 * 
 * Класс автоматически создается в классе Files при обработке данных из
 * глобального массива $_FILES и инкапсулирует всю логику работы с файлом.
 * 
 * Основные возможности:
 * - Доступ к данным файла через свойства объекта
 * - Сохранение файла в указанную директорию
 * - Проверка валидности загруженного файла
 * - Безопасная обработка файловых операций
 * - Поддержка переименования файлов при сохранении
 * 
 * @package CloudCastle\HttpRequest\Common
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 * @since 1.0.0
 * 
 * @example
 * ```php
 * // Получение файла через класс Files
 * $files = Files::getInstance();
 * $uploadedFiles = $files->avatar; // Массив UploadFile объектов
 * 
 * foreach ($uploadedFiles as $file) {
 *     // Доступ к данным файла
 *     $fileName = $file->name;        // Оригинальное имя файла
 *     $fileSize = $file->size;        // Размер в байтах
 *     $fileType = $file->type;        // MIME-тип
 *     $tmpName = $file->tmp_name;     // Временное имя
 *     $error = $file->error;          // Код ошибки
 *     
 *     // Сохранение файла
 *     $savedPath = $file->save('/var/www/uploads', 'new_name.jpg');
 *     if ($savedPath !== false) {
 *         echo "Файл сохранен: $savedPath";
 *     }
 * }
 * 
 * // Проверка ошибок загрузки
 * if ($file->error === UPLOAD_ERR_OK) {
 *     // Файл загружен успешно
 * } elseif ($file->error === UPLOAD_ERR_INI_SIZE) {
 *     // Файл превышает максимальный размер
 * }
 * ```
 * 
 * @see Files
 * @see stdClass
 * 
 * @property-read string $name Оригинальное имя файла на компьютере пользователя
 * @property-read string $type MIME-тип файла, предоставленный браузером
 * @property-read int $size Размер файла в байтах
 * @property-read string $tmp_name Временное имя файла на сервере
 * @property-read int $error Код ошибки загрузки (0 = успех)
 */
final class UploadFile extends stdClass
{
    /**
     * Конструктор класса UploadFile
     * 
     * Создает объект UploadFile из массива данных файла, полученного из
     * глобального массива $_FILES. Все данные файла становятся доступными
     * как свойства объекта для удобного доступа.
     * 
     * @param array<string, mixed> $file Массив данных файла из $_FILES
     * 
     * @example
     * ```php
     * // Данные файла из $_FILES
     * $fileData = [
     *     'name' => 'photo.jpg',
     *     'type' => 'image/jpeg',
     *     'size' => 1024000,
     *     'tmp_name' => '/tmp/php7F.tmp',
     *     'error' => 0
     * ];
     * 
     * $uploadFile = new UploadFile($fileData);
     * 
     * // Доступ к данным через свойства
     * echo $uploadFile->name;     // 'photo.jpg'
     * echo $uploadFile->size;     // 1024000
     * echo $uploadFile->type;     // 'image/jpeg'
     * ```
     * 
     * @note Конструктор автоматически вызывается в классе Files при обработке
     *       данных из $_FILES. Ручное создание экземпляров обычно не требуется.
     * 
     * @see Files::setFile()
     */
    public function __construct(array $file)
    {
        foreach ($file as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Сохранить загруженный файл в указанную директорию
     *
     * Перемещает временный файл, загруженный через HTTP, в указанную директорию.
     * Метод выполняет проверки безопасности и создает директорию при необходимости.
     * Можно указать новое имя файла. Если имя не указано, используется оригинальное имя.
     * Автоматически добавляет расширение файла на основе MIME-типа, если оно отсутствует.
     * 
     * Безопасность:
     * - Проверяет, что файл действительно был загружен через HTTP
     * - Использует move_uploaded_file() для безопасного перемещения
     * - Создает директорию с безопасными правами доступа
     * 
     * @param string $directory Путь к директории для сохранения файла (без завершающего слеша)
     * @param string|null $filename Новое имя файла (если не указано — используется оригинальное имя)
     * 
     * @return string|false Полный путь к сохранённому файлу или false при ошибке
     * 
     * @throws Exception При ошибках файловой системы (если включены исключения)
     * 
     * @example
     * ```php
     * // Сохранение с оригинальным именем
     * $result = $file->save('/var/www/uploads');
     * if ($result !== false) {
     *     echo "Файл сохранен: $result";
     * }
     * 
     * // Сохранение с новым именем
     * $result = $file->save('/var/www/uploads', 'avatar_' . time() . '.jpg');
     * 
     * // Сохранение в поддиректорию (создается автоматически)
     * $result = $file->save('/var/www/uploads/users/123', 'profile.jpg');
     * 
     * // Обработка ошибок
     * $result = $file->save('/var/www/uploads');
     * if ($result === false) {
     *     echo "Ошибка сохранения файла";
     * }
     * ```
     * 
     * @note Метод автоматически создает директорию, если она не существует.
     *       Права доступа устанавливаются как 0777 (полный доступ).
     *       Расширение файла добавляется автоматически на основе MIME-типа.
     * 
     * @see UploadFile::__construct()
     * @see move_uploaded_file()
     * @see is_uploaded_file()
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
        
        // Добавляем расширение, если его нет
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
     * Получить расширение файла на основе MIME-типа
     * 
     * Этот метод определяет расширение файла на основе его MIME-типа,
     * используя предопределенную карту соответствий MIME-типов и расширений.
     * Если MIME-тип не найден в карте, возвращает null.
     *
     * @param string $mimeType MIME-тип файла
     *
     * @return string|null Расширение файла или null, если MIME-тип не найден
     * 
     * @example
     * ```php
     * $file = new UploadFile($_FILES['document']);
     * 
     * $mimeType = $file->getMimeType();
     * $extension = $file->getExtensionFromMimeType($mimeType);
     * 
     * if ($extension) {
     *     echo "Расширение файла: .$extension";
     * } else {
     *     echo "Неизвестный MIME-тип: $mimeType";
     * }
     * 
     * // Примеры соответствий:
     * // 'image/jpeg' -> 'jpg'
     * // 'application/pdf' -> 'pdf'
     * // 'text/plain' -> 'txt'
     * ```
     * 
     * @see UploadFile::getMimeType()
     * @see UploadFile::getExtension()
     */
    private function getExtensionFromMimeType(string $mimeType): ?string
    {
        $mimeToExtension = require __DIR__ . '/../inc/mime_types.php';
        
        return $mimeToExtension[$mimeType] ?? null;
    }
    
    /**
     * Проверить, был ли файл загружен через HTTP POST
     * 
     * Этот метод проверяет, что файл действительно был загружен через
     * HTTP POST запрос, а не создан локально на сервере. Это важная
     * проверка безопасности для предотвращения атак через загрузку файлов.
     * 
     * @return bool true, если файл был загружен через HTTP POST, false в противном случае
     * 
     * @example
     * ```php
     * $file = new UploadFile($_FILES['document']);
     * 
     * if ($file->isUploaded()) {
     *     // Файл безопасно загружен через HTTP
     *     $result = $file->save('/uploads/');
     * } else {
     *     // Файл не был загружен через HTTP - потенциальная угроза безопасности
     *     echo "Ошибка: файл не был загружен через HTTP";
     * }
     * 
     * // Проверка перед сохранением
     * if ($file->isUploaded() && $file->getError() === UPLOAD_ERR_OK) {
     *     $file->save('/uploads/');
     * }
     * ```
     * 
     * @note Этот метод использует PHP функцию is_uploaded_file() для проверки.
     *       Всегда проверяйте результат этого метода перед сохранением файла.
     * 
     * @see is_uploaded_file()
     * @see UploadFile::save()
     * @see UploadFile::getError()
     */
    public function isUploaded(): bool
    {
        return is_uploaded_file($this->tmp_name);
    }
    
    /**
     * Получить оригинальное имя файла
     * 
     * Возвращает оригинальное имя файла, которое было на компьютере
     * пользователя до загрузки. Это имя может содержать путь и
     * специальные символы, поэтому его следует использовать с осторожностью.
     * 
     * @return string Оригинальное имя файла
     * 
     * @example
     * ```php
     * $file = new UploadFile($_FILES['document']);
     * 
     * $originalName = $file->getOriginalName();
     * echo "Загружен файл: $originalName";
     * 
     * // Примеры имен файлов:
     * // "photo.jpg"
     * // "My Document.pdf"
     * // "C:\Users\John\Desktop\image.png" (Windows)
     * // "/home/user/documents/file.txt" (Unix)
     * 
     * // Безопасное использование имени файла
     * $safeName = basename($file->getOriginalName());
     * $extension = pathinfo($safeName, PATHINFO_EXTENSION);
     * ```
     * 
     * @note Оригинальное имя может содержать путь к файлу и специальные
     *       символы. Для безопасного использования рекомендуется применять
     *       basename() и pathinfo() для извлечения только имени файла.
     * 
     * @see basename()
     * @see pathinfo()
     * @see UploadFile::save()
     */
    public function getOriginalName(): string
    {
        return $this->name;
    }
    
    /**
     * Получить размер файла в байтах
     * 
     * Возвращает размер загруженного файла в байтах. Это значение
     * предоставляется браузером и может быть использовано для проверки
     * ограничений размера файла.
     * 
     * @return int Размер файла в байтах
     * 
     * @example
     * ```php
     * $file = new UploadFile($_FILES['document']);
     * 
     * $size = $file->getSize();
     * echo "Размер файла: $size байт";
     * 
     * // Проверка ограничений размера
     * $maxSize = 5 * 1024 * 1024; // 5MB
     * if ($file->getSize() > $maxSize) {
     *     echo "Файл слишком большой";
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
     * echo "Размер: " . formatFileSize($file->getSize());
     * ```
     * 
     * @note Размер файла предоставляется браузером и может быть неточным
     *       в некоторых случаях. Для точной проверки рекомендуется
     *       использовать filesize() после сохранения файла.
     * 
     * @see filesize()
     * @see UploadFile::save()
     */
    public function getSize(): int
    {
        return $this->size;
    }
    
    /**
     * Получить код ошибки загрузки файла
     * 
     * Возвращает код ошибки, который указывает на результат попытки
     * загрузки файла. Код 0 означает успешную загрузку, другие коды
     * указывают на различные типы ошибок.
     * 
     * @return int Код ошибки загрузки
     * 
     * @example
     * ```php
     * $file = new UploadFile($_FILES['document']);
     * 
     * switch ($file->getError()) {
     *     case UPLOAD_ERR_OK:
     *         echo "Файл загружен успешно";
     *         break;
     *         
     *     case UPLOAD_ERR_INI_SIZE:
     *         echo "Файл превышает максимальный размер, указанный в php.ini";
     *         break;
     *         
     *     case UPLOAD_ERR_FORM_SIZE:
     *         echo "Файл превышает максимальный размер, указанный в форме";
     *         break;
     *         
     *     case UPLOAD_ERR_PARTIAL:
     *         echo "Файл был загружен только частично";
     *         break;
     *         
     *     case UPLOAD_ERR_NO_FILE:
     *         echo "Файл не был загружен";
     *         break;
     *         
     *     case UPLOAD_ERR_NO_TMP_DIR:
     *         echo "Отсутствует временная папка";
     *         break;
     *         
     *     case UPLOAD_ERR_CANT_WRITE:
     *         echo "Не удалось записать файл на диск";
     *         break;
     *         
     *     case UPLOAD_ERR_EXTENSION:
     *         echo "Загрузка файла была остановлена расширением PHP";
     *         break;
     *         
     *     default:
     *         echo "Неизвестная ошибка загрузки";
     * }
     * 
     * // Простая проверка успешности загрузки
     * if ($file->getError() === UPLOAD_ERR_OK) {
     *     $file->save('/uploads/');
     * }
     * ```
     * 
     * @note Всегда проверяйте код ошибки перед обработкой файла.
     *       Даже если файл присутствует в $_FILES, он может содержать ошибки.
     * 
     * @see UPLOAD_ERR_OK
     * @see UPLOAD_ERR_INI_SIZE
     * @see UPLOAD_ERR_FORM_SIZE
     * @see UPLOAD_ERR_PARTIAL
     * @see UPLOAD_ERR_NO_FILE
     * @see UPLOAD_ERR_NO_TMP_DIR
     * @see UPLOAD_ERR_CANT_WRITE
     * @see UPLOAD_ERR_EXTENSION
     * @see UploadFile::isUploaded()
     */
    public function getError(): int
    {
        return $this->error;
    }
    
    /**
     * Получить MIME-тип файла
     * 
     * Возвращает MIME-тип файла, предоставленный браузером. MIME-тип
     * указывает на тип содержимого файла и может быть использован для
     * валидации и определения расширения файла.
     * 
     * @return string MIME-тип файла
     * 
     * @example
     * ```php
     * $file = new UploadFile($_FILES['document']);
     * 
     * $mimeType = $file->getMimeType();
     * echo "Тип файла: $mimeType";
     * 
     * // Проверка допустимых типов файлов
     * $allowedTypes = [
     *     'image/jpeg',
     *     'image/png',
     *     'image/gif',
     *     'application/pdf',
     *     'text/plain'
     * ];
     * 
     * if (in_array($file->getMimeType(), $allowedTypes)) {
     *     echo "Тип файла разрешен";
     *     $file->save('/uploads/');
     * } else {
     *     echo "Недопустимый тип файла";
     * }
     * 
     * // Определение категории файла
     * $mimeType = $file->getMimeType();
     * if (str_starts_with($mimeType, 'image/')) {
     *     echo "Это изображение";
     * } elseif (str_starts_with($mimeType, 'video/')) {
     *     echo "Это видео";
     * } elseif (str_starts_with($mimeType, 'audio/')) {
     *     echo "Это аудио";
     * }
     * ```
     * 
     * @note MIME-тип предоставляется браузером и может быть подделан.
     *       Для надежной валидации рекомендуется дополнительно проверять
     *       содержимое файла или использовать функции типа finfo_file().
     * 
     * @see finfo_file()
     * @see UploadFile::getExtension()
     * @see UploadFile::save()
     */
    public function getMimeType(): string
    {
        return $this->type;
    }
    
    /**
     * Получить расширение файла на основе MIME-типа
     * 
     * Определяет расширение файла по его MIME-типу и возвращает его
     * без точки. Если MIME-тип не поддерживается, возвращает пустую строку.
     * 
     * @return string Расширение файла без точки или пустая строка, если тип не поддерживается
     * 
     * @example
     * ```php
     * $file = new UploadFile($_FILES['document']);
     * 
     * $extension = $file->getExtension();
     * echo "Расширение файла: $extension";
     * 
     * // Примеры возвращаемых значений:
     * // $file->getMimeType() = 'image/jpeg' → $file->getExtension() = 'jpg'
     * // $file->getMimeType() = 'application/pdf' → $file->getExtension() = 'pdf'
     * // $file->getMimeType() = 'text/plain' → $file->getExtension() = 'txt'
     * // $file->getMimeType() = 'unknown/type' → $file->getExtension() = ''
     * 
     * // Использование для создания безопасного имени файла
     * $originalName = $file->getOriginalName();
     * $extension = $file->getExtension();
     * 
     * if ($extension) {
     *     $safeName = uniqid() . '.' . $extension;
     * } else {
     *     // Если расширение не определено, используем оригинальное
     *     $safeName = uniqid() . '_' . basename($originalName);
     * }
     * 
     * $file->save('/uploads/', $safeName);
     * ```
     * 
     * @note Метод использует внутренний список MIME-типов для определения
     *       расширения. Если MIME-тип не найден в списке, возвращается
     *       пустая строка.
     * 
     * @see UploadFile::getMimeType()
     * @see UploadFile::getExtensionFromMimeType()
     * @see UploadFile::save()
     */
    public function getExtension(): string
    {
        return $this->getExtensionFromMimeType($this->type) ?? '';
    }
}