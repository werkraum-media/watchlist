<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use WerkraumMedia\Watchlist\Controller\WatchlistController;
use WerkraumMedia\Watchlist\Extbase\TypeConverter\ItemTypeConverter;
use WerkraumMedia\Watchlist\Extbase\TypeConverter\WatchlistTypeConverter;

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

    ExtensionUtility::registerTypeConverter(WatchlistTypeConverter::class);
    ExtensionUtility::registerTypeConverter(ItemTypeConverter::class);
    ExtensionManagementUtility::addPageTSConfig(
        "@import 'EXT:watchlist/Configuration/TSconfig/Page/Default.tsconfig'"
    );
})();
