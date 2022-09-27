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

namespace WerkraumMedia\Watchlist\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use WerkraumMedia\Watchlist\Domain\Model\Item;
use WerkraumMedia\Watchlist\Domain\Model\Watchlist;
use WerkraumMedia\Watchlist\Domain\Repository\WatchlistRepository;

class WatchlistController extends ActionController
{
    private WatchlistRepository $repository;

    public function __construct(
        WatchlistRepository $repository
    ) {
        $this->repository = $repository;
    }

    protected function initializeAction(): void
    {
        if ($this->request->hasArgument('watchlist') === false) {
            $this->request->setArgument('watchlist', 'default');
        }
    }

    public function indexAction(
        Watchlist $watchlist
    ): ResponseInterface {
        $this->view->assignMultiple([
            'watchlist' => $watchlist,
        ]);

        return $this->htmlResponse();
    }

    public function addAction(
        Watchlist $watchlist,
        Item $item,
        string $redirectUri = ''
    ): void {
        $watchlist->addItem($item);

        $this->repository->update($watchlist);

        throw new PropagateResponseException(new RedirectResponse($redirectUri, 303), 1664189968);
    }

    public function removeAction(
        Watchlist $watchlist,
        Item $item,
        string $redirectUri
    ): void {
        $watchlist->removeItem($item);

        $this->repository->update($watchlist);

        throw new PropagateResponseException(new RedirectResponse($redirectUri, 303), 1664189969);
    }
}
