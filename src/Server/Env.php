<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Server;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;
use stdClass;

/**
 * Class Env
 *
 * Класс-обёртка для доступа к переменным окружения ($_ENV/getenv) через удобные методы и магические свойства.
 * Реализует паттерн Singleton и предоставляет методы для получения значений по ключу, всех данных и магический геттер.
 * Позволяет также устанавливать переменные окружения через магический сеттер.
 *
 * Пример использования:
 * <code>
 * use CloudCastle\HttpRequest\Server\Env;
 *
 * $env = Env::getInstance();
 * $dbHost = $env->get('DB_HOST');
 * $appEnv = $env->app_env;
 * $all = $env->all();
 *
 * // Установка переменной окружения
 * $env->NEW_VAR = 'value';
 * </code>
 *
 * @package CloudCastle\HttpRequest\Server
 * @extends GetDataTrait<mixed>
 */
final class Env extends stdClass
{
    /**
     * @var array<string, mixed>
     */
    protected array $data = [];
    use GetDataTrait, GetInstanceTrait;
    
    /**
     * Создать экземпляр для тестирования (внутренний метод)
     *
     * @return static Экземпляр Env
     */
    public static function createForTesting(): static
    {
        return new static();
    }
    
    /**
     * Конструктор Env. Заполняет коллекцию данными из переменных окружения.
     * Вызывается только внутри класса (Singleton).
     *
     * Пример внутреннего использования:
     * <code>
     * $env = new self();
     * </code>
     */
    protected function __construct()
    {
        foreach ($_ENV as $key => $value) {
            if(json_validate($value)){
                $value = json_decode($value);
            }
            
            $this->data[mb_strtolower($key)] = $value;
        }
    }
    
    /**
     * Магический сеттер для установки переменной окружения.
     *
     * @param string $name Имя переменной окружения
     * @param mixed $value Значение переменной
     *
     * Пример:
     * <code>
     * $env->MY_VAR = 'test';
     * </code>
     */
    public function __set(string $name, mixed $value): void
    {
        $_ENV[$name] = $value;
        
        if(is_string($value) && in_array($value, ['true', 'on', 'yes', '1'])){
            $value = true;
        }
        
        if(is_string($value) && in_array($value, ['false', 'off', 'no', '0'])){
            $value = false;
        }
        
        if(is_string($value) && empty($value)){
            $value = null;
        }
        
        if(is_numeric($value)){
            if(is_float($value)){
                $value = (float)$value;
            }else{
                $value = (int)$value;
            }
        }
        
        if(is_array($value) || is_object($value) || is_bool($value)){
            $value = json_encode($value);
        }
        
        putenv("$name=".escapeshellarg($value));
        $this->data[mb_strtolower($name)] = $value;
    }
}