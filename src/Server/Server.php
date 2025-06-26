<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Server;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;
use stdClass;

final class Server extends stdClass
{
    use GetDataTrait, GetInstanceTrait;
    
    private function __construct()
    {
        foreach ($_SERVER as $key => $value) {
            if(is_string($value) && json_validate($value)){
                $value = json_decode($value);
            }
            
            $this->data[mb_strtolower($key)] = $value;
        }
    }
    
}