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

class JavaScriptCest
{
    private const SELECTOR_COUNTER = '.watchlist-badge-counter';

    public function counterShowsZeroByDefault(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->see('Watchlist is empty');
        $I->see('0', self::SELECTOR_COUNTER);
    }

    public function canAddItem(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->see('Watchlist is empty');

        $I->see('Page 1 zur Merkliste hinzufügen');
        $I->dontSee('Page 1 von Merkliste entfernen');
        $I->click('button[data-watchlist-item="page-1"]');

        $I->dontSee('Page 1 zur Merkliste hinzufügen');
        $I->see('Page 1 von Merkliste entfernen');
        $I->see('1', self::SELECTOR_COUNTER);
    }

    public function canRemoveItem(AcceptanceTester $I): void
    {
        $this->canAddItem($I);

        $I->click('button[data-watchlist-item="page-1"]');

        $I->see('Page 1 zur Merkliste hinzufügen');
        $I->dontSee('Page 1 von Merkliste entfernen');
        $I->see('0', self::SELECTOR_COUNTER);
    }

    public function canAddTwoItems(AcceptanceTester $I): void
    {
        $this->canAddItem($I);

        $I->click('button[data-watchlist-item="page-2"]');
        $I->dontSee('Page 2 zur Merkliste hinzufügen');
        $I->see('Page 2 von Merkliste entfernen');
        $I->see('2', self::SELECTOR_COUNTER);
    }

    public function canRemoveFirstItem(AcceptanceTester $I): void
    {
        $this->canAddTwoItems($I);

        $I->click('button[data-watchlist-item="page-1"]');
        $I->see('Page 1 zur Merkliste hinzufügen');
        $I->see('Page 2 von Merkliste entfernen');
        $I->dontSee('Page 2 zur Merkliste hinzufügen');
        $I->see('1', self::SELECTOR_COUNTER);
    }

    public function keepsStateOnReload(AcceptanceTester $I): void
    {
        $this->canAddItem($I);

        $I->amOnPage('/');

        $I->dontSee('Page 1 zur Merkliste hinzufügen');
        $I->see('Page 1 von Merkliste entfernen');
        $I->see('1', self::SELECTOR_COUNTER);
    }
}
