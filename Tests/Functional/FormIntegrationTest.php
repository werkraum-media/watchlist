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

use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class FormIntegrationTest extends FunctionalTestCase
{
    protected $coreExtensionsToLoad = [
        'fluid_styled_content',
        'form',
        'tstemplate',
    ];

    protected $testExtensionsToLoad = [
        'typo3conf/ext/watchlist',
        'typo3conf/ext/watchlist/Tests/Fixtures/watchlist_example',
    ];

    protected $pathsToLinkInTestInstance = [
        'typo3conf/ext/watchlist/Tests/Fixtures/Sites' => 'typo3conf/sites',
        'typo3conf/ext/watchlist/Tests/Fixtures/Fileadmin/Files' => 'fileadmin/Files',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/BasicDatabase.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/FormDatabase.csv');
    }

    /**
     * @test
     */
    public function rendersWatchlistItemsIntoForm(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(2);
        $request = $request->withHeader('Cookie', 'watchlist=page-1,page-2');
        $result = $this->executeFrontendRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertSame(2, substr_count($html, 'form-group'));
        self::assertStringContainsString('<span>Form: Page Title</span>', $html);
        self::assertStringContainsString('value="Page: Page Title"', $html);
        self::assertStringContainsString('<span>Form: Page 2 Title</span>', $html);
        self::assertStringContainsString('value="Page: Page 2 Title"', $html);
    }

    /**
     * @test
     */
    public function doesntRenderFormElementForZeroItems(): void
    {
        $request = new InternalRequest();
        $request = $request->withPageId(2);
        $result = $this->executeFrontendRequest($request);

        self::assertSame(200, $result->getStatusCode());
        $html = $result->getBody()->__toString();
        self::assertSame(1, substr_count($html, 'form-group'));
        self::assertStringNotContainsString('checkbox', $html);
        self::assertStringNotContainsString('Watchlist', $html);
    }
}
