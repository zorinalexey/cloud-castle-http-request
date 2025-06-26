<?php

use CloudCastle\HttpRequest\Request;

function request(): Request
{
    return Request::getInstance();
}