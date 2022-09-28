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

namespace WerkraumMedia\Watchlist\Tests\Acceptance;

use WerkraumMedia\Watchlist\Tests\Acceptance\Support\AcceptanceTester;

class FormIntegrationCest
{
    public function canSelectOneOfTwoSubmittedItemsFromWatchlist(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->click('button[data-watchlist-item="page-1"]');
        $I->click('button[data-watchlist-item="page-2"]');

        $I->amOnPage('/page-2');
        $I->checkOption('#test-1-watchlist-1-1');
        $I->click('Next step');

        $I->see('Page: Page 2 Title');
        $I->dontSee('Page: Page Title');
        $I->click('Submit');
    }

    public function watchlistItemsInFormAreNotCached(AcceptanceTester $I): void
    {
        $this->canSelectOneOfTwoSubmittedItemsFromWatchlist($I);

        $I->amOnPage('/');
        $I->click('button[data-watchlist-item="page-1"]');

        $I->amOnPage('/page-2');
        $I->see('Form: Page 2 Title');
        $I->dontSee('Form: Page Title');
    }
}
