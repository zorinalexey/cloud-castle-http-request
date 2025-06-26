<?php

use CloudCastle\HttpRequest\Http\Headers;

function headers(string|null $key = null, mixed $default = null): mixed
{
    $headers = Headers::getInstance();
    
    if($key){
        return $headers->get($key, $default);
    }
    
    return $headers;
}