<?php

namespace CloudCastle\HttpRequest\Exceptions;

use Exception;

/**
 * Class InputException
 *
 * Исключение, выбрасываемое при ошибках обработки входных данных (например, неподдерживаемый Content-Type, некорректный формат запроса и т.д.).
 *
 * Пример использования:
 * <code>
 * use CloudCastle\HttpRequest\Exceptions\InputException;
 *
 * if (!in_array($contentType, $supportedTypes)) {
 *     throw new InputException('Content type not supported');
 * }
 * </code>
 *
 * @package CloudCastle\HttpRequest\Exceptions
 */
final class InputException extends Exception
{
    
}