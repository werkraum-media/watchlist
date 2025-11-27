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

namespace WerkraumMedia\Watchlist\Form\EventListener;

use TYPO3\CMS\Form\Domain\Model\Renderable\RootRenderableInterface;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;
use TYPO3\CMS\Form\Event\BeforeRenderableIsRenderedEvent;
use WerkraumMedia\Watchlist\Form\FormElements\WatchlistFormElement;
use WerkraumMedia\Watchlist\Session\SessionServiceInterface;

/**
 * Renderables are instantiated with makeInstance and constructor arguments.
 * We therefore have no chance for DI.
 *
 * That's why we use the hook as this seems like the best workaround.
 * He will use the inject* method, so we can remove the hook once DI should work.
 */
final class BeforeRenderingEventListener
{
    private SessionServiceInterface $sessionService;

    public function __construct(
        SessionServiceInterface $sessionService
    ) {
        $this->sessionService = $sessionService;
    }

    public function __invoke(BeforeRenderableIsRenderedEvent $event): void
    {
        if (!$event->renderable instanceof WatchlistFormElement) {
            return;
        }

        $event->renderable->injectSessionService($this->sessionService);
    }

    /**
     * TODO: typo3/cms-core:^15.0 Remove legacy implementation for v13
     *
     * TYPO3 drop the Hook without a deprecation phase and replace it with an EventListener.
     * (see: https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Breaking-107569-RemovedBeforeRenderingHook.html#breaking-107569-removed-beforerendering-hook)ph
     */
    public function beforeRendering(
        FormRuntime $formRuntime,
        RootRenderableInterface $renderable
    ): void {
        if (!$renderable instanceof WatchlistFormElement) {
            return;
        }

        $renderable->injectSessionService($this->sessionService);
    }

    /**
     * TODO: typo3/cms-core:^15.0 Remove legacy implementation for v13
     *
     * TYPO3 drop the Hook without a deprecation phase and replace it with an EventListener.
     * (see: https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/14.0/Breaking-107569-RemovedBeforeRenderingHook.html#breaking-107569-removed-beforerendering-hook)
     */
    public static function register(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form']['beforeRendering'][self::class] = self::class;
    }
}
