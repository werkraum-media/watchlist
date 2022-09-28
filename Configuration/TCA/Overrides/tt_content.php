<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die();

(static function (string $extKey, string $table, string $contentType) {
    $languagePath = implode(':', [
        'LLL',
        'EXT',
        $extKey . '/Resources/Private/Language/locallang.xlf',
        $table . '.' . $contentType . '.',
    ]);

    ExtensionUtility::registerPlugin(
        $extKey,
        $extKey,
        $languagePath . 'title',
        'EXT:' . $extKey . '/Resources/Public/Icons/Extension.svg'
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
        $table,
        'CType',
        [
            $languagePath . 'title',
            $contentType,
            'content-bullets',
            'special',
        ]
    );

    \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA'][$table], [
        'ctrl' => [
            'typeicon_classes' => [
                $contentType => 'content-bullets',
            ],
        ],
        'types' => [
            $contentType => [
                'showitem' => implode(',', [
                    '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general',
                        '--palette--;;general',
                        '--palette--;;headers',
                    '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance',
                        '--palette--;;frames',
                        '--palette--;;appearanceLinks',
                    '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language',
                        '--palette--;;language',
                    '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access',
                        '--palette--;;hidden',
                        '--palette--;;access',
                    '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories',
                    '--div--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_category.tabs.category',
                        'categories',
                    '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes',
                        'rowDescription',
                    '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended',
                ]),
            ],
        ],
    ]);
})('watchlist', 'tt_content', 'watchlist_watchlist');
