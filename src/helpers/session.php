<?php

use CloudCastle\HttpRequest\Http\Session;

/**
 * Глобальная вспомогательная функция для доступа к сессии пользователя.
 *
 * Если передан ключ, возвращает значение из сессии, иначе возвращает объект Session для работы со всей сессией.
 *
 * @param string|null $key Имя ключа сессии (опционально)
 * @param mixed $default Значение по умолчанию, если ключ не найден
 * @return mixed|Session Значение из сессии или объект Session
 *
 * Пример использования:
 * <code>
 * $userId = session('user_id');
 * $session = session();
 * $session->set('user_id', 123);
 * </code>
 */
function session(string|null $key = null, mixed $default = null): mixed
{
    $session = Session::getInstance();
    
    if($key){
        return $session->get($key, $default);
    }
    
    return $session;
}