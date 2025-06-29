<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use CloudCastle\HttpRequest\Traits\SetExpireTrait;

/**
 * Class Cookie
 * 
 * Управляет HTTP cookies в приложении. Предоставляет методы для получения,
 * установки, удаления и очистки cookies. Использует singleton паттерн для
 * обеспечения единственного экземпляра класса.
 * 
 * Класс автоматически десериализует значения cookies при инициализации
 * и сериализует их при установке. Поддерживает безопасные cookies
 * (HTTPS) и автоматически определяет домен.
 * 
 * @package CloudCastle\HttpRequest\Http
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * 
 * @example
 * ```php
 * use CloudCastle\HttpRequest\Http\Cookie;
 * 
 * // Получение экземпляра cookie
 * $cookie = Cookie::getInstance();
 * 
 * // Установка значений
 * $cookie->set('user_id', 123);
 * $cookie->set('user_data', ['name' => 'John', 'email' => 'john@example.com']);
 * 
 * // Получение значений
 * $userId = $cookie->get('user_id', 0);
 * $userData = $cookie->get('user_data', []);
 * 
 * // Использование магических методов
 * $cookie->user_id = 456;
 * $cookie['theme'] = 'dark';
 * $currentTheme = $cookie->theme;
 * $currentUserId = $cookie['user_id'];
 * 
 * // Удаление значений
 * $cookie->delete('user_id');
 * 
 * // Очистка всех cookies
 * $cookie->clear();
 * ```
 */
final class Cookie
{
    use SetExpireTrait;
    
    /**
     * @var array<string, mixed> Массив данных cookies
     * 
     * Хранит все cookies в виде ассоциативного массива,
     * где ключ - имя cookie, значение - десериализованные данные
     * 
     * @example
     * ```php
     * // Внутренняя структура массива
     * $this->data = [
     *     'user_id' => 123, // десериализованное значение
     *     'user_data' => ['name' => 'John', 'email' => 'john@example.com'],
     *     'theme' => 'dark',
     *     'preferences' => ['language' => 'en', 'timezone' => 'UTC']
     * ];
     * 
     * // При установке значения автоматически сериализуются
     * $this->set('complex_data', ['nested' => ['key' => 'value']]);
     * // В $_COOKIE будет: 'complex_data' => 'a:1:{s:6:"nested";a:1:{s:3:"key";s:5:"value";}}'
     * ```
     */
    private array $data = [];
    
    /**
     * Приватный конструктор класса Cookie
     * 
     * Инициализирует объект Cookie, загружая все существующие cookies
     * из глобального массива $_COOKIE и десериализуя их значения.
     * 
     * @throws \Exception Если произошла ошибка при десериализации cookie
     * @since 1.0.0
     * 
     * @example
     * ```php
     * // Конструктор вызывается автоматически при получении экземпляра
     * $cookie = Cookie::getInstance();
     * 
     * // В этот момент происходит:
     * // 1. Загрузка всех cookies из $_COOKIE
     * // 2. Десериализация каждого значения
     * // 3. Сохранение в $this->data
     * 
     * // Если в $_COOKIE есть:
     * // $_COOKIE['user_id'] = 'i:123;'
     * // $_COOKIE['user_data'] = 'a:2:{s:4:"name";s:4:"John";s:5:"email";s:15:"john@example.com";}'
     * 
     * // То в $this->data будет:
     * // $this->data['user_id'] = 123
     * // $this->data['user_data'] = ['name' => 'John', 'email' => 'john@example.com']
     * ```
     */
    private function __construct()
    {
        foreach ($_COOKIE as $name => $value) {
            $this->data[$name] = unserialize($value);
        }
    }
    
    /**
     * Получает значение cookie по ключу
     * 
     * Возвращает значение cookie с указанным именем. Если cookie не существует,
     * возвращает значение по умолчанию. После получения значения автоматически
     * устанавливает cookie с тем же значением для обновления времени жизни.
     * 
     * @param string $key Имя cookie для получения
     * @param mixed $default Значение по умолчанию, если cookie не существует
     * @return mixed Значение cookie или значение по умолчанию
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $cookie = Cookie::getInstance();
     * 
     * // Получение простых значений
     * $userId = $cookie->get('user_id', 0);
     * $theme = $cookie->get('theme', 'light');
     * $isLoggedIn = $cookie->get('is_logged_in', false);
     * 
     * // Получение массивов
     * $userData = $cookie->get('user_data', []);
     * $preferences = $cookie->get('preferences', ['language' => 'en']);
     * 
     * // Получение объектов
     * $user = $cookie->get('user_object', null);
     * 
     * // Получение несуществующих значений
     * $nonExistent = $cookie->get('non_existent', 'default_value');
     * echo $nonExistent; // 'default_value'
     * 
     * // Получение без значения по умолчанию
     * $value = $cookie->get('some_key'); // null если не существует
     * 
     * // Автоматическое обновление времени жизни
     * $cookie->get('user_id', 0); // Время жизни cookie обновляется
     * 
     * // Работа с вложенными данными
     * $userData = $cookie->get('user_data', []);
     * if (isset($userData['name'])) {
     *     echo $userData['name']; // 'John'
     * }
     * ```
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->data[$key] ?? $default;
        $this->set($key, $value);
        
        return $value;
    }
    
    /**
     * Устанавливает cookie с указанным именем и значением
     * 
     * Создает или обновляет cookie с заданным именем и значением.
     * Автоматически определяет безопасность соединения (HTTPS) и домен.
     * Сериализует значение перед сохранением в cookie.
     * 
     * @param string $key Имя cookie для установки
     * @param mixed $value Значение cookie (будет сериализовано)
     * @return self Возвращает текущий экземпляр для цепочки методов
     * @throws \Exception Если произошла ошибка при установке cookie
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $cookie = Cookie::getInstance();
     * 
     * // Установка простых значений
     * $cookie->set('user_id', 123);
     * $cookie->set('username', 'john_doe');
     * $cookie->set('is_logged_in', true);
     * $cookie->set('theme', 'dark');
     * 
     * // Установка массивов
     * $cookie->set('user_data', [
     *     'name' => 'John Doe',
     *     'email' => 'john@example.com',
     *     'role' => 'admin'
     * ]);
     * 
     * // Установка объектов
     * $user = new User('John', 'john@example.com');
     * $cookie->set('user_object', $user);
     * 
     * // Установка сложных структур данных
     * $cookie->set('preferences', [
     *     'theme' => 'dark',
     *     'language' => 'en',
     *     'notifications' => [
     *         'email' => true,
     *         'push' => false,
     *         'sms' => true
     *     ]
     * ]);
     * 
     * // Цепочка методов
     * $cookie->set('key1', 'value1')
     *        ->set('key2', 'value2')
     *        ->set('key3', 'value3');
     * 
     * // Перезапись существующих значений
     * $cookie->set('user_id', 456); // Перезаписывает предыдущее значение
     * 
     * // Автоматическое определение безопасности
     * // Если HTTPS: secure = true, httponly = false
     * // Если HTTP: secure = false, httponly = true
     * 
     * // Автоматическое определение домена
     * // Из $_SERVER['HTTP_HOST'] или '/' по умолчанию
     * ```
     */
    public function set(string $key, mixed $value): self
    {
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? true : false;
        $domain = (!empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '/');
        $this->data[$key] = $value;
        setcookie($key, serialize($value), self::$expire, '/', $domain, $secure, !$secure);
        
        return $this;
    }
    
    /**
     * Магический метод для установки cookie через свойство
     * 
     * Позволяет устанавливать cookies через обращение к объекту как к массиву
     * или через присваивание свойству. Внутренне вызывает метод set().
     * 
     * @param string $key Имя cookie для установки
     * @param mixed $value Значение cookie
     * @return void
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $cookie = Cookie::getInstance();
     * 
     * // Установка через свойство
     * $cookie->user_id = 123;
     * $cookie->username = 'john_doe';
     * $cookie->theme = 'dark';
     * $cookie->is_logged_in = true;
     * 
     * // Установка через массив
     * $cookie['user_data'] = ['name' => 'John', 'email' => 'john@example.com'];
     * $cookie['preferences'] = ['language' => 'en', 'timezone' => 'UTC'];
     * $cookie['settings'] = ['notifications' => true, 'auto_save' => false];
     * 
     * // Установка сложных данных
     * $cookie->user_object = new User('John', 'john@example.com');
     * $cookie['nested_data'] = [
     *     'level1' => [
     *         'level2' => [
     *             'level3' => 'deep_value'
     *         ]
     *     ]
     * ];
     * 
     * // Перезапись значений
     * $cookie->user_id = 456; // Перезаписывает предыдущее значение
     * $cookie['theme'] = 'light'; // Перезаписывает 'dark'
     * 
     * // Установка различных типов данных
     * $cookie->integer_value = 42;
     * $cookie->float_value = 3.14;
     * $cookie->string_value = 'Hello World';
     * $cookie->boolean_value = true;
     * $cookie->null_value = null;
     * ```
     */
    public function __set(string $key, mixed $value): void
    {
        $this->set($key, $value);
    }
    
    /**
     * Магический метод для получения cookie через свойство
     * 
     * Позволяет получать значения cookies через обращение к объекту как к массиву
     * или через обращение к свойству. Внутренне вызывает метод get().
     * 
     * @param string $key Имя cookie для получения
     * @return mixed Значение cookie или null, если не существует
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $cookie = Cookie::getInstance();
     * 
     * // Получение через свойство
     * $userId = $cookie->user_id;
     * $username = $cookie->username;
     * $theme = $cookie->theme;
     * $isLoggedIn = $cookie->is_logged_in;
     * 
     * // Получение через массив
     * $userData = $cookie['user_data'];
     * $preferences = $cookie['preferences'];
     * $settings = $cookie['settings'];
     * 
     * // Получение сложных данных
     * $userObject = $cookie->user_object;
     * $nestedData = $cookie['nested_data'];
     * 
     * // Проверка существования перед получением
     * if (isset($cookie->user_id)) {
     *     $userId = $cookie->user_id;
     * } else {
     *     $userId = 0; // значение по умолчанию
     * }
     * 
     * // Получение несуществующих значений
     * $nonExistent = $cookie->non_existent; // null
     * $anotherNonExistent = $cookie['another_non_existent']; // null
     * 
     * // Работа с вложенными данными
     * $userData = $cookie->user_data;
     * if (isset($userData['name'])) {
     *     echo $userData['name']; // 'John'
     * }
     * 
     * // Получение различных типов данных
     * $integerValue = $cookie->integer_value; // 42
     * $floatValue = $cookie->float_value; // 3.14
     * $stringValue = $cookie->string_value; // 'Hello World'
     * $booleanValue = $cookie->boolean_value; // true
     * $nullValue = $cookie->null_value; // null
     * ```
     */
    public function __get(string $key): mixed
    {
        return $this->get($key);
    }
    
    /**
     * Удаляет cookie с указанным именем
     * 
     * Удаляет cookie из внутреннего массива данных, из глобального массива $_COOKIE
     * и устанавливает cookie с пустым значением и временем жизни в прошлом,
     * что приводит к его удалению в браузере.
     * 
     * После удаления переустанавливает все оставшиеся cookies для обновления
     * их времени жизни.
     * 
     * @param string $key Имя cookie для удаления
     * @return self Возвращает текущий экземпляр для цепочки методов
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $cookie = Cookie::getInstance();
     * 
     * // Удаление отдельных значений
     * $cookie->delete('user_id');
     * $cookie->delete('username');
     * $cookie->delete('theme');
     * 
     * // Удаление массивов и объектов
     * $cookie->delete('user_data');
     * $cookie->delete('user_object');
     * $cookie->delete('preferences');
     * 
     * // Цепочка методов
     * $cookie->delete('key1')
     *        ->delete('key2')
     *        ->delete('key3');
     * 
     * // Удаление несуществующих ключей (безопасно)
     * $cookie->delete('non_existent_key'); // Ничего не происходит
     * 
     * // Проверка после удаления
     * $cookie->set('test', 'value');
     * echo $cookie->get('test'); // 'value'
     * 
     * $cookie->delete('test');
     * echo $cookie->get('test'); // null
     * 
     * // Удаление и переустановка оставшихся cookies
     * $cookie->set('keep1', 'value1');
     * $cookie->set('keep2', 'value2');
     * $cookie->set('remove', 'value3');
     * 
     * $cookie->delete('remove');
     * // keep1 и keep2 будут переустановлены с обновленным временем жизни
     * 
     * // Удаление в браузере
     * // Cookie устанавливается с временем жизни в прошлом (time() - 3600)
     * // что заставляет браузер немедленно удалить его
     * ```
     */
    public function delete(string $key): self
    {
        unset($this->data[$key], $_COOKIE[$key]);
        setcookie($key, '', time() - 3600);
        
        foreach ($this->data as $name => $value) {
            $this->set($name, $value);
        }
        
        return $this;
    }
    
    /**
     * Очищает все cookies
     * 
     * Удаляет все cookies, хранящиеся в объекте. Последовательно вызывает
     * метод delete() для каждого cookie.
     * 
     * @return self Возвращает текущий экземпляр для цепочки методов
     * @since 1.0.0
     * 
     * @example
     * ```php
     * $cookie = Cookie::getInstance();
     * 
     * // Установка нескольких значений
     * $cookie->set('user_id', 123);
     * $cookie->set('username', 'john_doe');
     * $cookie->set('theme', 'dark');
     * $cookie->set('user_data', ['name' => 'John']);
     * $cookie->set('preferences', ['language' => 'en']);
     * 
     * // Проверка что значения существуют
     * echo $cookie->get('user_id'); // 123
     * echo $cookie->get('username'); // 'john_doe'
     * echo $cookie->get('theme'); // 'dark'
     * 
     * // Очистка всех cookies
     * $cookie->clear();
     * 
     * // Проверка что все значения удалены
     * echo $cookie->get('user_id'); // null
     * echo $cookie->get('username'); // null
     * echo $cookie->get('theme'); // null
     * echo $cookie->get('user_data'); // null
     * echo $cookie->get('preferences'); // null
     * 
     * // Можно добавлять новые cookies после очистки
     * $cookie->set('new_key', 'new_value');
     * echo $cookie->get('new_key'); // 'new_value'
     * 
     * // Использование в цепочке методов
     * $cookie->clear()->set('fresh_start', true);
     * 
     * // Очистка при выходе пользователя
     * function logout() {
     *     $cookie = Cookie::getInstance();
     *     $cookie->clear(); // Удаляет все cookies пользователя
     *     // Дополнительная логика выхода...
     * }
     * 
     * // Очистка при смене аккаунта
     * function switchAccount() {
     *     $cookie = Cookie::getInstance();
     *     $cookie->clear(); // Очищает старые данные
     *     // Установка новых данных для нового аккаунта...
     * }
     * ```
     */
    public function clear(): self
    {
        foreach (array_keys($this->data) as $name) {
            $this->delete($name);
        }
        
        return $this;
    }
}