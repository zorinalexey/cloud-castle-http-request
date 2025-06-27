<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;
use stdClass;

/**
 * Class Get
 *
 * Класс-обёртка для доступа к GET-параметрам ($_GET) через удобные методы и магические свойства.
 * Реализует паттерн Singleton и предоставляет методы для получения значений по ключу, всех данных и магический геттер.
 *
 * Пример использования:
 * <code>
 * use CloudCastle\HttpRequest\Http\Get;
 *
 * $get = Get::getInstance();
 * $id = $get->get('id');
 * $name = $get->name;
 * $all = $get->all();
 * </code>
 *
 * @package CloudCastle\HttpRequest\Http
 */
final class Get extends stdClass
{
    use GetDataTrait, GetInstanceTrait;
    
    /**
     * Конструктор Get. Заполняет коллекцию данными из $_GET.
     * Вызывается только внутри класса (Singleton).
     *
     * Пример внутреннего использования:
     * <code>
     * $get = new self();
     * </code>
     */
    private function __construct()
    {
        foreach ($_GET as $key => $value) {
            if(json_validate($value)){
                $value = json_decode($value);
            }
            
            $this->data[mb_strtolower($key)] = $value;
        }
    }
    
}