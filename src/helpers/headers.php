<?php

use CloudCastle\HttpRequest\Http\Headers;

/**
 * Глобальная вспомогательная функция для доступа к HTTP-заголовкам.
 *
 * Если передан ключ, возвращает значение заголовка, иначе возвращает объект Headers для доступа ко всем заголовкам.
 *
 * @param string|null $key Имя заголовка (опционально)
 * @param mixed $default Значение по умолчанию, если заголовок не найден
 * @return mixed|Headers Значение заголовка или объект Headers
 *
 * Пример использования:
 * <code>
 * $ua = headers('User-Agent');
 * $all = headers()->all();
 * </code>
 */
function headers(string|null $key = null, mixed $default = null): mixed
{
    $headers = Headers::getInstance();
    
    if($key){
        return $headers->get($key, $default);
    }
    
    return $headers;
}