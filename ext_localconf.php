<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use WerkraumMedia\Watchlist\Controller\WatchlistController;
use WerkraumMedia\Watchlist\Form\Hook\BeforeRenderingHook;

defined('TYPO3') || die('Access denied.');

(static function (): void {
    ExtensionUtility::configurePlugin(
        'Watchlist',
        'Watchlist',
        [
            WatchlistController::class => 'index, add, remove',
        ],
        [
            WatchlistController::class => 'index',
        ],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    BeforeRenderingHook::register();
})();
