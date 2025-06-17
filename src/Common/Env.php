<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Common;

/**
 * Класс для работы с переменными окружения
 *
 * Предоставляет удобный интерфейс для доступа к переменным окружения ($_ENV)
 * через паттерн Singleton. Позволяет получать и обрабатывать переменные окружения
 * в объектно-ориентированном стиле.
 *
 * Особенности:
 * - Автоматический доступ к переменным окружения
 * - Магические методы для доступа к переменным
 * - Проверка существования переменных
 * - Значения по умолчанию
 * - Безопасный доступ к данным
 *
 * @package CloudCastle\HttpRequest\Common
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 *
 * @example
 * // Получение экземпляра
 * $env = Env::getInstance();
 *
 * // Доступ к переменным окружения
 * $path = $env->PATH;                  // Через магический метод
 * $home = $env->get('HOME', '/root');  // Через метод get с дефолтом
 *
 * // Проверка существования
 * if ($env->has('APP_ENV')) {
 *     $appEnv = $env->APP_ENV;
 * }
 *
 * // Работа с пользовательскими переменными
 * $custom = $env->MY_CUSTOM_VAR ?? '';
 *
 * @property-read mixed $PATH Системный путь
 * @property-read mixed $HOME Домашний каталог пользователя
 * @property-read mixed $USER Имя пользователя
 * @property-read mixed $APP_ENV Окружение приложения
 * @property-read mixed $MY_CUSTOM_VAR Пользовательская переменная
 */
final class Env extends AbstractSingleton
{
    /**
     * Инициализация и получение переменных окружения
     *
     * Возвращает массив всех переменных окружения из глобального массива $_ENV.
     * Эти переменные содержат данные, определенные в окружении процесса.
     *
     * @return array<string, mixed> Массив переменных окружения
     *
     * @example
     * // Метод вызывается автоматически при getInstance()
     * $env = Env::getInstance();
     * // Все переменные будут доступны как свойства объекта
     *
     * // Доступ к переменным
     * $path = $env->PATH;
     * $home = $env->HOME;
     * $user = $env->USER;
     */
    protected static function checkRun(): array
    {
        return $_ENV;
    }
    
    /**
     * Создать новый экземпляр класса Env
     *
     * Фабричный метод для создания экземпляра класса.
     * Используется в AbstractSingleton для безопасного создания объекта.
     *
     * @return static Новый экземпляр класса Env
     *
     * @example
     * // Метод вызывается автоматически в AbstractSingleton::getInstance()
     * $env = Env::getInstance();
     */
    protected static function createInstance(): static
    {
        return new self();
    }
}