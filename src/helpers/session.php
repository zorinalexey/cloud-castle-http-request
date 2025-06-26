<?php

use CloudCastle\HttpRequest\Http\Session;

function session(): Session
{
    return Session::getInstance();
}