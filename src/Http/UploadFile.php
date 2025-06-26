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
}