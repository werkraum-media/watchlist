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

namespace WerkraumMedia\Watchlist\Form\FormElements;

use RuntimeException;
use TYPO3\CMS\Form\Domain\Model\FormElements\AbstractFormElement;
use TYPO3\CMS\Frontend\Cache\CacheInstruction;
use WerkraumMedia\Watchlist\Domain\Model\Watchlist;
use WerkraumMedia\Watchlist\Session\SessionServiceInterface;

final class WatchlistFormElement extends AbstractFormElement
{
    private SessionServiceInterface $sessionService;

    public function injectSessionService(SessionServiceInterface $sessionService): void
    {
        $this->sessionService = $sessionService;
    }

    public function getWatchlist(): ?Watchlist
    {
        // Only trigger once they are actually used = this method is used.
        $cacheInstruction = $this->getCacheInstruction();

        $cacheInstruction->disableCache('Prevent watchlist items from being cached for users.');

        return $this->sessionService->getWatchlist('default');
    }

    private function getCacheInstruction(): CacheInstruction
    {
        $request = $this->getRequest();
        if (is_null($request)) {
            throw new RuntimeException('Could not get request.', 1747311155);
        }

        $cacheInstruction = $request->getAttribute('frontend.cache.instruction');
        if (($cacheInstruction instanceof CacheInstruction) === false) {
            throw new RuntimeException('Could not get cache instruction from request.', 1747313867);
        }
        return $cacheInstruction;
    }
}
