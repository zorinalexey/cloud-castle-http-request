<?php

use CloudCastle\HttpRequest\Http\Cookie;

function cookies(string|null $key = null, mixed $default = null): mixed
{
    $cookie = Cookie::getInstance();
    
    if($key){
        return $cookie->get($key, $default);
    }
    
    return $cookie;
}