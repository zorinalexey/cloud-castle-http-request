<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Exceptions;

use Exception;

/**
 * Исключение для ошибок работы с хранилищами данных
 * 
 * Выбрасывается при возникновении ошибок в операциях с хранилищами данных,
 * такими как сессии, куки и другие типы хранилищ. Предоставляет информацию
 * о причине ошибки и контексте её возникновения.
 * 
 * @package CloudCastle\HttpRequest\Exceptions
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 * 
 * @example
 * // Выброс исключения при ошибке работы с куками
 * if (headers_sent()) {
 *     throw new StorageException('Headers already sent. Unable to set cookie.');
 * }
 * 
 * // Выброс исключения при ошибке сериализации
 * try {
 *     $serialized = serialize($data);
 * } catch (Exception $e) {
 *     throw new StorageException('Failed to serialize data', 0, $e);
 * }
 * 
 * // Обработка исключения
 * try {
 *     $storage->set('key', $value);
 * } catch (StorageException $e) {
 *     error_log('Storage error: ' . $e->getMessage());
 *     // Альтернативная логика обработки
 * }
 */
final class StorageException extends Exception
{
    /**
     * Создает новый экземпляр исключения хранилища
     * 
     * @param string $message Сообщение об ошибке
     * @param int $code Код ошибки (по умолчанию 0)
     * @param Exception|null $previous Предыдущее исключение для цепочки
     * 
     * @example
     * // Простое исключение
     * throw new StorageException('Storage is not available');
     * 
     * // Исключение с кодом ошибки
     * throw new StorageException('Permission denied', 403);
     * 
     * // Исключение с предыдущим исключением
     * try {
     *     $result = someStorageOperation();
     * } catch (Exception $e) {
     *     throw new StorageException('Storage operation failed', 0, $e);
     * }
     */
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}