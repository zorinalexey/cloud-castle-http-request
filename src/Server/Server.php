<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Server;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;
use stdClass;

/**
 * Class Server
 *
 * Класс-обёртка для доступа к данным суперглобального массива $_SERVER через удобные методы и магические свойства.
 * Реализует паттерн Singleton и предоставляет методы для получения значений по ключу, всех данных и магический геттер.
 *
 * Пример использования:
 * <code>
 * use CloudCastle\HttpRequest\Server\Server;
 *
 * $server = Server::getInstance();
 * $host = $server->get('HTTP_HOST');
 * $userAgent = $server->http_user_agent;
 * $all = $server->all();
 * </code>
 *
 * @package CloudCastle\HttpRequest\Server
 * @extends GetDataTrait<mixed>
 */
final class Server extends stdClass
{
    /**
     * @var array<string, mixed>
     */
    protected array $data = [];
    use GetDataTrait, GetInstanceTrait;
    
    /**
     * Конструктор Server. Заполняет коллекцию данными из $_SERVER.
     * Вызывается только внутри класса (Singleton).
     *
     * Пример внутреннего использования:
     * <code>
     * $server = new self();
     * </code>
     */
    private function __construct()
    {
        foreach ($_SERVER as $key => $value) {
            if(is_string($value) && json_validate($value)){
                $value = json_decode($value);
            }
            
            $this->data[mb_strtolower($key)] = $value;
        }
    }
    
}