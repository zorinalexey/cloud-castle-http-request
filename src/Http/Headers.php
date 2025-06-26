<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;
use stdClass;

final class Headers extends stdClass
{
    use GetDataTrait, GetInstanceTrait;
    
    private function __construct()
    {
        $headers = [];
        
        if(function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        }
        
        if(!$headers && function_exists('getallheaders')) {
            $headers = getallheaders();
        }
        
        if(!$headers) {
            foreach ($_SERVER as $key => $value) {
                $key = mb_strtoupper($key);
                
                if (str_starts_with($key, 'HTTP_')) {
                    $key = str_replace('HTTP_', '', $key);
                    $headers[$key] = $value;
                }
            }
        }
        
        foreach ($headers as $key => $value) {
            if(json_validate($value)){
                $value = json_decode($value);
            }
            
            $this->{$key} = $value;
        }
    }
    
    public function __set(string $key, mixed $value): void
    {
        $this->data[mb_strtolower($key)] = $value;
    }
}