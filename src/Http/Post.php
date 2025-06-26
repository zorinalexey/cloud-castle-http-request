<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;
use stdClass;

final class Post extends stdClass
{
    use GetDataTrait, GetInstanceTrait;
    
    private function __construct()
    {
        foreach ($_POST as $key => $value) {
            if(json_validate($value)){
                $value = json_decode($value);
            }
            
            $this->data[mb_strtolower($key)] = $value;
        }
    }
    
}