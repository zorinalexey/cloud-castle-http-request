<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Server;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;
use stdClass;

final class Env extends stdClass
{
    use GetDataTrait, GetInstanceTrait;
    
    private function __construct()
    {
        $env = $_ENV;
        
        if(!$env){
            $env = getenv();
        }
        
        foreach ($env as $key => $value) {
            if(json_validate($value)){
                $value = json_decode($value);
            }
            
            $this->data[mb_strtolower($key)] = $value;
        }
    }
    
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
    }
}