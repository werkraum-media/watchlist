<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die();

ExtensionUtility::registerPlugin(
    'Watchlist',
    'Watchlist',
    'LLL:EXT:watchlist/Resources/Private/Language/locallang.xlf:plugin.watchlist',
    'EXT:watchlist/Resources/Public/Icons/Extension.svg'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['watchlist_watchlist'] = 'recursive,select_key,pages';
