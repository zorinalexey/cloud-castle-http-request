<?php

use CloudCastle\HttpRequest\Http\Headers;

/**
 * Глобальная вспомогательная функция для доступа к HTTP-заголовкам
 * 
 * Предоставляет удобный способ работы с HTTP заголовками запроса.
 * Если передан ключ, возвращает значение конкретного заголовка,
 * иначе возвращает объект Headers для работы со всеми заголовками.
 * 
 * Функция автоматически обрабатывает различные типы заголовков и предоставляет
 * безопасный доступ к информации о клиенте, запросе и сервере.
 *
 * @param string|null $key Имя заголовка (опционально)
 * @param mixed $default Значение по умолчанию, если заголовок не найден
 * @return mixed|Headers Значение заголовка или объект Headers
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * 
 * @example
 * ```php
 * // Получение отдельных заголовков
 * $userAgent = headers('User-Agent');
 * $accept = headers('Accept', 'text/html');
 * $authorization = headers('Authorization');
 * $contentType = headers('Content-Type');
 * 
 * // Получение объекта headers для работы со всеми заголовками
 * $headers = headers();
 * 
 * // Работа с User-Agent
 * $userAgent = headers('User-Agent');
 * $isMobile = preg_match('/Mobile|Android|iPhone|iPad/', $userAgent);
 * $isBot = preg_match('/bot|crawler|spider|crawling/', strtolower($userAgent));
 * 
 * // Определение браузера
 * $browser = 'Unknown';
 * if (strpos($userAgent, 'Chrome') !== false) $browser = 'Chrome';
 * elseif (strpos($userAgent, 'Firefox') !== false) $browser = 'Firefox';
 * elseif (strpos($userAgent, 'Safari') !== false) $browser = 'Safari';
 * elseif (strpos($userAgent, 'Edge') !== false) $browser = 'Edge';
 * elseif (strpos($userAgent, 'MSIE') !== false) $browser = 'Internet Explorer';
 * 
 * // Работа с Accept заголовками
 * $accept = headers('Accept');
 * $acceptLanguage = headers('Accept-Language', 'en-US,en;q=0.9');
 * $acceptEncoding = headers('Accept-Encoding', 'gzip, deflate, br');
 * $acceptCharset = headers('Accept-Charset', 'utf-8');
 * 
 * // Определение предпочитаемого языка
 * $languages = explode(',', $acceptLanguage);
 * $preferredLanguage = trim(explode(';', $languages[0])[0]);
 * 
 * // Работа с авторизацией
 * $authorization = headers('Authorization');
 * $bearerToken = null;
 * if ($authorization && strpos($authorization, 'Bearer ') === 0) {
 *     $bearerToken = substr($authorization, 7);
 * }
 * 
 * $basicAuth = null;
 * if ($authorization && strpos($authorization, 'Basic ') === 0) {
 *     $basicAuth = substr($authorization, 6);
 *     $credentials = base64_decode($basicAuth);
 *     list($username, $password) = explode(':', $credentials, 2);
 * }
 * 
 * // Работа с Content-Type
 * $contentType = headers('Content-Type');
 * $isJson = strpos($contentType, 'application/json') === 0;
 * $isFormData = strpos($contentType, 'application/x-www-form-urlencoded') === 0;
 * $isMultipart = strpos($contentType, 'multipart/form-data') === 0;
 * 
 * // Работа с Content-Length
 * $contentLength = headers('Content-Length');
 * $maxContentLength = 10 * 1024 * 1024; // 10MB
 * if ($contentLength && $contentLength > $maxContentLength) {
 *     throw new Exception('Слишком большой размер запроса');
 * }
 * 
 * // Работа с Referer
 * $referer = headers('Referer');
 * $isInternalReferer = $referer && strpos($referer, $_SERVER['HTTP_HOST']) !== false;
 * $isExternalReferer = $referer && !$isInternalReferer;
 * 
 * // Работа с Origin
 * $origin = headers('Origin');
 * $isCorsRequest = !empty($origin);
 * $allowedOrigins = ['https://example.com', 'https://app.example.com'];
 * $isAllowedOrigin = in_array($origin, $allowedOrigins);
 * 
 * // Работа с Host
 * $host = headers('Host');
 * $isSecureHost = strpos($host, 'localhost') === false;
 * 
 * // Работа с Connection
 * $connection = headers('Connection');
 * $isKeepAlive = $connection === 'keep-alive';
 * $isUpgrade = $connection === 'upgrade';
 * 
 * // Проверка WebSocket запроса
 * $isWebSocket = headers('Upgrade') === 'websocket' && $isUpgrade;
 * 
 * // Работа с Cache-Control
 * $cacheControl = headers('Cache-Control');
 * $noCache = strpos($cacheControl, 'no-cache') !== false;
 * $noStore = strpos($cacheControl, 'no-store') !== false;
 * $maxAge = null;
 * if (preg_match('/max-age=(\d+)/', $cacheControl, $matches)) {
 *     $maxAge = (int)$matches[1];
 * }
 * 
 * // Работа с If-Modified-Since
 * $ifModifiedSince = headers('If-Modified-Since');
 * if ($ifModifiedSince) {
 *     $lastModified = strtotime($ifModifiedSince);
 *     $fileModified = filemtime($filePath);
 *     if ($fileModified <= $lastModified) {
 *         http_response_code(304);
 *         exit;
 *     }
 * }
 * 
 * // Работа с If-None-Match
 * $ifNoneMatch = headers('If-None-Match');
 * $etag = '"' . md5_file($filePath) . '"';
 * if ($ifNoneMatch === $etag) {
 *     http_response_code(304);
 *     exit;
 * }
 * 
 * // Работа с X-Forwarded заголовками
 * $forwardedFor = headers('X-Forwarded-For');
 * $forwardedProto = headers('X-Forwarded-Proto');
 * $forwardedHost = headers('X-Forwarded-Host');
 * $realIP = headers('X-Real-IP');
 * 
 * // Получение реального IP адреса
 * $clientIP = $realIP ?: 
 *             ($forwardedFor ? explode(',', $forwardedFor)[0] : null) ?: 
 *             $_SERVER['REMOTE_ADDR'];
 * 
 * // Проверка протокола
 * $isSecure = $forwardedProto === 'https' || $_SERVER['HTTPS'] === 'on';
 * 
 * // Работа с X-Requested-With
 * $requestedWith = headers('X-Requested-With');
 * $isAjax = $requestedWith === 'XMLHttpRequest';
 * 
 * // Работа с X-CSRF-Token
 * $csrfToken = headers('X-CSRF-Token');
 * if ($csrfToken && $csrfToken !== session('csrf_token')) {
 *     throw new Exception('Недействительный CSRF токен');
 * }
 * 
 * // Работа с X-API-Key
 * $apiKey = headers('X-API-Key');
 * if ($apiKey && !validateApiKey($apiKey)) {
 *     throw new Exception('Недействительный API ключ');
 * }
 * 
 * // Работа с X-API-Version
 * $apiVersion = headers('X-API-Version', 'v1');
 * $supportedVersions = ['v1', 'v2', 'v3'];
 * if (!in_array($apiVersion, $supportedVersions)) {
 *     throw new Exception('Неподдерживаемая версия API');
 * }
 * 
 * // Работа с X-Client-Version
 * $clientVersion = headers('X-Client-Version');
 * $minClientVersion = '1.0.0';
 * if ($clientVersion && version_compare($clientVersion, $minClientVersion, '<')) {
 *     throw new Exception('Требуется обновление клиента');
 * }
 * 
 * // Работа с X-Device-Info
 * $deviceInfo = headers('X-Device-Info');
 * if ($deviceInfo) {
 *     $deviceData = json_decode($deviceInfo, true);
 *     $deviceType = $deviceData['type'] ?? 'unknown';
 *     $osVersion = $deviceData['os_version'] ?? '';
 *     $appVersion = $deviceData['app_version'] ?? '';
 * }
 * 
 * // Работа с X-Timezone
 * $timezone = headers('X-Timezone', 'UTC');
 * date_default_timezone_set($timezone);
 * 
 * // Работа с X-Language
 * $language = headers('X-Language', 'en');
 * $supportedLanguages = ['en', 'ru', 'es', 'fr'];
 * if (!in_array($language, $supportedLanguages)) {
 *     $language = 'en';
 * }
 * 
 * // Работа с X-Theme
 * $theme = headers('X-Theme', 'light');
 * $supportedThemes = ['light', 'dark', 'auto'];
 * if (!in_array($theme, $supportedThemes)) {
 *     $theme = 'light';
 * }
 * 
 * // Работа с X-Debug
 * $debug = headers('X-Debug', false);
 * if ($debug && env('APP_ENV') === 'development') {
 *     ini_set('display_errors', '1');
 *     error_reporting(E_ALL);
 * }
 * 
 * // Работа с X-Trace-Id
 * $traceId = headers('X-Trace-Id');
 * if (!$traceId) {
 *     $traceId = uniqid('trace_', true);
 * }
 * 
 * // Работа с X-Correlation-Id
 * $correlationId = headers('X-Correlation-Id');
 * if (!$correlationId) {
 *     $correlationId = uniqid('corr_', true);
 * }
 * 
 * // Работа с X-Request-Id
 * $requestId = headers('X-Request-Id');
 * if (!$requestId) {
 *     $requestId = uniqid('req_', true);
 * }
 * 
 * // Работа с X-Session-Id
 * $sessionId = headers('X-Session-Id');
 * if ($sessionId && $sessionId !== session_id()) {
 *     session_id($sessionId);
 *     session_start();
 * }
 * 
 * // Работа с X-User-Id
 * $userId = headers('X-User-Id');
 * if ($userId && is_numeric($userId)) {
 *     $currentUser = User::find($userId);
 * }
 * 
 * // Работа с X-Role
 * $role = headers('X-Role');
 * $allowedRoles = ['admin', 'moderator', 'user'];
 * if ($role && !in_array($role, $allowedRoles)) {
 *     throw new Exception('Недостаточно прав');
 * }
 * 
 * // Работа с X-Permissions
 * $permissions = headers('X-Permissions');
 * if ($permissions) {
 *     $userPermissions = json_decode($permissions, true);
 *     $hasPermission = in_array('read:users', $userPermissions);
 * }
 * 
 * // Работа с X-Rate-Limit
 * $rateLimit = headers('X-Rate-Limit');
 * if ($rateLimit) {
 *     $rateLimitData = json_decode($rateLimit, true);
 *     $remaining = $rateLimitData['remaining'] ?? 0;
 *     $reset = $rateLimitData['reset'] ?? 0;
 *     
 *     if ($remaining <= 0) {
 *         http_response_code(429);
 *         exit;
 *     }
 * }
 * 
 * // Работа с X-Client-IP
 * $clientIP = headers('X-Client-IP');
 * if ($clientIP) {
 *     // Логирование IP адреса клиента
 *     logClientIP($clientIP);
 * }
 * 
 * // Работа с X-Forwarded-Port
 * $forwardedPort = headers('X-Forwarded-Port');
 * $port = $forwardedPort ?: $_SERVER['SERVER_PORT'];
 * 
 * // Работа с X-Forwarded-Server
 * $forwardedServer = headers('X-Forwarded-Server');
 * $serverName = $forwardedServer ?: $_SERVER['SERVER_NAME'];
 * 
 * // Работа с X-Original-URL
 * $originalUrl = headers('X-Original-URL');
 * if ($originalUrl) {
 *     // Обработка оригинального URL
 *     $parsedUrl = parse_url($originalUrl);
 * }
 * 
 * // Работа с X-Rewrite-URL
 * $rewriteUrl = headers('X-Rewrite-URL');
 * if ($rewriteUrl) {
 *     // Обработка переписанного URL
 *     $_SERVER['REQUEST_URI'] = $rewriteUrl;
 * }
 * 
 * // Создание заголовков ответа
 * function setResponseHeaders($data) {
 *     $headers = headers();
 *     foreach ($data as $name => $value) {
 *         $headers->set($name, $value);
 *     }
 * }
 * 
 * // Установка CORS заголовков
 * if ($isCorsRequest) {
 *     header('Access-Control-Allow-Origin: ' . $origin);
 *     header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
 *     header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');
 *     header('Access-Control-Allow-Credentials: true');
 * }
 * 
 * // Установка кэш заголовков
 * if (!$noCache) {
 *     header('Cache-Control: public, max-age=3600');
 *     header('ETag: "' . md5($content) . '"');
 *     header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
 * }
 * 
 * // Логирование заголовков
 * $headerLog = [
 *     'timestamp' => date('Y-m-d H:i:s'),
 *     'user_agent' => headers('User-Agent'),
 *     'accept_language' => headers('Accept-Language'),
 *     'content_type' => headers('Content-Type'),
 *     'authorization' => !empty(headers('Authorization')),
 *     'is_ajax' => $isAjax,
 *     'is_mobile' => $isMobile,
 *     'is_bot' => $isBot,
 *     'client_ip' => $clientIP,
 *     'trace_id' => $traceId
 * ];
 * 
 * // Получение всех заголовков
 * $allHeaders = headers()->all();
 * 
 * // Проверка существования заголовка
 * function hasHeader($name) {
 *     return headers($name) !== null;
 * }
 * 
 * // Получение заголовка с валидацией
 * function getValidatedHeader($name, $validator, $default = null) {
 *     $value = headers($name, $default);
 *     if ($validator($value)) {
 *         return $value;
 *     }
 *     return $default;
 * }
 * 
 * // Пример валидации email заголовка
 * $email = getValidatedHeader('X-User-Email', function($value) {
 *     return filter_var($value, FILTER_VALIDATE_EMAIL);
 * });
 * ```
 */
function headers(string|null $key = null, mixed $default = null): mixed
{
    $headers = Headers::getInstance();
    
    if($key){
        return $headers->get($key, $default);
    }
    
    return $headers;
}