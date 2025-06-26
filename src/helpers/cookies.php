<?php

use CloudCastle\HttpRequest\Http\Cookie;

function cookies(): Cookie
{
    return Cookie::getInstance();
}