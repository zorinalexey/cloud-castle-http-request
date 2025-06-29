<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;
use stdClass;

/**
 * Class Get
 * 
 * Управляет GET-параметрами HTTP запроса. Предоставляет удобный интерфейс для работы
 * с глобальным массивом $_GET через методы и магические свойства. Реализует паттерн
 * Singleton для обеспечения единственного экземпляра класса.
 * 
 * Класс автоматически обрабатывает JSON-данные в GET-параметрах, преобразуя их
 * в объекты PHP. Все ключи автоматически приводятся к нижнему регистру для
 * обеспечения единообразия доступа к данным.
 * 
 * @package CloudCastle\HttpRequest\Http
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * 
 * @example
 * ```php
 * use CloudCastle\HttpRequest\Http\Get;
 * 
 * // Получение экземпляра GET-параметров
 * $get = Get::getInstance();
 * 
 * // Получение значений по ключу
 * $id = $get->get('id');
 * $name = $get->get('name', 'default');
 * 
 * // Использование магических методов
 * $userId = $get->user_id;
 * $category = $get->category;
 * 
 * // Получение всех данных
 * $allData = $get->all();
 * 
 * // Работа с JSON-данными
 * // URL: ?user={"name":"John","age":30}
 * $user = $get->user; // Объект stdClass с name и age
 * ```
 */
final class Get extends stdClass
{
    /**
     * @var array<string, mixed> Массив GET-параметров
     * 
     * Хранит все GET-параметры в виде ассоциативного массива.
     * Ключи автоматически приводятся к нижнему регистру, JSON-данные
     * преобразуются в объекты PHP.
     * 
     * @example
     * ```php
     * // Внутренняя структура массива
     * $this->data = [
     *     'user_id' => '123',
     *     'name' => 'john_doe',
     *     'category' => 'electronics',
     *     'user' => (object)['name' => 'John', 'age' => 30], // JSON преобразован в объект
     *     'preferences' => (object)['theme' => 'dark', 'language' => 'en']
     * ];
     * 
     * // Исходные $_GET данные:
     * // $_GET['user_id'] = '123'
     * // $_GET['NAME'] = 'john_doe' (преобразовано в 'name')
     * // $_GET['user'] = '{"name":"John","age":30}' (преобразовано в объект)
     * ```
     */
    protected array $data = [];
    
    use GetDataTrait/*<mixed>*/, GetInstanceTrait;
    
    /**
     * Приватный конструктор класса Get
     * 
     * Инициализирует объект Get, загружая все GET-параметры из глобального
     * массива $_GET. Автоматически обрабатывает JSON-данные и приводит
     * ключи к нижнему регистру.
     * 
     * Процесс инициализации:
     * 1. Перебирает все элементы массива $_GET
     * 2. Проверяет, является ли значение валидным JSON
     * 3. Преобразует JSON в объект PHP если необходимо
     * 4. Приводит ключ к нижнему регистру
     * 5. Сохраняет в $this->data
     * 
     * @since 1.0.0
     * 
     * @example
     * ```php
     * // Конструктор вызывается автоматически при получении экземпляра
     * $get = Get::getInstance();
     * 
     * // В этот момент происходит обработка $_GET:
     * 
     * // Если $_GET содержит:
     * // $_GET['user_id'] = '123'
     * // $_GET['NAME'] = 'john_doe'
     * // $_GET['user'] = '{"name":"John","age":30}'
     * // $_GET['preferences'] = '{"theme":"dark","language":"en"}'
     * 
     * // То в $this->data будет:
     * // $this->data['user_id'] = '123'
     * // $this->data['name'] = 'john_doe' (ключ приведен к нижнему регистру)
     * // $this->data['user'] = (object)['name' => 'John', 'age' => 30] (JSON преобразован)
     * // $this->data['preferences'] = (object)['theme' => 'dark', 'language' => 'en']
     * 
     * // Примеры URL и их обработки:
     * // URL: ?id=123&name=John&user={"age":30}
     * // Результат: ['id' => '123', 'name' => 'john', 'user' => (object)['age' => 30]]
     * 
     * // URL: ?CATEGORY=electronics&User={"name":"John"}
     * // Результат: ['category' => 'electronics', 'user' => (object)['name' => 'John']]
     * ```
     */
    private function __construct()
    {
        foreach ($_GET as $key => $value) {
            if(json_validate($value)){
                $value = json_decode($value);
            }
            
            $this->data[mb_strtolower($key)] = $value;
        }
    }
    
}