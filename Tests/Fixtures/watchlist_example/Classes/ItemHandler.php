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

namespace WerkraumMedia\WatchlistExample;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use WerkraumMedia\Watchlist\Domain\ItemHandlerInterface;
use WerkraumMedia\Watchlist\Domain\Model\Item;

class ItemHandler implements ItemHandlerInterface
{
    private ConnectionPool $connectionPool;

    private FileRepository $fileRepository;

    public function __construct(
        ConnectionPool $connectionPool,
        FileRepository $fileRepository
    ) {
        $this->connectionPool = $connectionPool;
        $this->fileRepository = $fileRepository;
    }

    public function return(string $identifier): ?Item
    {
        $pageUid = (int)$identifier;
        $pageRecord = $this->getPageRecord($pageUid);

        return new Page(
            $pageUid,
            (string)$pageRecord['title'],
            $this->getImage($pageUid)
        );
    }

    public function handlesType(): string
    {
        return 'page';
    }

    private function getPageRecord(int $uid): array
    {
        $qb = $this->connectionPool->getQueryBuilderForTable('pages');
        $qb->select('title');
        $qb->from('pages');
        $qb->where($qb->expr()->eq('uid', $qb->createNamedParameter($uid)));
        $qb->setMaxResults(1);
        return $qb->execute()->fetchAssociative() ?: [];
    }

    private function getImage(int $uid): ?FileReference
    {
        return $this->fileRepository->findByRelation('pages', 'media', $uid)[0] ?? null;
    }
}
