<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;
use stdClass;

/**
 * Class Post
 *
 * Класс-обёртка для доступа к POST-данным ($_POST) через удобные методы и магические свойства.
 * Реализует паттерн Singleton и предоставляет методы для получения значений по ключу, всех данных и магический геттер.
 *
 * Пример использования:
 * <code>
 * use CloudCastle\HttpRequest\Http\Post;
 *
 * $post = Post::getInstance();
 * $username = $post->get('username');
 * $email = $post->email;
 * $all = $post->all();
 * </code>
 *
 * @package CloudCastle\HttpRequest\Http
 */
final class Post extends stdClass
{
    use GetDataTrait/*<mixed>*/, GetInstanceTrait;
    
    /**
     * Конструктор Post. Заполняет коллекцию данными из $_POST.
     * Вызывается только внутри класса (Singleton).
     *
     * Пример внутреннего использования:
     * <code>
     * $post = new self();
     * </code>
     */
    private function __construct()
    {
        foreach ($_POST as $key => $value) {
            if(json_validate($value)){
                $value = json_decode($value);
            }
            
            $this->data[mb_strtolower($key)] = $value;
        }
    }
    
}