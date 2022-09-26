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

namespace WerkraumMedia\Watchlist\Domain\Model;

class Watchlist
{
    /**
     * @var Item[]
     */
    private array $items = [];

    public function getIdentifier(): string
    {
        return 'default';
    }

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return array_values($this->items);
    }

    public function addItem(Item $item): void
    {
        $this->items[$item->getUniqueIdentifier()] = $item;
    }

    public function removeItem(Item $item): void
    {
        unset($this->items[$item->getUniqueIdentifier()]);
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }
}
