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

namespace WerkraumMedia\Watchlist\Form\Hook;

use TYPO3\CMS\Form\Domain\Model\Renderable\RootRenderableInterface;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;
use WerkraumMedia\Watchlist\Form\FormElements\WatchlistFormElement;
use WerkraumMedia\Watchlist\Session\SessionServiceInterface;

/**
 * Renderables are instantiated with makeInstance and constructor arguments.
 * We therefore have no chance for DI.
 *
 * That's why we use the hook as this seems like the best workaround.
 * He will use the inject* method, so we can remove the hook once DI should work.
 */
final class BeforeRenderingHook
{
    private SessionServiceInterface $sessionService;

    public function __construct(
        SessionServiceInterface $sessionService
    ) {
        $this->sessionService = $sessionService;
    }

    public function beforeRendering(
        FormRuntime $formRuntime,
        RootRenderableInterface $renderable
    ): void {
        if (!$renderable instanceof WatchlistFormElement) {
            return;
        }

        $renderable->injectSessionService($this->sessionService);
    }

    public static function register(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form']['beforeRendering'][self::class] = self::class;
    }
}
