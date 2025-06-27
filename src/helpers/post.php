<?php

declare(strict_types=1);

use CloudCastle\HttpRequest\Request;
use CloudCastle\HttpRequest\Http\Post;

/**
 * Глобальная вспомогательная функция для доступа к POST-данным.
 *
 * Если передан ключ, возвращает значение POST-параметра, иначе возвращает объект Post для доступа ко всем данным.
 *
 * @param string|null $key Имя POST-параметра (опционально)
 * @param mixed $default Значение по умолчанию, если параметр не найден
 * @return mixed|Post Значение параметра или объект Post
 *
 * Пример использования:
 * <code>
 * $username = post('username');
 * $all = post()->all();
 * </code>
 */
function post(string|null $key = null, mixed $default = null): mixed
{
    $post = Request::getInstance()->post;
    
    if($key){
        return $post->get($key, $default);
    }
    
    return $post;
}