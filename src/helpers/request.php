<?php

use CloudCastle\HttpRequest\Request;

/**
 * Глобальная вспомогательная функция для доступа к объекту Request или к отдельному параметру запроса.
 *
 * Если передан ключ, возвращает значение параметра запроса (GET, POST и др.), иначе возвращает объект Request.
 *
 * @param string|null $key Ключ параметра запроса (опционально)
 * @param mixed $default Значение по умолчанию, если параметр не найден
 * @return mixed|Request Значение параметра или объект Request
 *
 * Пример использования:
 * <code>
 * $id = request('id');
 * $all = request()->all();
 * </code>
 */
function request(string|null $key = null, mixed $default = null): mixed
{
    $request = Request::getInstance();
    
    if($key){
        return $request->get($key, $default);
    }
    
    return $request;
}