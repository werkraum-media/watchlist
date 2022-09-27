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

namespace WerkraumMedia\Watchlist\Domain;

use WerkraumMedia\Watchlist\Domain\Model\Item;

/**
 * Defines interface of concrete handler implementations per item type.
 *
 * Each item has a type, and each type has an handler.
 * The handler needs to take care of creating the item instances.
 */
interface ItemHandlerInterface
{
    /**
     * Returns an item instance based on its identifier.
     */
    public function return(string $identifier): ?Item;

    /**
     * Returns the type the handler can handle, e.g. "pages" or "tt_address".
     */
    public function handlesType(): string;
}
