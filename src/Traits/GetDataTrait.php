<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Traits;

trait GetDataTrait
{
    protected array $data = [];
    
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }
    
    public function get (string $name, mixed $default = null): mixed
    {
        $key = mb_strtolower($name);
        
        if(isset($this->data[$key])) {
            return $this->data[$key];
        }
        
        return $default;
    }
    
    public function all(): array
    {
        return $this->data;
    }
}