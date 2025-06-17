<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Interfaces;

use Exception;

/**
 * Интерфейс для реализации паттерна Singleton
 * 
 * Определяет контракт для классов, которые должны иметь только один экземпляр
 * в течение всего жизненного цикла приложения. Обеспечивает глобальную точку
 * доступа к единственному экземпляру класса.
 * 
 * @package CloudCastle\HttpRequest\Interfaces
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 * 
 * @example
 * // Реализация в классе
 * class MySingleton implements SingletonInterface
 * {
 *     private static ?self $instance = null;
 *     private array $data = [];
 *     
 *     private function __construct()
 *     {
 *         // Приватный конструктор
 *     }
 *     
 *     public static function getInstance(): static
 *     {
 *         if (!self::$instance) {
 *             self::$instance = new self();
 *         }
 *         return self::$instance;
 *     }
 *     
 *     private function __clone()
 *     {
 *         // Запрет клонирования
 *     }
 * }
 * 
 * // Использование
 * $instance1 = MySingleton::getInstance();
 * $instance2 = MySingleton::getInstance();
 * // $instance1 === $instance2 (true) - один и тот же объект
 */
interface SingletonInterface
{
    /**
     * Получить единственный экземпляр класса
     * 
     * Создает новый экземпляр класса, если он еще не существует,
     * или возвращает существующий экземпляр. Этот метод является
     * единственным способом получения экземпляра класса.
     * 
     * @return static Единственный экземпляр класса
     * 
     * @example
     * // Получение экземпляра
     * $request = Request::getInstance();
     * 
     * // Использование в цепочке вызовов
     * $data = Request::getInstance()->getData();
     * 
     * // Множественные вызовы возвращают один объект
     * $instance1 = MyClass::getInstance();
     * $instance2 = MyClass::getInstance();
     * var_dump($instance1 === $instance2); // bool(true)
     * 
     * @throws Exception При ошибке создания экземпляра класса
     */
    public static function getInstance(): static;
}