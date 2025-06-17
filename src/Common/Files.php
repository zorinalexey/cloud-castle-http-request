<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Common;

/**
 * Класс для работы с загруженными файлами
 *
 * Этот класс предоставляет объектно-ориентированный и безопасный доступ к данным,
 * переданным через механизм загрузки файлов в HTTP-запросах (глобальный массив $_FILES).
 * Реализует паттерн Singleton для централизованного доступа к файлам во всем приложении.
 * 
 * Класс наследует функциональность AbstractSingleton, что обеспечивает единственный
 * экземпляр для всего приложения и удобный доступ к данным файлов через магические
 * методы и методы интерфейса GetterInterface.
 *
 * Основные возможности:
 * - Централизованный доступ к данным загруженных файлов
 * - Магический доступ к файлам по имени поля формы
 * - Проверка существования файла по ключу
 * - Получение информации о файле с поддержкой значений по умолчанию
 * - Безопасная обработка данных файлов
 * - Регистронезависимый поиск файлов
 * - Кэширование результатов поиска для оптимизации
 * - Поддержка множественных файлов и массивов файлов
 *
 * @package CloudCastle\HttpRequest\Common
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 * @since 1.0.0
 *
 * @example
 * ```php
 * // Получение singleton-экземпляра
 * $files = Files::getInstance();
 *
 * // Доступ к файлу по имени поля формы
 * $avatar = $files->avatar; // Эквивалентно $_FILES['avatar']
 * 
 * // Проверка существования файла
 * if ($files->has('document')) {
 *     $doc = $files->get('document');
 * }
 * 
 * // Получение файла с дефолтным значением
 * $photo = $files->get('photo', null);
 *
 * // Работа с данными файла
 * if ($files->has('upload')) {
 *     $file = $files->upload;
 *     $fileName = $file['name'];
 *     $fileSize = $file['size'];
 *     $fileType = $file['type'];
 *     $tmpName = $file['tmp_name'];
 *     $error = $file['error'];
 * }
 *
 * // Перебор всех загруженных файлов
 * foreach ($_FILES as $key => $info) {
 *     $file = $files->$key;
 *     // обработка $file
 * }
 * ```
 * 
 * @see AbstractSingleton
 * @see GetterInterface
 *
 * @property-read array<string, mixed> $avatar Данные загруженного файла с ключом 'avatar'
 * @property-read array<string, mixed> $document Данные загруженного файла с ключом 'document'
 * @property-read array<string, mixed> $photo Данные загруженного файла с ключом 'photo'
 * @property-read array<string, mixed> $upload Данные загруженного файла с ключом 'upload'
 */
final class Files extends AbstractSingleton
{
    /**
     * Инициализация и получение данных загруженных файлов
     *
     * Этот метод обрабатывает массив всех файлов, загруженных через HTTP-запрос
     * (глобальный массив $_FILES). Для каждого поля формы всегда возвращается
     * массив объектов UploadFile (даже если файл один).
     *
     * @return array<string, array<int, UploadFile>> Массив обработанных файлов
     *
     * @example
     * $files = Files::getInstance();
     * $avatars = $files->avatar; // всегда массив UploadFile
     * foreach ($avatars as $file) {
     *     // $file instanceof UploadFile
     * }
     */
    protected static function checkRun(): array
    {
        $data = [];
        foreach ($_FILES as $fieldName => $file) {
            $data[$fieldName] = [];
            if (is_array($file['name'])) {
                foreach ($file['name'] as $key => $value) {
                    $dataFile = [
                        'name' => $file['name'][$key],
                        'type' => $file['type'][$key],
                        'size' => $file['size'][$key],
                        'tmp_name' => $file['tmp_name'][$key],
                        'error' => $file['error'][$key],
                    ];
                    $data[$fieldName][$key] = self::setFile($dataFile);
                }
            } else {
                $data[$fieldName][0] = self::setFile($file);
            }
        }
        return $data;
    }
    
    /**
     * Создать новый экземпляр класса Files
     *
     * Этот фабричный метод создает новый экземпляр класса Files. Используется
     * в AbstractSingleton для безопасного создания объекта при первом обращении
     * к getInstance(). Метод вызывается только один раз для каждого класса.
     *
     * @return static Новый экземпляр класса Files
     *
     * @example
     * ```php
     * // Метод вызывается автоматически в AbstractSingleton::getInstance()
     * $files = Files::getInstance();
     * 
     * // Все последующие вызовы возвращают тот же экземпляр
     * $files2 = Files::getInstance();
     * var_dump($files === $files2); // bool(true)
     * ```
     * 
     * @note Метод помечен как protected, поэтому не может быть вызван
     *       напрямую извне класса. Используйте getInstance() для получения экземпляра.
     * 
     * @see AbstractSingleton::getInstance()
     */
    protected static function createInstance(): static
    {
        return new self();
    }
    
    /**
     * Создать объект UploadFile из массива данных файла
     * 
     * Этот приватный метод создает объект UploadFile из массива данных файла,
     * полученного из глобального массива $_FILES. Объект UploadFile инкапсулирует
     * логику работы с файлом и предоставляет удобный API для получения информации
     * о файле, его валидации и обработки.
     * 
     * @param array<string, mixed> $file Массив данных файла из $_FILES
     * 
     * @return UploadFile Объект для работы с загруженным файлом
     * 
     * @example
     * ```php
     * // Метод вызывается автоматически в checkRun()
     * $fileData = [
     *     'name' => 'document.pdf',
     *     'type' => 'application/pdf',
     *     'size' => 2048000,
     *     'tmp_name' => '/tmp/php8G.tmp',
     *     'error' => 0
     * ];
     * 
     * $uploadFile = self::setFile($fileData);
     * // $uploadFile - объект UploadFile с методами для работы с файлом
     * ```
     * 
     * @note Метод помечен как private, поэтому может быть вызван только
     *       внутри класса Files.
     * 
     * @see Files::checkRun()
     */
    private static function setFile (array $file): UploadFile
    {
        return new UploadFile($file);
    }
}