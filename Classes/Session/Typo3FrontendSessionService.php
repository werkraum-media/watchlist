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

namespace WerkraumMedia\Watchlist\Session;

use TYPO3\CMS\Extbase\Property\PropertyMapper;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use WerkraumMedia\Watchlist\Domain\Model\Item;
use WerkraumMedia\Watchlist\Domain\Model\Watchlist;

class Typo3FrontendSessionService implements SessionServiceInterface
{
    private PropertyMapper $propertyMapper;

    public function __construct(
        PropertyMapper $propertyMapper
    ) {
        $this->propertyMapper = $propertyMapper;
    }

    public function getWatchlist(string $identifier): ?Watchlist
    {
        $items = $this->getTsfe()->fe_user->getSessionData('watchlist-' . $identifier) ?: [];
        if ($items === [] || is_array($items) === false) {
            return null;
        }

        $watchlist = new Watchlist();

        array_map(function (string $item) use ($watchlist) {
            $mappedItem = $this->propertyMapper->convert($item, Item::class);
            if (!$mappedItem instanceof Item) {
                return;
            }

            $watchlist->addItem($mappedItem);
        }, $items);

        return $watchlist;
    }

    public function update(Watchlist $watchlist): void
    {
        $this->getTsfe()->fe_user->setAndSaveSessionData(
            'watchlist-' . $watchlist->getIdentifier(),
            array_map(fn (Item $item) => $item->getUniqueIdentifier(), $watchlist->getItems())
        );
    }
    private function getTsfe(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
