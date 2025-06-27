<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use CloudCastle\HttpRequest\Traits\SetExpireTrait;

/**
 * Class Session
 *
 * Класс-обёртка для работы с сессиями пользователя ($_SESSION) через удобные методы и магические свойства.
 * Реализует паттерн Singleton, позволяет управлять временем жизни сессии, получать, устанавливать, удалять и очищать значения.
 *
 * Пример использования:
 * <code>
 * use CloudCastle\HttpRequest\Http\Session;
 *
 * $session = Session::getInstance();
 * $session->set('user_id', 123);
 * $userId = $session->get('user_id');
 * $session->delete('user_id');
 * $session->clear();
 * </code>
 *
 * @package CloudCastle\HttpRequest\Http
 */
final class Session
{
    use SetExpireTrait;
    
    /**
     * Данные сессии.
     *
     * @var array
     *
     * Пример:
     * <code>
     * $this->data = ['user_id' => 123];
     * </code>
     */
    private array $data = [];
    
    /**
     * Конструктор Session. Инициализирует сессию, загружает данные и управляет временем жизни.
     * Вызывается только внутри класса (Singleton).
     *
     * Пример внутреннего использования:
     * <code>
     * $session = new self();
     * </code>
     */
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
    
    /**
     * Получить значение из сессии по ключу.
     *
     * @param string $key Имя ключа
     * @param mixed $default Значение по умолчанию, если ключ не найден
     * @return mixed Значение из сессии или $default
     *
     * Пример:
     * <code>
     * $userId = $session->get('user_id', 0);
     * </code>
     */
    public function get(string $key, $default = null): mixed
    {
        if(array_key_exists($key, $this->data)){
            return unserialize($this->data[$key]);
        }
        
        return $default;
    }
    
    /**
     * Установить значение в сессию по ключу.
     *
     * @param string $key Имя ключа
     * @param mixed $value Значение
     * @return self
     *
     * Пример:
     * <code>
     * $session->set('user_id', 123);
     * </code>
     */
    public function set(string $key, mixed $value): self
    {
        $this->data[$key] = serialize($value);
        $_SESSION = $this->data;
        
        return $this;
    }
    
    /**
     * Магический сеттер для установки значения в сессию.
     *
     * @param string $key Имя ключа
     * @param mixed $value Значение
     *
     * Пример:
     * <code>
     * $session->user_id = 123;
     * </code>
     */
    public function __set(string $key, mixed $value): void
    {
        $this->set($key, $value);
    }
    
    /**
     * Магический геттер для получения значения из сессии.
     *
     * @param string $key Имя ключа
     * @return mixed Значение из сессии или null
     *
     * Пример:
     * <code>
     * $userId = $session->user_id;
     * </code>
     */
    public function __get(string $key): mixed
    {
        return $this->get($key);
    }
    
    /**
     * Удалить значение из сессии по ключу.
     *
     * @param string $key Имя ключа
     * @return self
     *
     * Пример:
     * <code>
     * $session->delete('user_id');
     * </code>
     */
    public function delete(string $key): self
    {
        unset($this->data[$key]);
        $_SESSION = $this->data;
        
        return $this;
    }
    
    /**
     * Очистить все данные сессии.
     *
     * @return self
     *
     * Пример:
     * <code>
     * $session->clear();
     * </code>
     */
    public function clear(): self
    {
        $this->data = [];
        $_SESSION = $this->data;
        
        return $this;
    }
}