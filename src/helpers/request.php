<?php

use CloudCastle\HttpRequest\Request;

/**
 * Глобальная вспомогательная функция для доступа к объекту Request или к отдельному параметру запроса
 * 
 * Предоставляет универсальный способ работы с HTTP запросами. Если передан ключ,
 * возвращает значение параметра из любого источника (GET, POST, PUT, DELETE),
 * иначе возвращает объект Request для работы со всеми данными запроса.
 * 
 * Функция автоматически определяет источник данных и предоставляет единый
 * интерфейс для работы с различными типами HTTP запросов.
 *
 * @param string|null $key Ключ параметра запроса (опционально)
 * @param mixed $default Значение по умолчанию, если параметр не найден
 * @return mixed|Request Значение параметра или объект Request
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 *
 * @example
 * ```php
 * // Получение отдельных параметров запроса
 * $id = request('id');
 * $name = request('name', 'default_name');
 * $email = request('email');
 * $page = request('page', 1);
 * 
 * // Получение объекта request для работы со всеми данными
 * $request = request();
 * 
 * // Работа с различными типами запросов
 * $method = request()->getMethod();
 * $isGet = $method === 'GET';
 * $isPost = $method === 'POST';
 * $isPut = $method === 'PUT';
 * $isDelete = $method === 'DELETE';
 * $isPatch = $method === 'PATCH';
 * 
 * // Получение всех данных запроса
 * $allData = request()->all();
 * $getData = request()->get()->all();
 * $postData = request()->post()->all();
 * $filesData = request()->files()->all();
 * 
 * // Работа с формой регистрации
 * if ($isPost) {
 *     $userData = [
 *         'username' => request('username'),
 *         'email' => request('email'),
 *         'password' => request('password'),
 *         'confirm_password' => request('confirm_password'),
 *         'terms_accepted' => request('terms_accepted', false)
 *     ];
 *     
 *     // Валидация данных
 *     $errors = [];
 *     if (empty($userData['username'])) {
 *         $errors['username'] = 'Имя пользователя обязательно';
 *     }
 *     if (empty($userData['email'])) {
 *         $errors['email'] = 'Email обязателен';
 *     }
 *     if ($userData['password'] !== $userData['confirm_password']) {
 *         $errors['password'] = 'Пароли не совпадают';
 *     }
 *     
 *     if (empty($errors)) {
 *         // Создание пользователя
 *         $user = new User($userData);
 *         $user->save();
 *     }
 * }
 * 
 * // Работа с API запросами
 * if (request()->isJson()) {
 *     $apiData = request()->json();
 *     $action = $apiData['action'] ?? '';
 *     $params = $apiData['params'] ?? [];
 *     $token = $apiData['token'] ?? '';
 *     
 *     // Обработка API действий
 *     switch ($action) {
 *         case 'create_user':
 *             $user = createUser($params);
 *             break;
 *         case 'update_user':
 *             $user = updateUser($params);
 *             break;
 *         case 'delete_user':
 *             $user = deleteUser($params);
 *             break;
 *     }
 * }
 * 
 * // Работа с поиском и фильтрацией
 * $searchParams = [
 *     'query' => request('q', ''),
 *     'category' => request('category', 'all'),
 *     'sort' => request('sort', 'relevance'),
 *     'order' => request('order', 'desc'),
 *     'page' => request('page', 1),
 *     'limit' => request('limit', 20),
 *     'filters' => [
 *         'price_min' => request('price_min'),
 *         'price_max' => request('price_max'),
 *         'brand' => request('brand'),
 *         'color' => request('color'),
 *         'size' => request('size')
 *     ]
 * ];
 * 
 * // Выполнение поиска
 * if (!empty($searchParams['query'])) {
 *     $results = performSearch($searchParams);
 * }
 * 
 * // Работа с пагинацией
 * $currentPage = request('page', 1);
 * $itemsPerPage = request('limit', 20);
 * $offset = ($currentPage - 1) * $itemsPerPage;
 * 
 * // Работа с сортировкой
 * $sortField = request('sort_by', 'created_at');
 * $sortOrder = request('sort_order', 'desc');
 * $allowedSortFields = ['name', 'price', 'created_at', 'rating'];
 * 
 * if (!in_array($sortField, $allowedSortFields)) {
 *     $sortField = 'created_at';
 * }
 * 
 * // Работа с фильтрами
 * $filters = [];
 * $filterFields = ['category', 'brand', 'color', 'size', 'price_min', 'price_max'];
 * 
 * foreach ($filterFields as $field) {
 *     $value = request($field);
 *     if ($value !== null && $value !== '') {
 *         $filters[$field] = $value;
 *     }
 * }
 * 
 * // Работа с множественными значениями
 * $categories = request('categories', []);
 * $tags = request('tags', []);
 * $colors = request('colors', []);
 * 
 * // Работа с булевыми параметрами
 * $showInactive = request('show_inactive', false);
 * $includeDeleted = request('include_deleted', false);
 * $debug = request('debug', false);
 * 
 * // Работа с числовыми параметрами
 * $age = request('age');
 * if ($age && (!is_numeric($age) || $age < 0 || $age > 150)) {
 *     throw new Exception('Некорректный возраст');
 * }
 * 
 * $price = request('price');
 * if ($price && (!is_numeric($price) || $price < 0)) {
 *     throw new Exception('Некорректная цена');
 * }
 * 
 * // Работа с датами
 * $dateFrom = request('date_from');
 * $dateTo = request('date_to');
 * $createdAt = request('created_at');
 * 
 * if ($dateFrom) {
 *     $dateFrom = DateTime::createFromFormat('Y-m-d', $dateFrom);
 * }
 * if ($dateTo) {
 *     $dateTo = DateTime::createFromFormat('Y-m-d', $dateTo);
 * }
 * 
 * // Работа с файлами
 * $uploadedFiles = request()->files()->all();
 * $avatar = request()->files('avatar');
 * $documents = request()->files('documents');
 * 
 * // Обработка загруженных файлов
 * if ($avatar && $avatar->isUploaded()) {
 *     $avatarPath = $avatar->save('/uploads/avatars/');
 * }
 * 
 * if ($documents && is_array($documents)) {
 *     foreach ($documents as $document) {
 *         if ($document && $document->isUploaded()) {
 *             $documentPath = $document->save('/uploads/documents/');
 *         }
 *     }
 * }
 * 
 * // Работа с заголовками
 * $userAgent = request()->headers('User-Agent');
 * $authorization = request()->headers('Authorization');
 * $contentType = request()->headers('Content-Type');
 * $acceptLanguage = request()->headers('Accept-Language');
 * 
 * // Определение типа клиента
 * $isMobile = preg_match('/Mobile|Android|iPhone|iPad/', $userAgent);
 * $isBot = preg_match('/bot|crawler|spider|crawling/', strtolower($userAgent));
 * $isAjax = request()->headers('X-Requested-With') === 'XMLHttpRequest';
 * 
 * // Работа с сессией
 * $sessionData = request()->session()->all();
 * $userId = request()->session('user_id');
 * $userRole = request()->session('user_role', 'guest');
 * $isLoggedIn = request()->session('is_logged_in', false);
 * 
 * // Проверка авторизации
 * if (!$isLoggedIn) {
 *     header('Location: /login');
 *     exit;
 * }
 * 
 * // Работа с cookie
 * $cookieData = request()->cookies()->all();
 * $language = request()->cookies('language', 'en');
 * $theme = request()->cookies('theme', 'light');
 * $trackingId = request()->cookies('tracking_id');
 * 
 * // Работа с переменными окружения
 * $envData = request()->env()->all();
 * $appEnv = request()->env('APP_ENV', 'production');
 * $debug = request()->env('APP_DEBUG', false);
 * $dbHost = request()->env('DB_HOST');
 * 
 * // Работа с серверными данными
 * $serverData = request()->server()->all();
 * $remoteAddr = request()->server('REMOTE_ADDR');
 * $requestUri = request()->server('REQUEST_URI');
 * $httpHost = request()->server('HTTP_HOST');
 * 
 * // Получение реального IP адреса
 * $realIP = request()->server('HTTP_X_REAL_IP') ?: 
 *           request()->server('HTTP_X_FORWARDED_FOR') ?: 
 *           $remoteAddr;
 * 
 * // Работа с URL параметрами
 * $url = request()->getUrl();
 * $path = request()->getPath();
 * $query = request()->getQuery();
 * $fragment = request()->getFragment();
 * 
 * // Создание URL с параметрами
 * $baseUrl = '/products';
 * $params = [
 *     'category' => request('category'),
 *     'brand' => request('brand'),
 *     'price_min' => request('price_min'),
 *     'price_max' => request('price_max'),
 *     'sort' => request('sort'),
 *     'page' => request('page', 1)
 * ];
 * 
 * // Удаление пустых параметров
 * $params = array_filter($params, function($value) {
 *     return $value !== null && $value !== '';
 * });
 * 
 * $fullUrl = $baseUrl . '?' . http_build_query($params);
 * 
 * // Работа с JSON данными
 * if (request()->isJson()) {
 *     $jsonData = request()->json();
 *     $action = $jsonData['action'] ?? '';
 *     $data = $jsonData['data'] ?? [];
 *     $meta = $jsonData['meta'] ?? [];
 *     
 *     // Обработка JSON запросов
 *     $response = handleJsonRequest($action, $data, $meta);
 *     header('Content-Type: application/json');
 *     echo json_encode($response);
 *     exit;
 * }
 * 
 * // Работа с XML данными
 * if (request()->isXml()) {
 *     $xmlData = request()->xml();
 *     // Обработка XML запросов
 * }
 * 
 * // Работа с multipart данными
 * if (request()->isMultipart()) {
 *     $formData = request()->post()->all();
 *     $files = request()->files()->all();
 *     // Обработка multipart запросов
 * }
 * 
 * // Валидация обязательных параметров
 * $requiredParams = ['action', 'method'];
 * foreach ($requiredParams as $param) {
 *     if (empty(request($param))) {
 *         throw new Exception("Обязательный параметр {$param} отсутствует");
 *     }
 * }
 * 
 * // Работа с токенами
 * $csrfToken = request('csrf_token');
 * $apiToken = request('api_token');
 * $accessToken = request('access_token');
 * 
 * // Проверка CSRF токена
 * if ($csrfToken && $csrfToken !== session('csrf_token')) {
 *     throw new Exception('Недействительный CSRF токен');
 * }
 * 
 * // Проверка API токена
 * if ($apiToken && !validateApiToken($apiToken)) {
 *     throw new Exception('Недействительный API токен');
 * }
 * 
 * // Работа с версионированием API
 * $apiVersion = request('api_version', 'v1');
 * $supportedVersions = ['v1', 'v2', 'v3'];
 * if (!in_array($apiVersion, $supportedVersions)) {
 *     throw new Exception('Неподдерживаемая версия API');
 * }
 * 
 * // Работа с локализацией
 * $locale = request('locale', 'en');
 * $timezone = request('timezone', 'UTC');
 * $currency = request('currency', 'USD');
 * 
 * // Установка локализации
 * setlocale(LC_ALL, $locale);
 * date_default_timezone_set($timezone);
 * 
 * // Работа с кэшированием
 * $cacheKey = request('cache_key');
 * $cacheTime = request('cache_time', 3600);
 * $noCache = request('no_cache', false);
 * 
 * if (!$noCache && $cacheKey) {
 *     $cachedData = getFromCache($cacheKey);
 *     if ($cachedData) {
 *         echo $cachedData;
 *         exit;
 *     }
 * }
 * 
 * // Работа с отладкой
 * $debug = request('debug', false);
 * $verbose = request('verbose', false);
 * $trace = request('trace', false);
 * 
 * if ($debug && env('APP_ENV') === 'development') {
 *     ini_set('display_errors', '1');
 *     error_reporting(E_ALL);
 * }
 * 
 * // Логирование запросов
 * $requestLog = [
 *     'timestamp' => date('Y-m-d H:i:s'),
 *     'method' => request()->getMethod(),
 *     'url' => request()->getUrl(),
 *     'ip' => $realIP,
 *     'user_agent' => $userAgent,
 *     'user_id' => $userId,
 *     'params' => request()->all(),
 *     'files_count' => count(request()->files()->all()),
 *     'response_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']
 * ];
 * 
 * // Получение всех данных запроса
 * $allRequestData = request()->all();
 * 
 * // Проверка существования параметра
 * function hasRequestParam($name) {
 *     return request($name) !== null;
 * }
 * 
 * // Получение параметра с валидацией
 * function getValidatedRequestParam($name, $validator, $default = null) {
 *     $value = request($name, $default);
 *     if ($validator($value)) {
 *         return $value;
 *     }
 *     return $default;
 * }
 * 
 * // Пример валидации email
 * $email = getValidatedRequestParam('email', function($value) {
 *     return filter_var($value, FILTER_VALIDATE_EMAIL);
 * });
 * 
 * // Пример валидации числового значения
 * $age = getValidatedRequestParam('age', function($value) {
 *     return is_numeric($value) && $value >= 0 && $value <= 150;
 * }, 18);
 * ```
 */
function request(string|null $key = null, mixed $default = null): mixed
{
    $request = Request::getInstance();
    
    if($key){
        return $request->get($key, $default);
    }
    
    return $request;
}