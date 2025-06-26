<?php

use CloudCastle\HttpRequest\Request;

function request(string|null $key = null, mixed $default = null): mixed
{
    $request = Request::getInstance();
    
    if($key){
        return $request->get($key, $default);
    }
    
    return $request;
}