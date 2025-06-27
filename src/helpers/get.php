<?php

declare(strict_types=1);

use CloudCastle\HttpRequest\Request;
use CloudCastle\HttpRequest\Http\Get;

/**
 * Глобальная вспомогательная функция для доступа к GET-параметрам.
 *
 * Если передан ключ, возвращает значение GET-параметра, иначе возвращает объект Get для доступа ко всем параметрам.
 *
 * @param string|null $key Имя GET-параметра (опционально)
 * @param mixed $default Значение по умолчанию, если параметр не найден
 * @return mixed|Get Значение параметра или объект Get
 *
 * Пример использования:
 * <code>
 * $id = get('id');
 * $all = get()->all();
 * </code>
 */
function get(string|null $key = null, mixed $default = null): mixed
{
    $post = Request::getInstance()->get;
    
    if($key){
        return $post->get($key, $default);
    }
    
    return $post;
}