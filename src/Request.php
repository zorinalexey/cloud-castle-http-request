<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest;

use CloudCastle\HttpRequest\Exceptions\CloneException;
use CloudCastle\HttpRequest\Exceptions\InputException;
use CloudCastle\HttpRequest\Http\Cookie;
use CloudCastle\HttpRequest\Http\Files;
use CloudCastle\HttpRequest\Http\Get;
use CloudCastle\HttpRequest\Http\Headers;
use CloudCastle\HttpRequest\Http\Post;
use CloudCastle\HttpRequest\Http\Session;
use CloudCastle\HttpRequest\Interfaces\HttpRequestInterface;
use CloudCastle\HttpRequest\Server\Env;
use CloudCastle\HttpRequest\Server\Server;
use stdClass;

final class Request extends stdClass implements HttpRequestInterface
{
    private static array $contentTypes = [
        'application/json',
        'application/xml',
        'text/html',
        'application/x-www-form-urlencoded',
        'multipart/form-data',
    ];
    
    private static self|null $instance = null;
    
    private static array $expire = [
        'session' => 3600,
        'cookie' => 43200,
    ];
    
    private function __construct()
    {
        foreach ($this->getRequestData() as $key => $value) {
            $this->{$key} = $value;
        }
    }
    
    private function getRequestData(): array
    {
        $headers = Headers::getInstance();
        $default = [
            ...$_GET,
            'session' => Session::setExpire(self::$expire['session'])::getInstance(),
            'cookie' => Cookie::setExpire(self::$expire['cookie'])::getInstance(),
            'server' => Server::getInstance(),
            'env' => Env::getInstance(),
            'headers' => $headers,
            'post' => Post::getInstance(),
            'get' => Get::getInstance(),
        ];
        
        if(in_array($headers->{'Content-Type'}, self::$contentTypes)) {
            $data = $this->getRequest($headers);
        }else{
            throw new InputException('Content type not supported');
        }
        
        return match ($_SERVER['REQUEST_METHOD'] ?? 'GET') {
            'POST', 'PUT', 'PATCH' => [...$default, ...$data, ...$_POST, 'files' => Files::getInstance()],
            'DELETE' => [...$default, ...$data],
            default => $default,
        };
    }
    
    private function getRequest(Headers $headers): array
    {
        $data = [];
        $contentType = ($headers->{'Content-Type'} ?? ($_SERVER['CONTENT_TYPE'] ?? null))?? null;
        $input = file_get_contents('php://input');
        
        if ($input !== false && in_array($contentType, self::$contentTypes)) {
            if ($contentType === 'application/json' && function_exists('json_validate') && json_validate($input)) {
                $data = json_decode($input, true) ?? [];
            }
            
            if (($contentType === 'application/xml' || $contentType === 'text/xml')) {
                if (($xml = simplexml_load_string($input)) !== false && ($jsonString = json_encode($xml))) {
                    $data = json_decode($jsonString, true) ?? [];
                }
            }
        }
        
        return $data;
    }
    
    public static function getInstance(): static
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    public static function init(int $secondsSession = 3600, int $secondCookie = 3600): static
    {
        self::$expire = [
            'session' => $secondsSession,
            'cookie' => $secondCookie,
        ];
        
        return self::getInstance();
    }
    
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }
    
    public function get(string $name, $default = null): mixed
    {
        return $this->{$name} ?? $default;
    }
    
    private function __clone(): void
    {
        throw new CloneException('Клонирование объекта '.$this::class.' запрещено');
    }
    
    public function all(): array
    {
        return get_object_vars($this);
    }
}