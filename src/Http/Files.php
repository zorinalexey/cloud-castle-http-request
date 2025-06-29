<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;

/**
 * Class Files
 *
 * Управляет загруженными файлами HTTP запроса. Предоставляет удобный интерфейс для работы
 * с глобальным массивом $_FILES через методы и магические свойства. Реализует паттерн
 * Singleton для обеспечения единственного экземпляра класса.
 * 
 * Класс автоматически обрабатывает как одиночные файлы, так и множественные файлы,
 * преобразуя их в объекты UploadFile для удобной работы. Поддерживает работу с
 * файлами из HTML-форм, AJAX-запросов и других источников загрузки файлов.
 * 
 * Каждый файл представлен объектом UploadFile, который предоставляет методы для
 * проверки, сохранения, перемещения и получения информации о файле.
 * 
 * @package CloudCastle\HttpRequest\Http
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * 
 * @example
 * ```php
 * use CloudCastle\HttpRequest\Http\Files;
 *
 * // Получение экземпляра файлов
 * $files = Files::getInstance();
 * 
 * // Получение одиночного файла
 * $avatar = $files->get('avatar');
 * if ($avatar instanceof UploadFile) {
 *     $avatar->save('/uploads/avatars/');
 * }
 * 
 * // Использование магических методов
 * $document = $files->document;
 * if ($document instanceof UploadFile) {
 *     echo $document->getName(); // Имя файла
 *     echo $document->getSize(); // Размер в байтах
 *     echo $document->getType(); // MIME тип
 * }
 * 
 * // Получение всех файлов
 * $allFiles = $files->all();
 * 
 * // Работа с множественными файлами
 * $photos = $files->get('photos'); // Массив UploadFile объектов
 * if (is_array($photos)) {
 *     foreach ($photos as $photo) {
 *         $photo->save('/uploads/photos/');
 *     }
 * }
 * 
 * // Проверка наличия файлов
 * if ($files->has('avatar')) {
 *     $avatar = $files->avatar;
 *     // Обработка файла
 * }
 * 
 * // Работа с файлами по имени
 * $profilePic = $files->get('profile.jpg');
 * if ($profilePic instanceof UploadFile) {
 *     $profilePic->move('/uploads/profiles/');
 * }
 * ```
 */
final class Files
{
    /**
     * @var array<string, UploadFile|array<int, UploadFile>> Массив загруженных файлов
     * 
     * Хранит все загруженные файлы в виде ассоциативного массива.
     * Ключ - имя файла, значение - объект UploadFile или массив UploadFile объектов
     * для множественных файлов.
     * 
     * @example
     * ```php
     * // Внутренняя структура массива
     * $this->data = [
     *     'avatar.jpg' => UploadFile объект, // Одиночный файл
     *     'document.pdf' => UploadFile объект, // Одиночный файл
     *     'photo1.jpg' => UploadFile объект, // Множественный файл
     *     'photo2.jpg' => UploadFile объект, // Множественный файл
     *     'photo3.jpg' => UploadFile объект, // Множественный файл
     *     'video.mp4' => UploadFile объект, // Одиночный файл
     * ];
     * 
     * // Исходные $_FILES данные:
     * // $_FILES['avatar'] = [
     * //   'name' => 'avatar.jpg',
     * //   'type' => 'image/jpeg',
     * //   'tmp_name' => '/tmp/phpABC123',
     * //   'error' => 0,
     * //   'size' => 1024000
     * // ];
     * 
     * // $_FILES['photos'] = [
     * //   'name' => ['photo1.jpg', 'photo2.jpg', 'photo3.jpg'],
     * //   'type' => ['image/jpeg', 'image/jpeg', 'image/jpeg'],
     * //   'tmp_name' => ['/tmp/phpDEF456', '/tmp/phpGHI789', '/tmp/phpJKL012'],
     * //   'error' => [0, 0, 0],
     * //   'size' => [512000, 768000, 1024000]
     * // ];
     * 
     * // Примеры различных типов загрузки:
     * 
     * // Одиночный файл:
     * // <input name="avatar" type="file">
     * // Результат: один UploadFile объект
     * 
     * // Множественные файлы:
     * // <input name="photos[]" type="file" multiple>
     * // Результат: массив UploadFile объектов
     * 
     * // Несколько полей с файлами:
     * // <input name="avatar" type="file">
     * // <input name="document" type="file">
     * // <input name="photos[]" type="file" multiple>
     * // Результат: смешанная структура
     * ```
     */
    protected array $data = [];
    
    use GetDataTrait, GetInstanceTrait;
    
    /**
     * Приватный конструктор класса Files
     * 
     * Инициализирует объект Files, загружая все файлы из глобального массива $_FILES
     * и преобразуя их в объекты UploadFile. Автоматически обрабатывает как одиночные,
     * так и множественные файлы.
     * 
     * Процесс инициализации:
     * 1. Перебирает все элементы массива $_FILES
     * 2. Определяет тип загрузки (одиночный или множественный)
     * 3. Для множественных файлов создает отдельный UploadFile для каждого файла
     * 4. Для одиночных файлов создает один UploadFile
     * 5. Сохраняет в $this->data с именем файла как ключом
     * 
     * @since 1.0.0
     * 
     * @example
     * ```php
     * // Конструктор вызывается автоматически при получении экземпляра
     * $files = Files::getInstance();
     * 
     * // В этот момент происходит обработка $_FILES:
     * 
     * // Если $_FILES содержит:
     * // $_FILES['avatar'] = [
     * //   'name' => 'profile.jpg',
     * //   'type' => 'image/jpeg',
     * //   'tmp_name' => '/tmp/phpABC123',
     * //   'error' => 0,
     * //   'size' => 1024000
     * // ];
     * // $_FILES['photos'] = [
     * //   'name' => ['photo1.jpg', 'photo2.jpg'],
     * //   'type' => ['image/jpeg', 'image/jpeg'],
     * //   'tmp_name' => ['/tmp/phpDEF456', '/tmp/phpGHI789'],
     * //   'error' => [0, 0],
     * //   'size' => [512000, 768000]
     * // ];
     * 
     * // То в $this->data будет:
     * // $this->data['profile.jpg'] = UploadFile объект для аватара
     * // $this->data['photo1.jpg'] = UploadFile объект для первой фотографии
     * // $this->data['photo2.jpg'] = UploadFile объект для второй фотографии
     * 
     * // Примеры различных сценариев загрузки:
     * 
     * // Сценарий 1: Одиночный файл
     * // HTML: <input name="avatar" type="file">
     * // $_FILES['avatar'] = ['name' => 'user.jpg', 'type' => 'image/jpeg', ...]
     * // Результат: $this->data['user.jpg'] = UploadFile объект
     * 
     * // Сценарий 2: Множественные файлы
     * // HTML: <input name="photos[]" type="file" multiple>
     * // $_FILES['photos'] = [
     * //   'name' => ['photo1.jpg', 'photo2.jpg', 'photo3.jpg'],
     * //   'type' => ['image/jpeg', 'image/jpeg', 'image/jpeg'],
     * //   ...
     * // ];
     * // Результат: 
     * // $this->data['photo1.jpg'] = UploadFile объект
     * // $this->data['photo2.jpg'] = UploadFile объект
     * // $this->data['photo3.jpg'] = UploadFile объект
     * 
     * // Сценарий 3: Смешанная загрузка
     * // HTML: 
     * // <input name="avatar" type="file">
     * // <input name="document" type="file">
     * // <input name="photos[]" type="file" multiple>
     * // Результат: смешанная структура с одиночными и множественными файлами
     * 
     * // Сценарий 4: AJAX загрузка файлов
     * // JavaScript:
     * // const formData = new FormData();
     * // formData.append('avatar', fileInput.files[0]);
     * // formData.append('photos[]', fileInput.files[1]);
     * // formData.append('photos[]', fileInput.files[2]);
     * // fetch('/upload', { method: 'POST', body: formData });
     * // Результат: аналогично HTML формам
     * 
     * // Сценарий 5: Drag & Drop загрузка
     * // JavaScript:
     * // dropZone.addEventListener('drop', (e) => {
     * //   const formData = new FormData();
     * //   e.dataTransfer.files.forEach(file => {
     * //     formData.append('files[]', file);
     * //   });
     * //   fetch('/upload', { method: 'POST', body: formData });
     * // });
     * // Результат: массив файлов в $this->data
     * 
     * // Обработка ошибок загрузки:
     * // $_FILES['file'] = [
     * //   'name' => 'test.jpg',
     * //   'type' => 'image/jpeg',
     * //   'tmp_name' => '',
     * //   'error' => UPLOAD_ERR_NO_FILE, // 4 - файл не был загружен
     * //   'size' => 0
     * // ];
     * // Результат: UploadFile объект с информацией об ошибке
     * 
     * // Обработка больших файлов:
     * // $_FILES['large_file'] = [
     * //   'name' => 'video.mp4',
     * //   'type' => 'video/mp4',
     * //   'tmp_name' => '/tmp/phpXYZ789',
     * //   'error' => 0,
     * //   'size' => 52428800 // 50MB
     * // ];
     * // Результат: UploadFile объект с полной информацией о файле
     * 
     * // Практические примеры использования:
     * 
     * // Загрузка аватара пользователя:
     * // HTML: <input name="avatar" type="file" accept="image/*">
     * // PHP: $avatar = $files->get('avatar.jpg');
     * //      if ($avatar && $avatar->isValid()) {
     * //        $avatar->save('/uploads/avatars/');
     * //      }
     * 
     * // Загрузка документов:
     * // HTML: <input name="document" type="file" accept=".pdf,.doc,.docx">
     * // PHP: $document = $files->get('document.pdf');
     * //      if ($document && $document->getSize() < 10485760) { // 10MB limit
     * //        $document->move('/uploads/documents/');
     * //      }
     * 
     * // Загрузка галереи фотографий:
     * // HTML: <input name="photos[]" type="file" multiple accept="image/*">
     * // PHP: $photos = $files->all();
     * //      foreach ($photos as $photo) {
     * //        if (strpos($photo->getName(), 'photo') === 0) {
     * //          $photo->save('/uploads/gallery/');
     * //        }
     * //      }
     * ```
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