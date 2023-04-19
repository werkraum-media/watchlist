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

namespace WerkraumMedia\Watchlist\Tests\Functional;

use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Cookie;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class BasicsTest extends FunctionalTestCase
{
    protected function setUp(): void
    {
        $this->coreExtensionsToLoad = [
            'typo3/cms-fluid-styled-content',
            'typo3/cms-form',
        ];

        $this->testExtensionsToLoad = [
            'werkraummedia/watchlist',
            'typo3conf/ext/watchlist/Tests/Fixtures/watchlist_example',
        ];

        $this->pathsToLinkInTestInstance = [
            'typo3conf/ext/watchlist/Tests/Fixtures/Sites' => 'typo3conf/sites',
            'typo3conf/ext/watchlist/Tests/Fixtures/Fileadmin/Files' => 'fileadmin/Files',
        ];

        $this->configurationToUseInTestInstance = [
            'FE' => [
                'cacheHash' => [
                    'excludedParameters' => [
                        '^tx_watchlist_watchlist[',
                    ],
                ],
            ],
        ];

        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/BasicDatabase.csv');
    }

    #[Test]
    public function watchlistIsRenderedAsEmptyByDefault(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        self::assertStringContainsString('Watchlist is empty', $result->getBody()->__toString());
    }

    #[Test]
    public function canStorePagesOnWatchlistAccrossPageCalls(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_watchlist_watchlist[redirectUri]', $request->getUri()->__toString());
        $request = $request->withQueryParameter('tx_watchlist_watchlist[action]', 'add');
        $request = $request->withQueryParameter('tx_watchlist_watchlist[item]', 'page-1');
        $result = $this->executeFrontendSubRequest($request);

        self::assertIsRedirect('http://localhost/?id=1', $result);
        self::assertCookie('page-1', $this->getCookie($result));

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withCookieParams([
            'watchlist' => 'page-1',
        ]);
        // $request = $request->withHeader('Cookie', 'watchlist=page-1');
        $result = $this->executeFrontendSubRequest($request);

        self::assertMatchesRegularExpression('#<li>\s*Page Title#', $result->getBody()->__toString());
        self::assertStringContainsString('<img src="/fileadmin/Files/FirstResult.png" width="" height="" alt="" />', $result->getBody()->__toString());
    }

    #[Test]
    public function canRemoveStoredEntryFromWatchlist(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_watchlist_watchlist[redirectUri]', $request->getUri()->__toString());
        $request = $request->withQueryParameter('tx_watchlist_watchlist[action]', 'add');
        $request = $request->withQueryParameter('tx_watchlist_watchlist[item]', 'page-1');
        $result = $this->executeFrontendSubRequest($request);

        self::assertCookie('page-1', $this->getCookie($result));

        $request = new InternalRequest();
        $request = $request->withCookieParams([
            'watchlist' => 'page-1',
        ]);
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_watchlist_watchlist[redirectUri]', $request->getUri()->__toString());
        $request = $request->withQueryParameter('tx_watchlist_watchlist[action]', 'remove');
        $request = $request->withQueryParameter('tx_watchlist_watchlist[item]', 'page-1');
        $result = $this->executeFrontendSubRequest($request);

        self::assertIsRedirect('http://localhost/?id=1', $result);
        $cookie = $this->getCookie($result);
        self::assertInstanceOf(Cookie::class, $cookie);
        self::assertLessThan(time(), $cookie->getExpiresTime());

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $result = $this->executeFrontendSubRequest($request);

        self::assertSame(200, $result->getStatusCode());
        self::assertStringContainsString('Watchlist is empty', $result->getBody()->__toString());
    }

    private static function assertIsRedirect(string $redirectLocation, ResponseInterface $result): void
    {
        self::assertSame(303, $result->getStatusCode());
        self::assertSame($redirectLocation, $result->getHeader('location')[0] ?? '');
    }

    private static function assertCookie(string $value, ?Cookie $cookie): void
    {
        self::assertInstanceOf(Cookie::class, $cookie);
        self::assertSame('watchlist', $cookie->getName());
        self::assertSame('page-1', $cookie->getValue());
        self::assertNull($cookie->getDomain());
        self::assertSame('/', $cookie->getPath());
        self::assertSame('strict', $cookie->getSameSite());
        self::assertFalse($cookie->isSecure());
    }

    private function getCookie(ResponseInterface $result): ?Cookie
    {
        $cookie = $result->getHeader('Set-Cookie')[0] ?? '';
        if ($cookie === '') {
            return null;
        }

        return Cookie::fromString($cookie);
    }
}
