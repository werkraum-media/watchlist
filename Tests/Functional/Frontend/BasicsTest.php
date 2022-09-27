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

namespace WerkraumMedia\Watchlist\Tests\Functional\Frontend;

use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalResponse;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \WerkraumMedia\Watchlist\Frontend\Basics
 */
class BasicsTest extends FunctionalTestCase
{
    protected $coreExtensionsToLoad = [
        'fluid_styled_content',
    ];

    protected $testExtensionsToLoad = [
        'typo3conf/ext/watchlist',
    ];

    protected $pathsToProvideInTestInstance = [
        'typo3conf/ext/watchlist/Tests/Functional/Frontend/Fixtures/Sites/' => 'typo3conf/sites/',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpBackendUserFromFixture(1);

        $this->importCSVDataSet(__DIR__ . '/Fixtures/BasicDatabase.csv');
        $this->setUpFrontendRootPage(1, [
            'EXT:watchlist/Tests/Functional/Frontend/Fixtures/FrontendRendering.typoscript',
            'EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript',
        ]);
    }

    /**
     * @test
     */
    public function watchlistIsRenderedAsEmptyByDefault(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $result = $this->executeFrontendRequest($request);

        self::assertSame(200, $result->getStatusCode());
        self::assertStringContainsString('Watchlist is empty', $result->getBody()->__toString());
    }

    /**
     * @test
     */
    public function canStorePagesOnWatchlistAccrossPageCalls(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_watchlist_watchlist[redirectUri]', $request->getUri()->__toString());
        $request = $request->withQueryParameter('tx_watchlist_watchlist[action]', 'add');
        $request = $request->withQueryParameter('tx_watchlist_watchlist[item]', 'page-1');
        $result = $this->executeFrontendRequest($request);

        self::assertIsRedirect('http://localhost/?id=1', $result);
        self::assertHasCookie('fe_typo_user', $result);

        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withHeader('Cookie', self::getCookies($result));
        $result = $this->executeFrontendRequest($request);

        self::assertStringContainsString('Page Title', $result->getBody()->__toString());
    }

    /**
     * @test
     */
    public function canRemoveStoredEntryFromWatchlist(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_watchlist_watchlist[redirectUri]', $request->getUri()->__toString());
        $request = $request->withQueryParameter('tx_watchlist_watchlist[action]', 'add');
        $request = $request->withQueryParameter('tx_watchlist_watchlist[item]', 'page-1');
        $result = $this->executeFrontendRequest($request);

        $cookies = self::getCookies($result);

        $request = new InternalRequest();
        $request = $request->withHeader('Cookie', $cookies);
        $request = $request->withPageId(1);
        $request = $request->withQueryParameter('tx_watchlist_watchlist[redirectUri]', $request->getUri()->__toString());
        $request = $request->withQueryParameter('tx_watchlist_watchlist[action]', 'remove');
        $request = $request->withQueryParameter('tx_watchlist_watchlist[item]', 'page-1');
        $result = $this->executeFrontendRequest($request);

        self::assertIsRedirect('http://localhost/?id=1', $result);

        $request = new InternalRequest();
        $request = $request->withHeader('Cookie', $cookies);
        $request = $request->withPageId(1);
        $result = $this->executeFrontendRequest($request);

        self::assertSame(200, $result->getStatusCode());
        self::assertStringContainsString('Watchlist is empty', $result->getBody()->__toString());
    }

    private static function assertIsRedirect(string $redirectLocation, InternalResponse $result): void
    {
        self::assertSame(303, $result->getStatusCode());
        self::assertSame($redirectLocation, $result->getHeader('location')[0] ?? '');
    }

    private static function assertHasCookie(string $cookieName, InternalResponse $result): void
    {
        self::assertStringContainsString($cookieName . '=', self::getCookies($result));
    }

    private static function getCookies(InternalResponse $result): string
    {
        return explode(' ', $result->getHeader('Set-Cookie')[0] ?? '', 2)[0] ?? '';
    }
}
