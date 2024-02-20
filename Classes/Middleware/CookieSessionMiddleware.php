<?php

declare(strict_types=1);

/*
 * Copyright (C) 2022 Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

namespace WerkraumMedia\Watchlist\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use TYPO3\CMS\Core\Http\NormalizedParams;
use WerkraumMedia\Watchlist\Session\CookieSessionService;

class CookieSessionMiddleware implements MiddlewareInterface
{
    private CookieSessionService $cookieSession;

    public function __construct(
        CookieSessionService $cookieSession
    ) {
        $this->cookieSession = $cookieSession;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $response = $handler->handle($request);

        if ($this->shouldAddCookie($request)) {
            return $this->addCookie($response, $request);
        }
        if ($this->shouldRemoveCookie($request)) {
            return $this->removeCookie($response, $request);
        }

        return $response;
    }

    private function shouldAddCookie(ServerRequestInterface $request): bool
    {
        return $this->cookieSession->getCookieValue() !== '';
    }

    private function addCookie(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): ResponseInterface {
        return $response->withAddedHeader(
            'Set-Cookie',
            $this->getCookie($request)->__toString()
        );
    }

    private function shouldRemoveCookie(ServerRequestInterface $request): bool
    {
        $cookieName = $this->cookieSession->getCookieName();

        return $this->cookieSession->getCookieValue() === ''
            && isset($request->getCookieParams()[$cookieName])
        ;
    }

    private function removeCookie(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): ResponseInterface {
        $cookie = $this->getCookie($request)
            ->withExpires(-1);
        return $response->withAddedHeader('Set-Cookie', $cookie->__toString());
    }

    private function getCookie(ServerRequestInterface $request): Cookie
    {
        $normalizedParams = $request->getAttribute('normalizedParams');
        if (!$normalizedParams instanceof NormalizedParams) {
            throw new \Exception('Could not retrieve normalized params from request.', 1664357339);
        }

        $days = 7;

        return new Cookie(
            $this->cookieSession->getCookieName(),
            $this->cookieSession->getCookieValue(),
            $GLOBALS['EXEC_TIME'] + 24 * 60 * 60 * $days,
            $normalizedParams->getSitePath(),
            '',
            false,
            false,
            false,
            Cookie::SAMESITE_STRICT
        );
    }
}
