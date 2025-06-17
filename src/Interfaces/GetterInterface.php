<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Interfaces;

/**
 * Интерфейс для объектов с возможностью получения данных
 * 
 * Определяет контракт для классов, которые предоставляют доступ к данным
 * через магические методы и методы getter. Используется для создания
 * единообразного API доступа к различным типам данных (GET, POST, сессии, куки и т.д.).
 * 
 * @package CloudCastle\HttpRequest\Interfaces
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 * 
 * @example
 * // Реализация в классе
 * class MyDataClass implements GetterInterface
 * {
 *     private array $data = ['name' => 'John', 'age' => 30];
 *     
 *     public function __get(string $key): mixed
 *     {
 *         return $this->get($key);
 *     }
 *     
 *     public function has(string $key): bool
 *     {
 *         return array_key_exists($key, $this->data);
 *     }
 *     
 *     public function get(string $key, mixed $default = null): mixed
 *     {
 *         return $this->data[$key] ?? $default;
 *     }
 * }
 * 
 * // Использование
 * $data = new MyDataClass();
 * $name = $data->name;           // Через магический метод
 * $age = $data->get('age');      // Через метод get
 * $exists = $data->has('name');  // Проверка существования
 */
interface GetterInterface
{
    /**
     * Магический метод для доступа к данным как к свойствам объекта
     * 
     * Позволяет обращаться к данным через синтаксис свойств объекта.
     * Должен делегировать вызов методу get() для получения значения.
     *
     * @param string $key Ключ для получения данных
     *
     * @return mixed Значение по указанному ключу или null, если ключ не найден
     * 
     * @example
     * $data = new MyDataClass();
     * $value = $data->someKey;  // Эквивалентно $data->get('someKey')
     */
    public function __get(string $key): mixed;
    
    /**
     * Проверить существование значения по указанному ключу
     * 
     * Возвращает true, если данные по указанному ключу существуют,
     * false в противном случае. Не проверяет значение на null,
     * только факт существования ключа.
     *
     * @param string $key Ключ для проверки существования
     *
     * @return bool true, если ключ существует, false в противном случае
     * 
     * @example
     * $data = new MyDataClass();
     * if ($data->has('user_id')) {
     *     $userId = $data->get('user_id');
     * }
     */
    public function has(string $key): bool;
    
    /**
     * Получить значение по ключу с возможностью указания значения по умолчанию
     * 
     * Возвращает значение по указанному ключу. Если ключ не существует
     * или значение равно null, возвращает значение по умолчанию.
     *
     * @param string $key Ключ для получения данных
     * @param mixed $default Значение по умолчанию, возвращаемое если ключ не найден
     *
     * @return mixed Значение по ключу или значение по умолчанию
     * 
     * @example
     * $data = new MyDataClass();
     * $name = $data->get('name', 'Anonymous');     // Вернет 'John' или 'Anonymous'
     * $age = $data->get('age', 18);                // Вернет 30 или 18
     * $email = $data->get('email', '');            // Вернет email или пустую строку
     */
    public function get(string $key, mixed $default = null): mixed;
}