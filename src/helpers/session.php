<?php

use CloudCastle\HttpRequest\Http\Session;

/**
 * Глобальная вспомогательная функция для доступа к сессии пользователя
 * 
 * Предоставляет удобный способ работы с сессиями PHP. Если передан ключ,
 * возвращает значение из сессии, иначе возвращает объект Session для
 * работы со всей сессией, включая установку, удаление и управление данными.
 * 
 * Функция автоматически инициализирует сессию при необходимости и предоставляет
 * безопасный доступ к данным пользователя между запросами.
 * 
 * @param string|null $key Имя ключа сессии (опционально)
 * @param mixed $default Значение по умолчанию, если ключ не найден
 * @return mixed|Session Значение из сессии или объект Session
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * 
 * @example
 * ```php
 * // Получение отдельных значений из сессии
 * $userId = session('user_id');
 * $username = session('username', 'guest');
 * $isLoggedIn = session('is_logged_in', false);
 * $lastActivity = session('last_activity');
 * 
 * // Получение объекта сессии для работы со всеми данными
 * $session = session();
 * 
 * // Работа с аутентификацией пользователя
 * $userData = [
 *     'id' => session('user_id'),
 *     'username' => session('username'),
 *     'email' => session('user_email'),
 *     'role' => session('user_role', 'user'),
 *     'permissions' => session('user_permissions', [])
 * ];
 * 
 * // Проверка авторизации
 * if (session('is_logged_in', false)) {
 *     // Пользователь авторизован
 *     $currentUser = new User(session('user_id'));
 * } else {
 *     // Перенаправление на страницу входа
 *     header('Location: /login');
 *     exit;
 * }
 * 
 * // Установка данных в сессию
 * session()->set('user_id', 123);
 * session()->set('username', 'john_doe');
 * session()->set('user_email', 'john@example.com');
 * session()->set('is_logged_in', true);
 * session()->set('login_time', time());
 * 
 * // Работа с корзиной покупок
 * $cart = session('cart', []);
 * $cartItems = session('cart_items', 0);
 * $cartTotal = session('cart_total', 0.00);
 * 
 * // Добавление товара в корзину
 * $cart = session('cart', []);
 * $cart[] = [
 *     'product_id' => 456,
 *     'name' => 'Product Name',
 *     'price' => 29.99,
 *     'quantity' => 1
 * ];
 * session()->set('cart', $cart);
 * session()->set('cart_items', count($cart));
 * 
 * // Работа с настройками пользователя
 * $userSettings = session('user_settings', [
 *     'theme' => 'light',
 *     'language' => 'en',
 *     'notifications' => true,
 *     'timezone' => 'UTC'
 * ]);
 * 
 * // Обновление настроек
 * $userSettings['theme'] = 'dark';
 * session()->set('user_settings', $userSettings);
 * 
 * // Работа с временными данными (flash messages)
 * $flashMessages = session('flash_messages', []);
 * foreach ($flashMessages as $message) {
 *     echo "<div class='alert'>{$message}</div>";
 * }
 * session()->delete('flash_messages');
 * 
 * // Установка flash сообщения
 * session()->set('flash_messages', ['Успешно сохранено!']);
 * 
 * // Работа с историей просмотров
 * $viewHistory = session('view_history', []);
 * $currentPage = '/products/123';
 * 
 * if (!in_array($currentPage, $viewHistory)) {
 *     array_unshift($viewHistory, $currentPage);
 *     $viewHistory = array_slice($viewHistory, 0, 10); // Ограничиваем 10 страницами
 *     session()->set('view_history', $viewHistory);
 * }
 * 
 * // Работа с избранными товарами
 * $favorites = session('favorites', []);
 * $productId = 789;
 * 
 * if (!in_array($productId, $favorites)) {
 *     $favorites[] = $productId;
 *     session()->set('favorites', $favorites);
 * }
 * 
 * // Работа с формой (сохранение данных при ошибке валидации)
 * $formData = session('form_data', []);
 * $errors = session('form_errors', []);
 * 
 * if (!empty($errors)) {
 *     // Отображение ошибок и восстановление данных формы
 *     foreach ($errors as $field => $error) {
 *         echo "<div class='error'>{$error}</div>";
 *     }
 *     // Восстановление данных формы
 *     $username = $formData['username'] ?? '';
 *     $email = $formData['email'] ?? '';
 * }
 * 
 * // Сохранение данных формы при ошибке
 * session()->set('form_data', [
 *     'username' => $_POST['username'],
 *     'email' => $_POST['email']
 * ]);
 * session()->set('form_errors', $validationErrors);
 * 
 * // Работа с CSRF токенами
 * $csrfToken = session('csrf_token');
 * if (!$csrfToken) {
 *     $csrfToken = bin2hex(random_bytes(32));
 *     session()->set('csrf_token', $csrfToken);
 * }
 * 
 * // Проверка CSRF токена
 * if ($_POST['csrf_token'] !== session('csrf_token')) {
 *     throw new Exception('Недействительный CSRF токен');
 * }
 * 
 * // Работа с временными метками
 * $lastActivity = session('last_activity');
 * $currentTime = time();
 * 
 * if ($lastActivity && ($currentTime - $lastActivity) > 1800) {
 *     // Сессия истекла (30 минут)
 *     session()->destroy();
 *     header('Location: /login?expired=1');
 *     exit;
 * }
 * 
 * session()->set('last_activity', $currentTime);
 * 
 * // Работа с языковыми настройками
 * $language = session('language', 'en');
 * $locale = session('locale', 'en_US');
 * 
 * // Изменение языка
 * if (isset($_GET['lang'])) {
 *     $newLanguage = $_GET['lang'];
 *     session()->set('language', $newLanguage);
 *     session()->set('locale', $newLanguage . '_' . strtoupper($newLanguage));
 * }
 * 
 * // Работа с темой оформления
 * $theme = session('theme', 'light');
 * 
 * if (isset($_GET['theme'])) {
 *     $newTheme = $_GET['theme'];
 *     session()->set('theme', $newTheme);
 * }
 * 
 * // Работа с уведомлениями
 * $notifications = session('notifications', []);
 * 
 * // Добавление уведомления
 * $notifications[] = [
 *     'id' => uniqid(),
 *     'type' => 'info',
 *     'message' => 'Новое сообщение',
 *     'timestamp' => time(),
 *     'read' => false
 * ];
 * session()->set('notifications', $notifications);
 * 
 * // Работа с фильтрами и поиском
 * $searchFilters = session('search_filters', []);
 * $lastSearch = session('last_search', '');
 * 
 * // Сохранение фильтров поиска
 * session()->set('search_filters', [
 *     'category' => $_GET['category'],
 *     'price_min' => $_GET['price_min'],
 *     'price_max' => $_GET['price_max'],
 *     'brand' => $_GET['brand']
 * ]);
 * session()->set('last_search', $_GET['q']);
 * 
 * // Работа с пагинацией
 * $currentPage = session('current_page', 1);
 * $itemsPerPage = session('items_per_page', 20);
 * 
 * // Сохранение состояния пагинации
 * session()->set('current_page', $_GET['page'] ?? 1);
 * 
 * // Работа с сортировкой
 * $sortField = session('sort_field', 'created_at');
 * $sortOrder = session('sort_order', 'desc');
 * 
 * // Сохранение настроек сортировки
 * session()->set('sort_field', $_GET['sort_by'] ?? 'created_at');
 * session()->set('sort_order', $_GET['sort_order'] ?? 'desc');
 * 
 * // Работа с корзиной сравнения
 * $compareList = session('compare_list', []);
 * 
 * // Добавление товара для сравнения
 * if (!in_array($productId, $compareList)) {
 *     $compareList[] = $productId;
 *     session()->set('compare_list', $compareList);
 * }
 * 
 * // Работа с купонами и скидками
 * $appliedCoupons = session('applied_coupons', []);
 * $discountAmount = session('discount_amount', 0.00);
 * 
 * // Применение купона
 * $couponCode = 'SAVE10';
 * if (!in_array($couponCode, $appliedCoupons)) {
 *     $appliedCoupons[] = $couponCode;
 *     session()->set('applied_coupons', $appliedCoupons);
 *     session()->set('discount_amount', 10.00);
 * }
 * 
 * // Работа с адресами доставки
 * $shippingAddress = session('shipping_address', []);
 * $billingAddress = session('billing_address', []);
 * 
 * // Сохранение адреса доставки
 * session()->set('shipping_address', [
 *     'first_name' => $_POST['shipping_first_name'],
 *     'last_name' => $_POST['shipping_last_name'],
 *     'address' => $_POST['shipping_address'],
 *     'city' => $_POST['shipping_city'],
 *     'zip' => $_POST['shipping_zip'],
 *     'country' => $_POST['shipping_country']
 * ]);
 * 
 * // Работа с методами оплаты
 * $paymentMethod = session('payment_method', 'credit_card');
 * $paymentData = session('payment_data', []);
 * 
 * // Сохранение метода оплаты
 * session()->set('payment_method', $_POST['payment_method']);
 * session()->set('payment_data', [
 *     'card_last4' => substr($_POST['card_number'], -4),
 *     'card_type' => $_POST['card_type']
 * ]);
 * 
 * // Очистка сессии при выходе
 * function logout() {
 *     session()->destroy();
 *     header('Location: /login');
 *     exit;
 * }
 * 
 * // Проверка прав доступа
 * function checkPermission($permission) {
 *     $userPermissions = session('user_permissions', []);
 *     return in_array($permission, $userPermissions);
 * }
 * 
 * // Работа с временными данными
 * function setTempData($key, $value, $expiry = 300) {
 *     session()->set("temp_{$key}", [
 *         'value' => $value,
 *         'expires' => time() + $expiry
 *     ]);
 * }
 * 
 * function getTempData($key, $default = null) {
 *     $tempData = session("temp_{$key}");
 *     if ($tempData && $tempData['expires'] > time()) {
 *         return $tempData['value'];
 *     }
 *     session()->delete("temp_{$key}");
 *     return $default;
 * }
 * 
 * // Логирование действий пользователя
 * $userActions = session('user_actions', []);
 * $userActions[] = [
 *     'action' => 'view_product',
 *     'product_id' => 123,
 *     'timestamp' => time()
 * ];
 * session()->set('user_actions', array_slice($userActions, -50)); // Храним последние 50 действий
 * ```
 */
function session(string|null $key = null, mixed $default = null): mixed
{
    $session = Session::getInstance();
    
    if($key){
        return $session->get($key, $default);
    }
    
    return $session;
}