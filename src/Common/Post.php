<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Common;

/**
 * Класс для работы с POST данными
 * 
 * Предоставляет удобный интерфейс для доступа к POST данным ($_POST) через
 * паттерн Singleton. Позволяет получать и обрабатывать данные, отправленные
 * через HTTP POST запросы, в объектно-ориентированном стиле.
 * 
 * Поддерживаемые типы данных:
 * - Формальные данные (application/x-www-form-urlencoded)
 * - JSON данные (application/json)
 * - XML данные (application/xml, text/xml)
 * - Мультипарт данные (multipart/form-data)
 * 
 * Особенности:
 * - Автоматический доступ к POST данным
 * - Поддержка магических методов для доступа к свойствам
 * - Проверка существования данных
 * - Значения по умолчанию
 * - Безопасный доступ к данным
 * 
 * @package CloudCastle\HttpRequest\Common
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 * 
 * @example
 * // Получение экземпляра
 * $post = Post::getInstance();
 * 
 * // Доступ к POST данным
 * $username = $post->username;              // Через магический метод
 * $email = $post->get('email');             // Через метод get
 * $age = $post->get('age', 18);             // С значением по умолчанию
 * 
 * // Проверка существования
 * if ($post->has('user_id')) {
 *     $userId = $post->user_id;
 * }
 * 
 * // Работа с формами
 * $formData = [
 *     'name' => $post->name ?? '',
 *     'email' => $post->email ?? '',
 *     'message' => $post->message ?? ''
 * ];
 * 
 * // Обработка JSON данных
 * if ($post->has('json_data')) {
 *     $jsonData = json_decode($post->json_data, true);
 * }
 * 
 * @property-read mixed $username Имя пользователя из формы
 * @property-read mixed $email Email пользователя
 * @property-read mixed $password Пароль пользователя
 * @property-read mixed $message Сообщение пользователя
 * @property-read mixed $user_id ID пользователя
 * @property-read mixed $action Действие для обработки
 * @property-read mixed $token CSRF токен
 * @property-read mixed $submit Кнопка отправки формы
 */
final class Post extends AbstractSingleton
{
    /**
     * Инициализация и получение POST данных
     * 
     * Возвращает массив всех доступных POST данных из глобального
     * массива $_POST. Эти данные содержат информацию, отправленную
     * клиентом через HTTP POST запрос.
     *
     * @return array<string, mixed> Массив POST данных
     * 
     * @example
     * // Метод вызывается автоматически при getInstance()
     * $post = Post::getInstance();
     * // Все POST данные будут доступны как свойства объекта
     * 
     * // Доступ к данным формы
     * $username = $post->username;
     * $email = $post->email;
     * $message = $post->message;
     * 
     * // Проверка отправки формы
     * if ($post->has('submit')) {
     *     // Форма была отправлена
     *     processForm($post);
     * }
     */
    protected static function checkRun(): array
    {
        return $_POST;
    }
    
    /**
     * Создать новый экземпляр класса Post
     * 
     * Фабричный метод для создания экземпляра класса.
     * Используется в AbstractSingleton для безопасного создания объекта.
     *
     * @return static Новый экземпляр класса Post
     * 
     * @example
     * // Метод вызывается автоматически в AbstractSingleton::getInstance()
     * $post = Post::getInstance();
     */
    protected static function createInstance(): static
    {
        return new self();
    }
}