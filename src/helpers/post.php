<?php

declare(strict_types=1);

use CloudCastle\HttpRequest\Request;
use CloudCastle\HttpRequest\Http\Post;

/**
 * Глобальная вспомогательная функция для доступа к POST-данным
 * 
 * Предоставляет удобный способ получения POST-параметров из HTTP запроса.
 * Если передан ключ, возвращает значение конкретного POST-параметра,
 * иначе возвращает объект Post для работы со всеми POST-данными.
 * 
 * Функция автоматически обрабатывает различные типы данных, включая JSON,
 * и предоставляет безопасный доступ к данным формы, AJAX запросов и API.
 *
 * @param string|null $key Имя POST-параметра (опционально)
 * @param mixed $default Значение по умолчанию, если параметр не найден
 * @return mixed|Post Значение параметра или объект Post
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 *
 * @example
 * ```php
 * // Получение отдельных POST-параметров
 * $username = post('username');
 * $email = post('email', 'default@example.com');
 * $age = post('age', 18);
 * $isActive = post('is_active', false);
 * 
 * // Получение всех POST-данных
 * $allPostData = post()->all();
 * 
 * // Работа с формой регистрации
 * $userData = [
 *     'username' => post('username'),
 *     'email' => post('email'),
 *     'password' => post('password'),
 *     'confirm_password' => post('confirm_password'),
 *     'terms_accepted' => post('terms_accepted', false)
 * ];
 * 
 * // Валидация обязательных полей
 * $requiredFields = ['username', 'email', 'password'];
 * foreach ($requiredFields as $field) {
 *     if (empty(post($field))) {
 *         throw new Exception("Поле {$field} обязательно для заполнения");
 *     }
 * }
 * 
 * // Работа с JSON данными
 * $jsonData = post('json_data');
 * if (is_string($jsonData)) {
 *     $decodedData = json_decode($jsonData, true);
 * }
 * 
 * // Обработка файлов загрузки
 * $uploadedFiles = post()->files();
 * if (!empty($uploadedFiles)) {
 *     foreach ($uploadedFiles as $file) {
 *         if ($file instanceof UploadFile && $file->isUploaded()) {
 *             $file->save('/uploads/');
 *         }
 *     }
 * }
 * 
 * // Работа с массивами в форме
 * $tags = post('tags', []); // Для полей типа name="tags[]"
 * $categories = post('categories', []);
 * 
 * // Обработка чекбоксов
 * $newsletter = post('newsletter', false);
 * $notifications = post('notifications', false);
 * 
 * // Работа с селектами
 * $country = post('country', 'US');
 * $language = post('language', 'en');
 * 
 * // Обработка радиокнопок
 * $gender = post('gender', 'other');
 * $subscription = post('subscription', 'monthly');
 * 
 * // Работа с текстовыми областями
 * $description = post('description', '');
 * $bio = post('bio', '');
 * 
 * // Обработка скрытых полей
 * $csrfToken = post('csrf_token');
 * $formId = post('form_id');
 * 
 * // Валидация email
 * $email = post('email');
 * if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
 *     throw new Exception('Некорректный email адрес');
 * }
 * 
 * // Валидация числовых значений
 * $age = post('age');
 * if ($age && (!is_numeric($age) || $age < 0 || $age > 150)) {
 *     throw new Exception('Некорректный возраст');
 * }
 * 
 * // Обработка API запросов
 * if (post()->isJson()) {
 *     $apiData = post()->json();
 *     $action = $apiData['action'] ?? '';
 *     $params = $apiData['params'] ?? [];
 * }
 * 
 * // Работа с вложенными данными
 * $address = [
 *     'street' => post('address_street'),
 *     'city' => post('address_city'),
 *     'zip' => post('address_zip'),
 *     'country' => post('address_country')
 * ];
 * 
 * // Обработка множественных файлов
 * $photos = post()->files('photos');
 * if (!empty($photos)) {
 *     foreach ($photos as $photo) {
 *         if ($photo instanceof UploadFile && $photo->isUploaded()) {
 *             $photo->save('/uploads/photos/');
 *         }
 *     }
 * }
 * 
 * // Создание объекта пользователя
 * $user = new User();
 * $user->username = post('username');
 * $user->email = post('email');
 * $user->firstName = post('first_name');
 * $user->lastName = post('last_name');
 * $user->isActive = post('is_active', true);
 * 
 * // Обработка формы с условными полями
 * $hasCompany = post('has_company', false);
 * if ($hasCompany) {
 *     $companyData = [
 *         'name' => post('company_name'),
 *         'position' => post('company_position'),
 *         'website' => post('company_website')
 *     ];
 * }
 * 
 * // Валидация паролей
 * $password = post('password');
 * $confirmPassword = post('confirm_password');
 * 
 * if ($password !== $confirmPassword) {
 *     throw new Exception('Пароли не совпадают');
 * }
 * 
 * if (strlen($password) < 8) {
 *     throw new Exception('Пароль должен содержать минимум 8 символов');
 * }
 * 
 * // Обработка формы поиска
 * $searchData = [
 *     'query' => post('search_query', ''),
 *     'category' => post('search_category', 'all'),
 *     'sort' => post('search_sort', 'relevance'),
 *     'page' => post('search_page', 1),
 *     'limit' => post('search_limit', 20)
 * ];
 * 
 * // Работа с фильтрами
 * $filters = [
 *     'price_min' => post('price_min'),
 *     'price_max' => post('price_max'),
 *     'brands' => post('brands', []),
 *     'colors' => post('colors', []),
 *     'sizes' => post('sizes', [])
 * ];
 * 
 * // Обработка формы комментария
 * $commentData = [
 *     'content' => post('comment_content'),
 *     'parent_id' => post('comment_parent_id'),
 *     'article_id' => post('comment_article_id'),
 *     'rating' => post('comment_rating', 5)
 * ];
 * 
 * // Проверка CSRF токена
 * $csrfToken = post('csrf_token');
 * if (!$csrfToken || !validateCsrfToken($csrfToken)) {
 *     throw new Exception('Недействительный CSRF токен');
 * }
 * 
 * // Обработка формы настроек
 * $settings = [
 *     'notifications' => [
 *         'email' => post('notify_email', true),
 *         'sms' => post('notify_sms', false),
 *         'push' => post('notify_push', true)
 *     ],
 *     'privacy' => [
 *         'profile_public' => post('profile_public', false),
 *         'show_email' => post('show_email', false),
 *         'allow_messages' => post('allow_messages', true)
 *     ],
 *     'display' => [
 *         'theme' => post('theme', 'light'),
 *         'language' => post('language', 'en'),
 *         'timezone' => post('timezone', 'UTC')
 *     ]
 * ];
 * 
 * // Логирование POST-запросов
 * $logData = [
 *     'timestamp' => date('Y-m-d H:i:s'),
 *     'ip' => $_SERVER['REMOTE_ADDR'],
 *     'user_agent' => $_SERVER['HTTP_USER_AGENT'],
 *     'post_data' => post()->all(),
 *     'files_count' => count(post()->files())
 * ];
 * ```
 */
function post(string|null $key = null, mixed $default = null): mixed
{
    $post = Request::getInstance()->post;
    
    if($key){
        return $post->get($key, $default);
    }
    
    return $post;
}