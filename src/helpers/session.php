<?php

use CloudCastle\HttpRequest\Http\Session;

function session(string|null $key = null, mixed $default = null): mixed
{
    $session = Session::getInstance();
    
    if($key){
        return $session->get($key, $default);
    }
    
    return $session;
}