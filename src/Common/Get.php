<?php

declare(strict_types = 1);

namespace CloudCastle\HttpRequest\Common;

/**
 * Класс для работы с GET параметрами
 *
 * Предоставляет удобный интерфейс для доступа к GET данным ($_GET) через
 * паттерн Singleton. Позволяет получать и обрабатывать параметры URL,
 * переданные через строку запроса, в объектно-ориентированном стиле.
 *
 * Особенности:
 * - Автоматический доступ к GET данным
 * - Магические методы для доступа к параметрам
 * - Проверка существования параметров
 * - Значения по умолчанию
 * - Безопасный доступ к данным
 *
 * @package CloudCastle\HttpRequest\Common
 * @author Алексей Зорин <zorinalexey59292@gmail.com>
 *
 * @example
 * // Получение экземпляра
 * $get = Get::getInstance();
 *
 * // Доступ к GET параметрам
 * $page = $get->page;                  // Через магический метод
 * $sort = $get->get('sort', 'desc');   // Через метод get с дефолтом
 *
 * // Проверка существования
 * if ($get->has('filter')) {
 *     $filter = $get->filter;
 * }
 *
 * // Работа с параметрами поиска
 * $search = $get->search ?? '';
 * $category = $get->category ?? 'all';
 *
 * @property-read int $page Номер страницы
 * @property-read int $per_page Максимальное количество сущностей на странице
 * @property-read array<string, string> $sort Параметр сортировки
 * @property-read string $search Строка поиска
 */
final class Get extends AbstractSingleton
{
    /**
     * Инициализация и получение GET параметров
     *
     * Возвращает массив всех GET параметров из глобального массива $_GET.
     * Эти параметры содержат данные, переданные через строку запроса.
     *
     * @return array<string, mixed> Массив GET параметров
     *
     * @example
     * // Метод вызывается автоматически при getInstance()
     * $get = Get::getInstance();
     * // Все параметры будут доступны как свойства объекта
     *
     * // Доступ к параметрам
     * $page = $get->page;
     * $sort = $get->sort;
     * $filter = $get->filter;
     */
    protected static function checkRun(): array
    {
        return $_GET;
    }
    
    /**
     * Создать новый экземпляр класса Get
     *
     * Фабричный метод для создания экземпляра класса.
     * Используется в AbstractSingleton для безопасного создания объекта.
     *
     * @return static Новый экземпляр класса Get
     *
     * @example
     * // Метод вызывается автоматически в AbstractSingleton::getInstance()
     * $get = Get::getInstance();
     */
    protected static function createInstance(): static
    {
        return new self();
    }
}