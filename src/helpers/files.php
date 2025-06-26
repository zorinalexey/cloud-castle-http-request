<?php

use CloudCastle\HttpRequest\Http\Files;

function files(string|null $name): mixed
{
    $files = Files::getInstance();
    
    if($name){
        foreach ($files->all() as $key => $value) {
            if($key === $name){
                return $value;
            }
        }
    }
    
    return $files;
}