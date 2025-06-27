<?php

use CloudCastle\HttpRequest\Http\Files;

/**
 * Глобальная вспомогательная функция для доступа к загруженным файлам.
 *
 * Если передано имя, возвращает объект UploadFile по имени, иначе возвращает объект Files для доступа ко всем файлам.
 *
 * @param string|null $name Имя файла (опционально)
 * @return mixed|Files|UploadFile Объект UploadFile, Files или null
 *
 * Пример использования:
 * <code>
 * $file = files('avatar');
 * if ($file) {
 *     $file->save('/tmp/uploads');
 * }
 * $all = files()->all();
 * </code>
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