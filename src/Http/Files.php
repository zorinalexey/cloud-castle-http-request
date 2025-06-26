<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use CloudCastle\HttpRequest\Traits\GetDataTrait;
use CloudCastle\HttpRequest\Traits\GetInstanceTrait;

final class Files
{
    use GetDataTrait, GetInstanceTrait;
    
    private function __construct()
    {
        $files = [];
        
        foreach ($_FILES as $file) {
            if(is_array($file['name'])) {
                foreach ($file as $key => $value) {
                    $data = [
                        'name' => $file['name'][$key],
                        'type' => $file['type'][$key],
                        'tmp_name' => $file['tmp_name'][$key],
                        'error' => $file['error'][$key],
                    ];
                    $files[$data['name']][$key] = new UploadFile($data);
                }
            }else{
                $files[$file['name']] = new UploadFile($file);
            }
        }
        
        $this->data = $files;
    }
}