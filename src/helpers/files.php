<?php

use CloudCastle\HttpRequest\Http\Files;

/**
 * Глобальная вспомогательная функция для доступа к загруженным файлам
 * 
 * Предоставляет удобный способ работы с файлами, загруженными через HTTP запрос.
 * Если передано имя файла, возвращает объект UploadFile для конкретного файла,
 * иначе возвращает объект Files для работы со всеми загруженными файлами.
 * 
 * Функция автоматически обрабатывает различные типы файлов и предоставляет
 * безопасный доступ к загруженным файлам, включая валидацию и сохранение.
 *
 * @param string|null $name Имя файла (опционально)
 * @return mixed|Files|UploadFile Объект UploadFile, Files или null
 * @since 1.0.0
 * @author Зорин Алексей <zorinalexey59292@gmail.com>
 * 
 * @example
 * ```php
 * // Получение конкретного файла
 * $avatar = files('avatar');
 * $document = files('document');
 * $photo = files('photo');
 * 
 * // Получение объекта files для работы со всеми файлами
 * $allFiles = files();
 * 
 * // Работа с аватаром пользователя
 * $avatar = files('avatar');
 * if ($avatar && $avatar->isUploaded()) {
 *     // Валидация размера файла
 *     if ($avatar->getSize() > 5 * 1024 * 1024) { // 5MB
 *         throw new Exception('Файл слишком большой');
 *     }
 *     
 *     // Валидация типа файла
 *     $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
 *     if (!in_array($avatar->getMimeType(), $allowedTypes)) {
 *         throw new Exception('Неподдерживаемый тип файла');
 *     }
 *     
 *     // Сохранение файла
 *     $userId = session('user_id');
 *     $path = $avatar->save('/uploads/avatars/', "user_{$userId}_avatar.jpg");
 *     
 *     if ($path) {
 *         // Обновление профиля пользователя
 *         $user = User::find($userId);
 *         $user->avatar_path = $path;
 *         $user->save();
 *     }
 * }
 * 
 * // Работа с документами
 * $document = files('document');
 * if ($document && $document->isUploaded()) {
 *     // Проверка расширения
 *     $allowedExtensions = ['pdf', 'doc', 'docx', 'txt'];
 *     if (!in_array($document->getExtension(), $allowedExtensions)) {
 *         throw new Exception('Неподдерживаемое расширение файла');
 *     }
 *     
 *     // Генерация уникального имени
 *     $fileName = uniqid('doc_') . '.' . $document->getExtension();
 *     $path = $document->save('/uploads/documents/', $fileName);
 *     
 *     // Сохранение информации о документе в базе данных
 *     $fileRecord = new FileRecord([
 *         'original_name' => $document->getOriginalName(),
 *         'stored_name' => $fileName,
 *         'path' => $path,
 *         'size' => $document->getSize(),
 *         'mime_type' => $document->getMimeType(),
 *         'uploaded_by' => session('user_id')
 *     ]);
 *     $fileRecord->save();
 * }
 * 
 * // Работа с множественными файлами
 * $photos = files('photos');
 * if ($photos && is_array($photos)) {
 *     $uploadedPhotos = [];
 *     
 *     foreach ($photos as $photo) {
 *         if ($photo && $photo->isUploaded()) {
 *             // Валидация каждого файла
 *             if ($photo->getSize() > 10 * 1024 * 1024) { // 10MB
 *                 continue; // Пропускаем слишком большие файлы
 *             }
 *             
 *             $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
 *             if (!in_array($photo->getMimeType(), $allowedTypes)) {
 *                 continue; // Пропускаем неподдерживаемые типы
 *             }
 *             
 *             // Сохранение с уникальным именем
 *             $fileName = uniqid('photo_') . '.' . $photo->getExtension();
 *             $path = $photo->save('/uploads/photos/', $fileName);
 *             
 *             if ($path) {
 *                 $uploadedPhotos[] = [
 *                     'original_name' => $photo->getOriginalName(),
 *                     'stored_name' => $fileName,
 *                     'path' => $path,
 *                     'size' => $photo->getSize(),
 *                     'mime_type' => $photo->getMimeType()
 *                 ];
 *             }
 *         }
 *     }
 *     
 *     // Сохранение информации о загруженных фотографиях
 *     foreach ($uploadedPhotos as $photoData) {
 *         $photoRecord = new PhotoRecord($photoData);
 *         $photoRecord->save();
 *     }
 * }
 * 
 * // Работа с файлами для галереи
 * $galleryFiles = files('gallery');
 * if ($galleryFiles && is_array($galleryFiles)) {
 *     $galleryId = post('gallery_id');
 *     
 *     foreach ($galleryFiles as $file) {
 *         if ($file && $file->isUploaded()) {
 *             // Создание миниатюры
 *             $thumbnailPath = createThumbnail($file, 300, 300);
 *             
 *             // Сохранение оригинального файла
 *             $originalPath = $file->save('/uploads/gallery/originals/');
 *             
 *             // Сохранение в базе данных
 *             $galleryItem = new GalleryItem([
 *                 'gallery_id' => $galleryId,
 *                 'original_path' => $originalPath,
 *                 'thumbnail_path' => $thumbnailPath,
 *                 'title' => $file->getOriginalName(),
 *                 'description' => post('description', ''),
 *                 'uploaded_by' => session('user_id')
 *             ]);
 *             $galleryItem->save();
 *         }
 *     }
 * }
 * 
 * // Работа с файлами для блога
 * $blogImages = files('blog_images');
 * if ($blogImages && is_array($blogImages)) {
 *     $blogPostId = post('blog_post_id');
 *     
 *     foreach ($blogImages as $image) {
 *         if ($image && $image->isUploaded()) {
 *             // Оптимизация изображения для веба
 *             $optimizedPath = optimizeImage($image, [
 *                 'quality' => 85,
 *                 'max_width' => 1200,
 *                 'max_height' => 800
 *             ]);
 *             
 *             // Сохранение оптимизированного изображения
 *             $path = $image->save('/uploads/blog/', null, $optimizedPath);
 *             
 *             // Добавление изображения к посту
 *             $blogImage = new BlogImage([
 *                 'post_id' => $blogPostId,
 *                 'image_path' => $path,
 *                 'alt_text' => post('alt_text', ''),
 *                 'caption' => post('caption', '')
 *             ]);
 *             $blogImage->save();
 *         }
 *     }
 * }
 * 
 * // Работа с файлами для продуктов
 * $productImages = files('product_images');
 * if ($productImages && is_array($productImages)) {
 *     $productId = post('product_id');
 *     
 *     foreach ($productImages as $index => $image) {
 *         if ($image && $image->isUploaded()) {
 *             // Определение типа изображения (основное, дополнительное)
 *             $isMain = $index === 0; // Первое изображение - основное
 *             
 *             // Создание разных размеров
 *             $sizes = [
 *                 'thumb' => [150, 150],
 *                 'small' => [300, 300],
 *                 'medium' => [600, 600],
 *                 'large' => [1200, 1200]
 *             ];
 *             
 *             $imagePaths = [];
 *             foreach ($sizes as $size => $dimensions) {
 *                 $imagePaths[$size] = createThumbnail($image, $dimensions[0], $dimensions[1]);
 *             }
 *             
 *             // Сохранение оригинального файла
 *             $originalPath = $image->save('/uploads/products/originals/');
 *             
 *             // Сохранение в базе данных
 *             $productImage = new ProductImage([
 *                 'product_id' => $productId,
 *                 'original_path' => $originalPath,
 *                 'thumb_path' => $imagePaths['thumb'],
 *                 'small_path' => $imagePaths['small'],
 *                 'medium_path' => $imagePaths['medium'],
 *                 'large_path' => $imagePaths['large'],
 *                 'is_main' => $isMain,
 *                 'sort_order' => $index
 *             ]);
 *             $productImage->save();
 *         }
 *     }
 * }
 * 
 * // Работа с файлами для резюме
 * $resume = files('resume');
 * if ($resume && $resume->isUploaded()) {
 *     // Валидация типа файла
 *     $allowedTypes = ['application/pdf', 'application/msword', 
 *                      'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
 *     if (!in_array($resume->getMimeType(), $allowedTypes)) {
 *         throw new Exception('Резюме должно быть в формате PDF или Word');
 *     }
 *     
 *     // Валидация размера (максимум 5MB)
 *     if ($resume->getSize() > 5 * 1024 * 1024) {
 *         throw new Exception('Размер файла резюме не должен превышать 5MB');
 *     }
 *     
 *     // Сохранение с безопасным именем
 *     $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $resume->getOriginalName());
 *     $path = $resume->save('/uploads/resumes/', $safeName);
 *     
 *     // Обновление профиля пользователя
 *     $userId = session('user_id');
 *     $user = User::find($userId);
 *     $user->resume_path = $path;
 *     $user->resume_uploaded_at = now();
 *     $user->save();
 * }
 * 
 * // Работа с файлами для заказов
 * $orderFiles = files('order_files');
 * if ($orderFiles && is_array($orderFiles)) {
 *     $orderId = post('order_id');
 *     
 *     foreach ($orderFiles as $file) {
 *         if ($file && $file->isUploaded()) {
 *             // Проверка ошибок загрузки
 *             if ($file->getError() !== UPLOAD_ERR_OK) {
 *                 continue; // Пропускаем файлы с ошибками
 *             }
 *             
 *             // Сохранение файла
 *             $path = $file->save('/uploads/orders/');
 *             
 *             // Создание записи о файле заказа
 *             $orderFile = new OrderFile([
 *                 'order_id' => $orderId,
 *                 'file_path' => $path,
 *                 'original_name' => $file->getOriginalName(),
 *                 'file_size' => $file->getSize(),
 *                 'mime_type' => $file->getMimeType(),
 *                 'uploaded_by' => session('user_id')
 *             ]);
 *             $orderFile->save();
 *         }
 *     }
 * }
 * 
 * // Работа с файлами для сообщений
 * $messageAttachments = files('attachments');
 * if ($messageAttachments && is_array($messageAttachments)) {
 *     $messageId = post('message_id');
 *     
 *     foreach ($messageAttachments as $attachment) {
 *         if ($attachment && $attachment->isUploaded()) {
 *             // Проверка безопасности файла
 *             if (!isFileSafe($attachment)) {
 *                 continue; // Пропускаем потенциально опасные файлы
 *             }
 *             
 *             // Сохранение вложения
 *             $path = $attachment->save('/uploads/messages/attachments/');
 *             
 *             // Создание записи о вложении
 *             $messageAttachment = new MessageAttachment([
 *                 'message_id' => $messageId,
 *                 'file_path' => $path,
 *                 'original_name' => $attachment->getOriginalName(),
 *                 'file_size' => $attachment->getSize(),
 *                 'mime_type' => $attachment->getMimeType()
 *             ]);
 *             $messageAttachment->save();
 *         }
 *     }
 * }
 * 
 * // Получение всех загруженных файлов
 * $allFiles = files()->all();
 * 
 * // Подсчет общего размера загруженных файлов
 * $totalSize = 0;
 * foreach ($allFiles as $file) {
 *     if ($file && $file->isUploaded()) {
 *         $totalSize += $file->getSize();
 *     }
 * }
 * 
 * // Проверка лимита размера
 * $maxTotalSize = 50 * 1024 * 1024; // 50MB
 * if ($totalSize > $maxTotalSize) {
 *     throw new Exception('Общий размер файлов превышает лимит');
 * }
 * 
 * // Проверка количества файлов
 * $fileCount = count(array_filter($allFiles, function($file) {
 *     return $file && $file->isUploaded();
 * }));
 * 
 * $maxFileCount = 10;
 * if ($fileCount > $maxFileCount) {
 *     throw new Exception('Превышено максимальное количество файлов');
 * }
 * 
 * // Функция для проверки безопасности файла
 * function isFileSafe($file) {
 *     // Проверка расширения
 *     $dangerousExtensions = ['php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'bat', 'cmd'];
 *     if (in_array(strtolower($file->getExtension()), $dangerousExtensions)) {
 *         return false;
 *     }
 *     
 *     // Проверка MIME-типа
 *     $dangerousMimeTypes = ['application/x-php', 'application/x-executable'];
 *     if (in_array($file->getMimeType(), $dangerousMimeTypes)) {
 *         return false;
 *     }
 *     
 *     return true;
 * }
 * 
 * // Функция для создания миниатюры
 * function createThumbnail($file, $width, $height) {
 *     $image = imagecreatefromstring(file_get_contents($file->tmp_name));
 *     $thumbnail = imagecreatetruecolor($width, $height);
 *     
 *     imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));
 *     
 *     $thumbnailPath = '/uploads/thumbnails/' . uniqid() . '.jpg';
 *     imagejpeg($thumbnail, $thumbnailPath, 85);
 *     
 *     imagedestroy($image);
 *     imagedestroy($thumbnail);
 *     
 *     return $thumbnailPath;
 * }
 * 
 * // Функция для оптимизации изображения
 * function optimizeImage($file, $options = []) {
 *     $quality = $options['quality'] ?? 85;
 *     $maxWidth = $options['max_width'] ?? 1200;
 *     $maxHeight = $options['max_height'] ?? 800;
 *     
 *     $image = imagecreatefromstring(file_get_contents($file->tmp_name));
 *     $originalWidth = imagesx($image);
 *     $originalHeight = imagesy($image);
 *     
 *     // Вычисление новых размеров
 *     $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
 *     $newWidth = $originalWidth * $ratio;
 *     $newHeight = $originalHeight * $ratio;
 *     
 *     $optimized = imagecreatetruecolor($newWidth, $newHeight);
 *     imagecopyresampled($optimized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
 *     
 *     $optimizedPath = '/uploads/optimized/' . uniqid() . '.jpg';
 *     imagejpeg($optimized, $optimizedPath, $quality);
 *     
 *     imagedestroy($image);
 *     imagedestroy($optimized);
 *     
 *     return $optimizedPath;
 * }
 * 
 * // Логирование загрузки файлов
 * $uploadLog = [
 *     'timestamp' => date('Y-m-d H:i:s'),
 *     'user_id' => session('user_id'),
 *     'files_count' => $fileCount,
 *     'total_size' => $totalSize,
 *     'ip_address' => $_SERVER['REMOTE_ADDR'],
 *     'user_agent' => $_SERVER['HTTP_USER_AGENT']
 * ];
 * ```
 */
function files(string|null $name): mixed
{
    $files = Files::getInstance();
    
    if($name){
        foreach ($files->all() as $key => $value) {
            if($key === $name){
                return $value;
            }
        }
    }
    
    return $files;
}