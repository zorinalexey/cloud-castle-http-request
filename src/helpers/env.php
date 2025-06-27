<?php

use CloudCastle\HttpRequest\Server\Env;

/**
 * Глобальная вспомогательная функция для доступа к переменным окружения.
 *
 * Если передан ключ, возвращает значение переменной окружения, иначе возвращает объект Env для доступа ко всем переменным.
 *
 * @param string|null $key Имя переменной окружения (опционально)
 * @param mixed $default Значение по умолчанию, если переменная не найдена
 * @return mixed|Env Значение переменной или объект Env
 *
 * Пример использования:
 * <code>
 * $dbHost = env('DB_HOST');
 * $all = env()->all();
 * </code>
 */
function env(string|null $key = null, mixed $default = null): mixed
{
    $env = Env::getInstance();
    
    if($key){
        return $env->get($key, $default);
    }
    
    return $env;
}