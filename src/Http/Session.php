<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use CloudCastle\HttpRequest\Traits\SetExpireTrait;

final class Session
{
    use SetExpireTrait;
    
    private array $data = [];
    
    private function __construct()
    {
        $sessionStatus = session_status();
        
        if($sessionStatus !== PHP_SESSION_DISABLED && $sessionStatus !== PHP_SESSION_ACTIVE) {
            session_start();
            ini_set('session.gc_maxlifetime', self::$expire);
            
            foreach ($_SESSION as $key => $value){
                if(is_string($value)){
                    $this->data[$key] = unserialize($value);
                }
            }
            
            $lastActive = $this->{'last_active'};
            
            if($lastActive !== NULL && time() - $lastActive > self::$expire) {
                session_unset();
                session_destroy();
            }else{
                $this->{'last_active'} = time();
            }
        }
    }
    
    public function get(string $key, $default = null): mixed
    {
        if(array_key_exists($key, $this->data)){
            return unserialize($this->data[$key]);
        }
        
        return $default;
    }
    
    public function set(string $key, mixed $value): self
    {
        $this->data[$key] = serialize($value);
        $_SESSION = $this->data;
        
        return $this;
    }
    
    public function __set(string $key, mixed $value): void
    {
        $this->set($key, $value);
    }
    
    public function __get(string $key): mixed
    {
        return $this->get($key);
    }
    
    public function delete(string $key): self
    {
        unset($this->data[$key]);
        $_SESSION = $this->data;
        
        return $this;
    }
    
    public function clear(): self
    {
        $this->data = [];
        $_SESSION = $this->data;
        
        return $this;
    }
}