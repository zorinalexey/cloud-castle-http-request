<?php

use CloudCastle\HttpRequest\Http\Cookie;

/**
 * Глобальная вспомогательная функция для доступа к cookie
 * 
 * Предоставляет удобный способ работы с HTTP cookie. Если передан ключ,
 * возвращает значение конкретного cookie, иначе возвращает объект Cookie
 * для работы со всеми cookie, включая установку, удаление и управление.
 * 
 * Функция автоматически обрабатывает различные типы данных и предоставляет
 * безопасный доступ к cookie между запросами и сессиями.
 * 
 * @param string|null $key Имя cookie (опционально)
 * @param mixed $default Значение по умолчанию, если cookie не найден
 * @return mixed|Cookie Значение cookie или объект Cookie
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * 
 * @example
 * ```php
 * // Получение отдельных cookie
 * $token = cookies('auth_token');
 * $language = cookies('language', 'en');
 * $theme = cookies('theme', 'light');
 * $userId = cookies('user_id');
 * 
 * // Получение объекта cookie для работы со всеми cookie
 * $cookie = cookies();
 * 
 * // Работа с токенами авторизации
 * $authToken = cookies('auth_token');
 * $refreshToken = cookies('refresh_token');
 * $apiToken = cookies('api_token');
 * 
 * if ($authToken) {
 *     // Проверка токена авторизации
 *     $user = validateAuthToken($authToken);
 * } else {
 *     // Перенаправление на страницу входа
 *     header('Location: /login');
 *     exit;
 * }
 * 
 * // Установка cookie
 * cookies()->set('auth_token', $token, time() + 3600); // 1 час
 * cookies()->set('user_id', $userId, time() + 86400); // 24 часа
 * cookies()->set('language', 'ru', time() + 2592000); // 30 дней
 * 
 * // Установка cookie с дополнительными параметрами
 * cookies()->set('secure_token', $token, [
 *     'expires' => time() + 3600,
 *     'path' => '/',
 *     'domain' => '.example.com',
 *     'secure' => true,
 *     'httponly' => true,
 *     'samesite' => 'Strict'
 * ]);
 * 
 * // Работа с языковыми настройками
 * $language = cookies('language', 'en');
 * $locale = cookies('locale', 'en_US');
 * 
 * // Изменение языка
 * if (isset($_GET['lang'])) {
 *     $newLanguage = $_GET['lang'];
 *     cookies()->set('language', $newLanguage, time() + 2592000);
 *     cookies()->set('locale', $newLanguage . '_' . strtoupper($newLanguage), time() + 2592000);
 * }
 * 
 * // Работа с темой оформления
 * $theme = cookies('theme', 'light');
 * 
 * if (isset($_GET['theme'])) {
 *     $newTheme = $_GET['theme'];
 *     cookies()->set('theme', $newTheme, time() + 2592000);
 * }
 * 
 * // Работа с настройками пользователя
 * $userPreferences = [
 *     'notifications' => cookies('notifications_enabled', true),
 *     'timezone' => cookies('timezone', 'UTC'),
 *     'date_format' => cookies('date_format', 'Y-m-d'),
 *     'time_format' => cookies('time_format', 'H:i:s')
 * ];
 * 
 * // Сохранение настроек пользователя
 * cookies()->set('notifications_enabled', $userPreferences['notifications'], time() + 2592000);
 * cookies()->set('timezone', $userPreferences['timezone'], time() + 2592000);
 * 
 * // Работа с корзиной покупок
 * $cartId = cookies('cart_id');
 * if (!$cartId) {
 *     $cartId = uniqid('cart_');
 *     cookies()->set('cart_id', $cartId, time() + 604800); // 7 дней
 * }
 * 
 * // Работа с отслеживанием
 * $trackingId = cookies('tracking_id');
 * if (!$trackingId) {
 *     $trackingId = generateTrackingId();
 *     cookies()->set('tracking_id', $trackingId, time() + 31536000); // 1 год
 * }
 * 
 * // Работа с реферальными ссылками
 * $referralCode = cookies('referral_code');
 * $affiliateId = cookies('affiliate_id');
 * 
 * if (isset($_GET['ref'])) {
 *     $referralCode = $_GET['ref'];
 *     cookies()->set('referral_code', $referralCode, time() + 2592000);
 * }
 * 
 * // Работа с A/B тестированием
 * $abTestGroup = cookies('ab_test_group');
 * if (!$abTestGroup) {
 *     $abTestGroup = rand(1, 2) === 1 ? 'A' : 'B';
 *     cookies()->set('ab_test_group', $abTestGroup, time() + 604800);
 * }
 * 
 * // Работа с уведомлениями
 * $notificationSettings = [
 *     'email' => cookies('notify_email', true),
 *     'sms' => cookies('notify_sms', false),
 *     'push' => cookies('notify_push', true)
 * ];
 * 
 * // Сохранение настроек уведомлений
 * foreach ($notificationSettings as $type => $enabled) {
 *     cookies()->set("notify_{$type}", $enabled, time() + 2592000);
 * }
 * 
 * // Работа с фильтрами и поиском
 * $searchHistory = cookies('search_history', []);
 * $lastSearch = cookies('last_search', '');
 * 
 * // Сохранение истории поиска
 * if (!empty($_GET['q'])) {
 *     $searchQuery = $_GET['q'];
 *     if (!in_array($searchQuery, $searchHistory)) {
 *         array_unshift($searchHistory, $searchQuery);
 *         $searchHistory = array_slice($searchHistory, 0, 10); // Ограничиваем 10 запросами
 *         cookies()->set('search_history', json_encode($searchHistory), time() + 2592000);
 *     }
 *     cookies()->set('last_search', $searchQuery, time() + 2592000);
 * }
 * 
 * // Работа с избранными товарами
 * $favorites = cookies('favorites', []);
 * $productId = 123;
 * 
 * if (!in_array($productId, $favorites)) {
 *     $favorites[] = $productId;
 *     cookies()->set('favorites', json_encode($favorites), time() + 2592000);
 * }
 * 
 * // Работа с последними просмотренными товарами
 * $recentlyViewed = cookies('recently_viewed', []);
 * $currentProduct = 456;
 * 
 * if (!in_array($currentProduct, $recentlyViewed)) {
 *     array_unshift($recentlyViewed, $currentProduct);
 *     $recentlyViewed = array_slice($recentlyViewed, 0, 20); // Ограничиваем 20 товарами
 *     cookies()->set('recently_viewed', json_encode($recentlyViewed), time() + 604800);
 * }
 * 
 * // Работа с купонами
 * $appliedCoupons = cookies('applied_coupons', []);
 * $couponCode = 'SAVE10';
 * 
 * if (!in_array($couponCode, $appliedCoupons)) {
 *     $appliedCoupons[] = $couponCode;
 *     cookies()->set('applied_coupons', json_encode($appliedCoupons), time() + 86400);
 * }
 * 
 * // Работа с адресами доставки
 * $shippingAddress = cookies('shipping_address');
 * if ($shippingAddress) {
 *     $shippingAddress = json_decode($shippingAddress, true);
 * }
 * 
 * // Сохранение адреса доставки
 * $address = [
 *     'first_name' => $_POST['first_name'],
 *     'last_name' => $_POST['last_name'],
 *     'address' => $_POST['address'],
 *     'city' => $_POST['city'],
 *     'zip' => $_POST['zip']
 * ];
 * cookies()->set('shipping_address', json_encode($address), time() + 2592000);
 * 
 * // Работа с методами оплаты
 * $paymentMethod = cookies('payment_method', 'credit_card');
 * $paymentData = cookies('payment_data');
 * 
 * if ($paymentData) {
 *     $paymentData = json_decode($paymentData, true);
 * }
 * 
 * // Сохранение метода оплаты
 * cookies()->set('payment_method', $_POST['payment_method'], time() + 2592000);
 * cookies()->set('payment_data', json_encode([
 *     'card_last4' => substr($_POST['card_number'], -4),
 *     'card_type' => $_POST['card_type']
 * ]), time() + 2592000);
 * 
 * // Работа с временными метками
 * $lastVisit = cookies('last_visit');
 * $visitCount = cookies('visit_count', 0);
 * 
 * // Обновление статистики посещений
 * $currentTime = time();
 * cookies()->set('last_visit', $currentTime, time() + 31536000);
 * cookies()->set('visit_count', $visitCount + 1, time() + 31536000);
 * 
 * // Работа с сессиями
 * $sessionId = cookies('session_id');
 * if (!$sessionId) {
 *     $sessionId = session_id();
 *     cookies()->set('session_id', $sessionId, time() + 3600);
 * }
 * 
 * // Работа с CSRF токенами
 * $csrfToken = cookies('csrf_token');
 * if (!$csrfToken) {
 *     $csrfToken = bin2hex(random_bytes(32));
 *     cookies()->set('csrf_token', $csrfToken, time() + 3600);
 * }
 * 
 * // Проверка CSRF токена
 * if ($_POST['csrf_token'] !== cookies('csrf_token')) {
 *     throw new Exception('Недействительный CSRF токен');
 * }
 * 
 * // Работа с аналитикой
 * $analyticsId = cookies('analytics_id');
 * if (!$analyticsId) {
 *     $analyticsId = uniqid('analytics_');
 *     cookies()->set('analytics_id', $analyticsId, time() + 31536000);
 * }
 * 
 * // Работа с персонализацией
 * $personalizationData = [
 *     'age_group' => cookies('age_group'),
 *     'gender' => cookies('gender'),
 *     'interests' => cookies('interests', []),
 *     'location' => cookies('location')
 * ];
 * 
 * // Сохранение данных персонализации
 * if (isset($_POST['age_group'])) {
 *     cookies()->set('age_group', $_POST['age_group'], time() + 2592000);
 * }
 * 
 * // Работа с уведомлениями о cookie
 * $cookieConsent = cookies('cookie_consent');
 * if (!$cookieConsent) {
 *     // Показать уведомление о cookie
 *     showCookieConsent();
 * }
 * 
 * // Сохранение согласия на cookie
 * if (isset($_POST['accept_cookies'])) {
 *     cookies()->set('cookie_consent', 'accepted', time() + 31536000);
 * }
 * 
 * // Удаление cookie
 * cookies()->delete('temp_data');
 * cookies()->delete('old_token');
 * 
 * // Очистка всех cookie при выходе
 * function logout() {
 *     cookies()->delete('auth_token');
 *     cookies()->delete('user_id');
 *     cookies()->delete('session_id');
 *     // Оставляем настройки пользователя (язык, тема и т.д.)
 * }
 * 
 * // Проверка существования cookie
 * function hasCookie($name) {
 *     return cookies($name) !== null;
 * }
 * 
 * // Получение всех cookie
 * $allCookies = cookies()->all();
 * 
 * // Логирование использования cookie
 * $cookieUsage = [
 *     'timestamp' => date('Y-m-d H:i:s'),
 *     'cookies_count' => count(cookies()->all()),
 *     'user_agent' => $_SERVER['HTTP_USER_AGENT']
 * ];
 * 
 * // Работа с безопасными cookie
 * function setSecureCookie($name, $value, $expires = 3600) {
 *     cookies()->set($name, $value, [
 *         'expires' => time() + $expires,
 *         'path' => '/',
 *         'secure' => true,
 *         'httponly' => true,
 *         'samesite' => 'Strict'
 *     ]);
 * }
 * 
 * // Установка безопасного токена
 * setSecureCookie('secure_token', $token, 3600);
 * ```
 */
function cookies(string|null $key = null, mixed $default = null): mixed
{
    $cookie = Cookie::getInstance();
    
    if($key){
        return $cookie->get($key, $default);
    }
    
    return $cookie;
}