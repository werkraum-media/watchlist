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

namespace WerkraumMedia\Watchlist\Extbase\TypeConverter;

use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;
use WerkraumMedia\Watchlist\Domain\Model\Item;
use WerkraumMedia\Watchlist\Domain\Repository\ItemRepository;

class ItemTypeConverter extends AbstractTypeConverter
{
    protected array $sourceTypes = ['string'];

    protected string $targetType = Item::class;

    protected int $priority = 10;

    private ItemRepository $itemRepository;

    public function __construct(
        ItemRepository $itemRepository
    ) {
        $this->itemRepository = $itemRepository;
    }

    public function convertFrom(
        $source,
        string $targetType,
        array $convertedChildProperties = [],
        ?PropertyMappingConfigurationInterface $configuration = null
    ): ?Item {
        if (is_string($source) === false) {
            throw new \InvalidArgumentException('Source was not of expected type string.', 1664197305);
        }

        return $this->itemRepository->getByUniqueIdentifier($source);
    }
}
