<?php

declare(strict_types=1);

use CloudCastle\HttpRequest\Request;
use CloudCastle\HttpRequest\Http\Get;

/**
 * Глобальная вспомогательная функция для доступа к GET-параметрам
 * 
 * Предоставляет удобный способ получения GET-параметров из URL запроса.
 * Если передан ключ, возвращает значение конкретного GET-параметра,
 * иначе возвращает объект Get для работы со всеми GET-параметрами.
 * 
 * Функция автоматически обрабатывает различные типы данных и предоставляет
 * безопасный доступ к параметрам URL, включая пагинацию, фильтры и поиск.
 * 
 * @param string|null $key Имя GET-параметра (опционально)
 * @param mixed $default Значение по умолчанию, если параметр не найден
 * @return mixed|Get Значение параметра или объект Get
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * 
 * @example
 * ```php
 * // Получение отдельных GET-параметров
 * $id = get('id');
 * $page = get('page', 1);
 * $limit = get('limit', 20);
 * $search = get('search', '');
 * 
 * // Получение всех GET-параметров
 * $allParams = get()->all();
 * 
 * // Работа с пагинацией
 * $currentPage = get('page', 1);
 * $itemsPerPage = get('limit', 20);
 * $offset = ($currentPage - 1) * $itemsPerPage;
 * 
 * // Обработка поиска
 * $searchQuery = get('q', '');
 * $searchCategory = get('category', 'all');
 * $searchSort = get('sort', 'relevance');
 * 
 * if (!empty($searchQuery)) {
 *     // Выполнение поиска
 *     $results = performSearch($searchQuery, $searchCategory, $searchSort);
 * }
 * 
 * // Работа с фильтрами
 * $filters = [
 *     'price_min' => get('price_min'),
 *     'price_max' => get('price_max'),
 *     'brand' => get('brand'),
 *     'color' => get('color'),
 *     'size' => get('size'),
 *     'rating' => get('rating')
 * ];
 * 
 * // Обработка множественных значений
 * $categories = get('categories', []); // Для параметров типа ?categories[]=1&categories[]=2
 * $tags = get('tags', []);
 * $colors = get('colors', []);
 * 
 * // Работа с сортировкой
 * $sortField = get('sort_by', 'created_at');
 * $sortOrder = get('sort_order', 'desc');
 * 
 * $allowedSortFields = ['name', 'price', 'created_at', 'rating'];
 * if (!in_array($sortField, $allowedSortFields)) {
 *     $sortField = 'created_at';
 * }
 * 
 * // Обработка ID объектов
 * $userId = get('user_id');
 * $productId = get('product_id');
 * $orderId = get('order_id');
 * 
 * // Валидация числовых параметров
 * $page = get('page', 1);
 * if (!is_numeric($page) || $page < 1) {
 *     $page = 1;
 * }
 * 
 * $limit = get('limit', 20);
 * if (!is_numeric($limit) || $limit < 1 || $limit > 100) {
 *     $limit = 20;
 * }
 * 
 * // Обработка булевых параметров
 * $showInactive = get('show_inactive', false);
 * $includeDeleted = get('include_deleted', false);
 * $debug = get('debug', false);
 * 
 * // Работа с языками и локализацией
 * $language = get('lang', 'en');
 * $locale = get('locale', 'en_US');
 * $timezone = get('timezone', 'UTC');
 * 
 * // Обработка API версионирования
 * $apiVersion = get('api_version', 'v1');
 * $format = get('format', 'json');
 * 
 * // Работа с токенами авторизации
 * $token = get('token');
 * $apiKey = get('api_key');
 * $accessToken = get('access_token');
 * 
 * // Обработка callback URL
 * $callback = get('callback');
 * $redirect = get('redirect');
 * $returnUrl = get('return_url');
 * 
 * // Работа с временными метками
 * $since = get('since'); // timestamp или дата
 * $until = get('until');
 * $date = get('date');
 * 
 * // Обработка географических параметров
 * $latitude = get('lat');
 * $longitude = get('lng');
 * $radius = get('radius', 10); // в километрах
 * $location = get('location');
 * 
 * // Работа с тегами и категориями
 * $tag = get('tag');
 * $category = get('category');
 * $subcategory = get('subcategory');
 * 
 * // Обработка статусов
 * $status = get('status', 'active');
 * $state = get('state');
 * $phase = get('phase');
 * 
 * // Работа с диапазонами
 * $ageMin = get('age_min');
 * $ageMax = get('age_max');
 * $priceMin = get('price_min');
 * $priceMax = get('price_max');
 * 
 * // Обработка флагов функций
 * $includeDetails = get('include_details', false);
 * $includeRelations = get('include_relations', false);
 * $includeStats = get('include_stats', false);
 * 
 * // Работа с экспортом данных
 * $export = get('export', false);
 * $exportFormat = get('export_format', 'csv');
 * $exportFields = get('export_fields', []);
 * 
 * // Обработка предварительного просмотра
 * $preview = get('preview', false);
 * $draft = get('draft', false);
 * $test = get('test', false);
 * 
 * // Работа с кэшированием
 * $cache = get('cache', true);
 * $cacheTime = get('cache_time', 3600);
 * $noCache = get('no_cache', false);
 * 
 * // Обработка отладки
 * $debug = get('debug', false);
 * $verbose = get('verbose', false);
 * $trace = get('trace', false);
 * 
 * // Работа с лимитами и ограничениями
 * $maxResults = get('max_results', 1000);
 * $timeout = get('timeout', 30);
 * $retry = get('retry', 3);
 * 
 * // Обработка форматов ответа
 * $responseFormat = get('format', 'json');
 * $pretty = get('pretty', false);
 * $indent = get('indent', 2);
 * 
 * // Работа с кодировкой
 * $encoding = get('encoding', 'utf-8');
 * $charset = get('charset', 'utf-8');
 * 
 * // Обработка сжатия
 * $compress = get('compress', false);
 * $gzip = get('gzip', false);
 * 
 * // Работа с версиями
 * $version = get('version');
 * $build = get('build');
 * $release = get('release');
 * 
 * // Обработка окружения
 * $environment = get('env', 'production');
 * $stage = get('stage', 'live');
 * $mode = get('mode', 'normal');
 * 
 * // Создание URL с параметрами
 * $baseUrl = '/products';
 * $params = [
 *     'category' => get('category'),
 *     'brand' => get('brand'),
 *     'price_min' => get('price_min'),
 *     'price_max' => get('price_max'),
 *     'sort' => get('sort'),
 *     'page' => get('page', 1)
 * ];
 * 
 * // Удаление пустых параметров
 * $params = array_filter($params, function($value) {
 *     return $value !== null && $value !== '';
 * });
 * 
 * $url = $baseUrl . '?' . http_build_query($params);
 * 
 * // Обработка параметров для API
 * $apiParams = [
 *     'action' => get('action'),
 *     'method' => get('method'),
 *     'params' => get('params', []),
 *     'callback' => get('callback')
 * ];
 * 
 * // Валидация обязательных параметров
 * $requiredParams = ['action', 'method'];
 * foreach ($requiredParams as $param) {
 *     if (empty(get($param))) {
 *         throw new Exception("Обязательный параметр {$param} отсутствует");
 *     }
 * }
 * 
 * // Обработка параметров для статистики
 * $statsParams = [
 *     'period' => get('period', 'day'),
 *     'group_by' => get('group_by', 'date'),
 *     'metrics' => get('metrics', ['views', 'clicks']),
 *     'filters' => get('filters', [])
 * ];
 * 
 * // Работа с параметрами для отчетов
 * $reportParams = [
 *     'type' => get('report_type'),
 *     'date_from' => get('date_from'),
 *     'date_to' => get('date_to'),
 *     'format' => get('report_format', 'pdf'),
 *     'include_charts' => get('include_charts', true)
 * ];
 * 
 * // Обработка параметров для уведомлений
 * $notificationParams = [
 *     'type' => get('notification_type'),
 *     'priority' => get('priority', 'normal'),
 *     'channels' => get('channels', ['email']),
 *     'template' => get('template')
 * ];
 * 
 * // Логирование GET-запросов
 * $logData = [
 *     'timestamp' => date('Y-m-d H:i:s'),
 *     'ip' => $_SERVER['REMOTE_ADDR'],
 *     'user_agent' => $_SERVER['HTTP_USER_AGENT'],
 *     'get_params' => get()->all(),
 *     'url' => $_SERVER['REQUEST_URI']
 * ];
 * ```
 */
function get(string|null $key = null, mixed $default = null): mixed
{
    $post = Request::getInstance()->get;
    
    if($key){
        return $post->get($key, $default);
    }
    
    return $post;
}