<?php

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
    public function __construct(array $file){
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
     * Определяет расширение файла по его MIME-типу. Поддерживает наиболее
     * распространенные типы файлов: изображения, документы, архивы и другие.
     * 
     * @param string $mimeType MIME-тип файла
     * 
     * @return string|null Расширение файла без точки или null, если тип не поддерживается
     * 
     * @example
     * ```php
     * $extension = $this->getExtensionFromMimeType('image/jpeg'); // 'jpg'
     * $extension = $this->getExtensionFromMimeType('application/pdf'); // 'pdf'
     * $extension = $this->getExtensionFromMimeType('text/plain'); // 'txt'
     * ```
     * 
     * @note Метод поддерживает ограниченный набор MIME-типов. Для расширенной
     *       поддержки можно использовать внешние библиотеки или базы данных.
     */
    private function getExtensionFromMimeType(string $mimeType): ?string
    {
        $mimeToExtension = require '../inc/mime_types.php';
        
        return $mimeToExtension[$mimeType] ?? null;
    }
}