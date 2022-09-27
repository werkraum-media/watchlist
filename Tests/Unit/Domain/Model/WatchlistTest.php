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

namespace WerkraumMedia\Watchlist\Tests\Unit\Domain\Model;

use PHPUnit\Framework\TestCase;
use WerkraumMedia\Watchlist\Domain\Items\Page\Page;
use WerkraumMedia\Watchlist\Domain\Model\Watchlist;

/**
 * @covers \WerkraumMedia\Watchlist\Domain\Model\Watchlist
 * @testdox The Watchlist
 */
class WatchlistTest extends TestCase
{
    /**
     * @test
     */
    public function canBeCreated(): void
    {
        $subject = new Watchlist();

        self::assertInstanceOf(
            Watchlist::class,
            $subject
        );
    }

    /**
     * @test
     */
    public function doesNotHaveItemsByDefault(): void
    {
        $subject = new Watchlist();

        self::assertCount(0, $subject->getItems());
    }

    /**
     * @test
     */
    public function canAddAItem(): void
    {
        $subject = new Watchlist();

        $thing = new Page(1, 'test');
        $subject->addItem($thing);

        self::assertCount(1, $subject->getItems());
        self::assertSame($thing, $subject->getItems()[0]);
    }

    /**
     * @test
     */
    public function canRemoveAItem(): void
    {
        $subject = new Watchlist();

        $thing = new Page(1, 'test');
        $subject->addItem($thing);

        $subject->removeItem($thing);

        self::assertCount(0, $subject->getItems());
    }
}
