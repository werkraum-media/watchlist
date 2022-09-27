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

namespace WerkraumMedia\Watchlist\Domain\Repository;

use WerkraumMedia\Watchlist\Domain\ItemHandlerRegistry;
use WerkraumMedia\Watchlist\Domain\Model\Item;

class ItemRepository
{
    private ItemHandlerRegistry $registry;

    public function __construct(
        ItemHandlerRegistry $registry
    ) {
        $this->registry = $registry;
    }

    public function getByUniqueIdentifier(string $uniqueIdentifier): ?Item
    {
        [$type, $identifier] = explode('-', $uniqueIdentifier, 2);

        if ($this->registry->exists($type)) {
            return $this->registry->get($type)->return($identifier);
        }

        throw new \Exception(
            sprintf(
                'No item handler for type "%s", only supported: %s.',
                $type,
                implode(', ', $this->registry->getSupportedTypes()),
            ),
            1664186139
        );
    }
}
