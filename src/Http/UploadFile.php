<?php

declare(strict_types=1);

namespace CloudCastle\HttpRequest\Http;

use stdClass;

final class UploadFile extends stdClass
{
    public function __construct(array $file)
    {
        foreach ($file as $key => $value) {
            $this->{$key} = $value;
        }
    }
    
    public function save(string $directory, ?string $filename = null): string|false
    {
        if (!isset($this->tmp_name) || !is_uploaded_file($this->tmp_name)) {
            return false;
        }
        
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
                return false;
            }
        }
        
        $name = $filename ?? ($this->name ?? uniqid('upload_', true));
        
        if (!pathinfo($name, PATHINFO_EXTENSION) && isset($this->type)) {
            $extension = $this->getExtensionFromMimeType($this->type);
            
            if ($extension) {
                $name .= '.' . $extension;
            }
        }
        
        $target = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;
        
        if (move_uploaded_file($this->tmp_name, $target)) {
            return $target;
        }
        
        return false;
    }
    
    private function getExtensionFromMimeType(string $mimeType): ?string
    {
        $mimeToExtension = require __DIR__ . '/../inc/mime_types.php';
        
        return $mimeToExtension[$mimeType] ?? null;
    }
    
    public function isUploaded(): bool
    {
        return is_uploaded_file($this->tmp_name);
    }
    
    public function getOriginalName(): string
    {
        return $this->name;
    }
    
    public function getSize(): int
    {
        return $this->size;
    }
    
    public function getError(): int
    {
        return $this->error;
    }
    
    public function getMimeType(): string
    {
        return $this->type;
    }
    
    public function getExtension(): string
    {
        return $this->getExtensionFromMimeType($this->type) ?? '';
    }
}