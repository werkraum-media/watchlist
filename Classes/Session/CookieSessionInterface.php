<?php

namespace WerkraumMedia\Watchlist\Session;

interface CookieSessionInterface
{
    public function getCookieName(): string;

    public function getCookieValue(): string;
}
