<?php

use CloudCastle\HttpRequest\Http\Cookie;

/**
 * Глобальная вспомогательная функция для доступа к cookie.
 *
 * Если передан ключ, возвращает значение cookie, иначе возвращает объект Cookie для доступа ко всем cookie.
 *
 * @param string|null $key Имя cookie (опционально)
 * @param mixed $default Значение по умолчанию, если cookie не найден
 * @return mixed|Cookie Значение cookie или объект Cookie
 *
 * Пример использования:
 * <code>
 * $token = cookies('token');
 * $all = cookies()->all();
 * </code>
 */
function cookies(string|null $key = null, mixed $default = null): mixed
{
    $cookie = Cookie::getInstance();
    
    if($key){
        return $cookie->get($key, $default);
    }
    
    return $cookie;
}