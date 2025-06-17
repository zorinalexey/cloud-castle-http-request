<?php

namespace CloudCastle\HttpRequest\Tests\Unit;

class PhpInputMock
{
    /**
     * @var string|null
     */
    public static $inputFile;

    /**
     * @var resource|false|null
     */
    private $handle;

    /**
     * @var resource|null
     */
    public $context;

    /**
     * @param string $path
     * @param string $mode
     * @param int $options
     * @param string|null $opened_path
     * @return bool
     */
    public function stream_open(string $path, string $mode, int $options, ?string &$opened_path): bool
    {
        $this->handle = fopen((string)self::$inputFile, 'r');
        return \is_resource($this->handle);
    }

    /**
     * @param int $count
     * @return string|false
     */
    public function stream_read(int $count): string|false
    {
        if ($count < 1 || !\is_resource($this->handle)) {
            return false;
        }
        return fread($this->handle, $count);
    }

    /**
     * @return bool
     */
    public function stream_eof(): bool
    {
        return \is_resource($this->handle) ? feof($this->handle) : true;
    }

    /**
     * @return array<mixed>
     */
    public function stream_stat(): array
    {
        return [];
    }

    /**
     * @return void
     */
    public function stream_close(): void
    {
        if (\is_resource($this->handle)) {
            fclose($this->handle);
        }
    }
} 