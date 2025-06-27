<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;
use stdClass;

/**
 * Class Headers
 *
 * Класс-обёртка для доступа к HTTP-заголовкам через удобные методы и магические свойства.
 * Реализует паттерн Singleton и предоставляет методы для получения значений по ключу, всех заголовков и магический геттер.
 * Позволяет также устанавливать заголовки через магический сеттер (только в рамках объекта).
 *
 * Пример использования:
 * <code>
 * use CloudCastle\HttpRequest\Http\Headers;
 *
 * $headers = Headers::getInstance();
 * $contentType = $headers->get('Content-Type');
 * $userAgent = $headers->user_agent;
 * $all = $headers->all();
 *
 * // Установка заголовка (в объекте)
 * $headers->X_CUSTOM_HEADER = 'value';
 * </code>
 *
 * @package CloudCastle\HttpRequest\Http
 */
final class Headers extends stdClass
{
    use GetDataTrait, GetInstanceTrait;
    
    /**
     * Конструктор Headers. Заполняет коллекцию данными из HTTP-заголовков.
     * Вызывается только внутри класса (Singleton).
     *
     * Пример внутреннего использования:
     * <code>
     * $headers = new self();
     * </code>
     */
    private function __construct()
    {
        $headers = [];
        
        if(function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        }
        
        if(!$headers && function_exists('getallheaders')) {
            $headers = getallheaders();
        }
        
        if(!$headers) {
            foreach ($_SERVER as $key => $value) {
                $key = mb_strtoupper($key);
                
                if (str_starts_with($key, 'HTTP_')) {
                    $key = str_replace('HTTP_', '', $key);
                    $headers[$key] = $value;
                }
            }
        }
        
        foreach ($headers as $key => $value) {
            if(json_validate($value)){
                $value = json_decode($value);
            }
            
            $this->{$key} = $value;
        }
    }
    
    /**
     * Магический сеттер для установки значения заголовка (в объекте и в HTTP-ответе).
     *
     * При установке значения через этот метод заголовок будет отправлен клиенту с помощью функции header().
     *
     * @param string $key Имя заголовка
     * @param mixed $value Значение заголовка
     *
     * Пример:
     * <code>
     * $headers->X_CUSTOM_HEADER = 'value'; // Отправит заголовок X-CUSTOM-HEADER: value
     * </code>
     */
    public function __set(string $key, mixed $value): void
    {
        $this->data[mb_strtolower($key)] = $value;
        header($key.': '.$value);
    }
}