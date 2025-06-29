<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;
use stdClass;

/**
 * Class Post
 * 
 * Управляет POST-данными HTTP запроса. Предоставляет удобный интерфейс для работы
 * с глобальным массивом $_POST через методы и магические свойства. Реализует паттерн
 * Singleton для обеспечения единственного экземпляра класса.
 * 
 * Класс автоматически обрабатывает JSON-данные в POST-параметрах, преобразуя их
 * в объекты PHP. Все ключи автоматически приводятся к нижнему регистру для
 * обеспечения единообразия доступа к данным.
 * 
 * Поддерживает работу с данными из HTML-форм, AJAX-запросов, API-запросов
 * и других источников POST-данных.
 * 
 * @package CloudCastle\HttpRequest\Http
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * 
 * @example
 * ```php
 * use CloudCastle\HttpRequest\Http\Post;
 * 
 * // Получение экземпляра POST-данных
 * $post = Post::getInstance();
 * 
 * // Получение значений по ключу
 * $username = $post->get('username');
 * $email = $post->get('email', 'default@example.com');
 * 
 * // Использование магических методов
 * $userId = $post->user_id;
 * $firstName = $post->first_name;
 * 
 * // Получение всех данных
 * $allData = $post->all();
 * 
 * // Работа с JSON-данными
 * // POST body: {"user":{"name":"John","age":30}}
 * $user = $post->user; // Объект stdClass с name и age
 * 
 * // Работа с данными форм
 * // <form method="post">
 * //   <input name="username" value="john_doe">
 * //   <input name="email" value="john@example.com">
 * // </form>
 * $username = $post->username; // 'john_doe'
 * $email = $post->email; // 'john@example.com'
 * ```
 */
final class Post extends stdClass
{
    use GetDataTrait/*<mixed>*/, GetInstanceTrait;
    
    /**
     * @var array<string, mixed> Массив POST-данных
     * 
     * Хранит все POST-данные в виде ассоциативного массива.
     * Ключи автоматически приводятся к нижнему регистру, JSON-данные
     * преобразуются в объекты PHP.
     * 
     * @example
     * ```php
     * // Внутренняя структура массива
     * $this->data = [
     *     'username' => 'john_doe',
     *     'email' => 'john@example.com',
     *     'user_id' => '123',
     *     'user_data' => (object)['name' => 'John', 'age' => 30], // JSON преобразован в объект
     *     'preferences' => (object)['theme' => 'dark', 'language' => 'en'],
     *     'is_active' => '1',
     *     'role' => 'admin'
     * ];
     * 
     * // Исходные $_POST данные:
     * // $_POST['username'] = 'john_doe'
     * // $_POST['EMAIL'] = 'john@example.com' (преобразовано в 'email')
     * // $_POST['user_data'] = '{"name":"John","age":30}' (преобразовано в объект)
     * // $_POST['preferences'] = '{"theme":"dark","language":"en"}' (преобразовано в объект)
     * 
     * // Примеры различных источников данных:
     * 
     * // HTML форма:
     * // $_POST['username'] = 'john_doe'
     * // $_POST['email'] = 'john@example.com'
     * // $_POST['password'] = 'secret123'
     * 
     * // AJAX запрос с JSON:
     * // $_POST['data'] = '{"action":"update","params":{"id":123,"name":"John"}}'
     * 
     * // API запрос:
     * // $_POST['user'] = '{"id":123,"name":"John","email":"john@example.com"}'
     * // $_POST['settings'] = '{"theme":"dark","notifications":true}'
     * ```
     */
    protected array $data = [];
    
    /**
     * Приватный конструктор класса Post
     * 
     * Инициализирует объект Post, загружая все POST-данные из глобального
     * массива $_POST. Автоматически обрабатывает JSON-данные и приводит
     * ключи к нижнему регистру.
     * 
     * Процесс инициализации:
     * 1. Перебирает все элементы массива $_POST
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
     * $post = Post::getInstance();
     * 
     * // В этот момент происходит обработка $_POST:
     * 
     * // Если $_POST содержит:
     * // $_POST['username'] = 'john_doe'
     * // $_POST['EMAIL'] = 'john@example.com'
     * // $_POST['user_id'] = '123'
     * // $_POST['user_data'] = '{"name":"John","age":30}'
     * // $_POST['preferences'] = '{"theme":"dark","language":"en"}'
     * // $_POST['IS_ACTIVE'] = '1'
     * 
     * // То в $this->data будет:
     * // $this->data['username'] = 'john_doe'
     * // $this->data['email'] = 'john@example.com' (ключ приведен к нижнему регистру)
     * // $this->data['user_id'] = '123'
     * // $this->data['user_data'] = (object)['name' => 'John', 'age' => 30] (JSON преобразован)
     * // $this->data['preferences'] = (object)['theme' => 'dark', 'language' => 'en']
     * // $this->data['is_active'] = '1' (ключ приведен к нижнему регистру)
     * 
     * // Примеры различных типов POST-данных:
     * 
     * // Простая HTML форма:
     * // <form method="post">
     * //   <input name="username" value="john_doe">
     * //   <input name="email" value="john@example.com">
     * //   <input name="password" value="secret123">
     * // </form>
     * // Результат: ['username' => 'john_doe', 'email' => 'john@example.com', 'password' => 'secret123']
     * 
     * // AJAX запрос с JSON:
     * // fetch('/api/user', {
     * //   method: 'POST',
     * //   body: JSON.stringify({name: 'John', age: 30})
     * // });
     * // $_POST['{"name":"John","age":30}'] = '' (если отправлено как raw JSON)
     * // Или: $_POST['data'] = '{"name":"John","age":30}' (если отправлено как form data)
     * 
     * // API запрос с множественными данными:
     * // $_POST['user'] = '{"id":123,"name":"John","email":"john@example.com"}'
     * // $_POST['settings'] = '{"theme":"dark","notifications":true}'
     * // $_POST['action'] = 'update'
     * // Результат: 
     * // [
     * //   'user' => (object)['id' => 123, 'name' => 'John', 'email' => 'john@example.com'],
     * //   'settings' => (object)['theme' => 'dark', 'notifications' => true],
     * //   'action' => 'update'
     * // ]
     * 
     * // Форма с файлами и данными:
     * // <form method="post" enctype="multipart/form-data">
     * //   <input name="username" value="john_doe">
     * //   <input name="avatar" type="file">
     * //   <input name="user_data" value='{"bio":"Developer","location":"NYC"}'>
     * // </form>
     * // Результат: 
     * // [
     * //   'username' => 'john_doe',
     * //   'user_data' => (object)['bio' => 'Developer', 'location' => 'NYC']
     * // ]
     * // (файлы обрабатываются отдельно в $_FILES)
     * 
     * // Обработка вложенных JSON структур:
     * // $_POST['complex_data'] = '{"user":{"profile":{"name":"John","age":30},"settings":{"theme":"dark"}},"meta":{"version":"1.0"}}'
     * // Результат: 
     * // $this->data['complex_data'] = (object)[
     * //   'user' => (object)[
     * //     'profile' => (object)['name' => 'John', 'age' => 30],
     * //     'settings' => (object)['theme' => 'dark']
     * //   ],
     * //   'meta' => (object)['version' => '1.0']
     * // ]
     * ```
     */
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