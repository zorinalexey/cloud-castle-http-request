<?php

namespace CloudCastle\HttpRequest\Exceptions;

use Exception;

/**
 * Class CloneException
 *
 * Исключение, выбрасываемое при попытке клонирования объектов, для которых это запрещено (например, Singleton).
 *
 * Пример использования:
 * <code>
 * use CloudCastle\HttpRequest\Exceptions\CloneException;
 *
 * class Singleton {
 *     private function __clone() {
 *         throw new CloneException('Клонирование запрещено');
 *     }
 * }
 * </code>
 *
 * @package CloudCastle\HttpRequest\Exceptions
 */
final class CloneException extends Exception
{
    
}