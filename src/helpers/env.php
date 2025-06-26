<?php

use CloudCastle\HttpRequest\Server\Env;

function env(string|null $key = null, mixed $default = null): mixed
{
    $env = Env::getInstance();
    
    if($key){
        return $env->get($key, $default);
    }
    
    return $env;
}